<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Test complete user registration flow from registration to authorization.
 *
 * This test verifies:
 * 1. User registration form is accessible
 * 2. User can register with valid data
 * 3. User is created in database with correct attributes
 * 4. User is automatically logged in after registration
 * 5. Registered event is dispatched
 * 6. User has correct default role
 * 7. User can access appropriate routes based on role
 * 8. User cannot access admin routes without admin role
 */
class UserRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a default tenant for new users
        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Výchozí Tenant',
                'en' => 'Default Tenant',
            ],
            'slug' => 'default-tenant',
        ]);

        // Set tenant context
        app()->instance('tenant.id', $this->tenant->id);
    }

    /** @test */
    public function registration_form_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register');
    }

    /** @test */
    public function user_can_register_with_valid_data(): void
    {
        Event::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Registration should succeed and redirect
        $response->assertRedirect();

        // User should be created in database
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Registered event should be dispatched
        Event::assertDispatched(Registered::class, function ($event) {
            return $event->user->email === 'test@example.com';
        });
    }

    /** @test */
    public function registered_user_has_correct_default_role(): void
    {
        $this->post('/register', [
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'regular@example.com')->first();

        // New users should have SUBSCRIBER role by default (not ADMIN)
        $this->assertEquals(UserRole::SUBSCRIBER, $user->role);
    }

    /** @test */
    public function user_is_logged_in_after_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'Auto Login User',
            'email' => 'autologin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // User should be authenticated
        $this->assertAuthenticated();

        // The authenticated user should be the one we just created
        $this->assertEquals('autologin@example.com', auth()->user()->email);
    }

    /** @test */
    public function registered_user_cannot_access_admin_routes(): void
    {
        // Register a new user (default role: SUBSCRIBER)
        $this->post('/register', [
            'name' => 'Non Admin User',
            'email' => 'nonadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'nonadmin@example.com')->first();
        $user->tenants()->attach($this->tenant->id);
        $user->update(['current_tenant_id' => $this->tenant->id]);

        // Try to access admin dashboard
        $response = $this->actingAs($user)->get('/admin');

        // Should be forbidden (403) because user is not admin
        $response->assertStatus(403);
    }

    /** @test */
    public function registered_user_can_access_profile(): void
    {
        // Register a new user
        $this->post('/register', [
            'name' => 'Profile User',
            'email' => 'profile@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'profile@example.com')->first();
        $user->tenants()->attach($this->tenant->id);
        $user->update(['current_tenant_id' => $this->tenant->id]);

        // User should be able to access their profile
        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function registration_validates_required_fields(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function registration_validates_email_format(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function registration_validates_unique_email(): void
    {
        // Create existing user
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        // Try to register with same email
        $response = $this->post('/register', [
            'name' => 'Duplicate User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function registration_validates_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function registration_redirects_subscriber_to_appropriate_page(): void
    {
        $response = $this->post('/register', [
            'name' => 'Subscriber User',
            'email' => 'subscriber@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Check where it redirects
        // If redirect is to /admin, it will fail for non-admin users
        // Should redirect to a page accessible to all authenticated users

        // For now, just check that redirect happens
        $response->assertRedirect();

        // Follow the redirect and check if it's accessible
        $redirectResponse = $this->followingRedirects()->post('/register', [
            'name' => 'Another User',
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Should not be 403 (forbidden) or 500 (error)
        $this->assertNotEquals(403, $redirectResponse->status());
        $this->assertNotEquals(500, $redirectResponse->status());
    }

    /** @test */
    public function password_is_hashed_in_database(): void
    {
        $this->post('/register', [
            'name' => 'Hashed Password User',
            'email' => 'hashed@example.com',
            'password' => 'plain-password',
            'password_confirmation' => 'plain-password',
        ]);

        $user = User::where('email', 'hashed@example.com')->first();

        // Password should be hashed
        $this->assertNotEquals('plain-password', $user->password);

        // But it should match when checked with Hash
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }
}
