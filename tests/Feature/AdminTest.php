<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    private User $admin;

    private User $subscriber;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tenant
        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Testovací Tenant',
                'en' => 'Test Tenant',
            ],
            'slug' => 'test-tenant',
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'current_tenant_id' => $this->tenant->id,
        ]);
        $this->admin->tenants()->attach($this->tenant->id);

        // Create subscriber user
        $this->subscriber = User::factory()->create([
            'role' => UserRole::SUBSCRIBER,
            'current_tenant_id' => $this->tenant->id,
        ]);
        $this->subscriber->tenants()->attach($this->tenant->id);

        // Set tenant context
        app()->instance('tenant.id', $this->tenant->id);
    }

    /** @test */
    public function admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::dashboard.index');
    }

    /** @test */
    public function subscriber_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->subscriber)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_view_submissions_list(): void
    {
        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::submissions.index');
        $response->assertViewHas('submissions');
    }

    /** @test */
    public function subscriber_cannot_view_submissions_list(): void
    {
        $response = $this->actingAs($this->subscriber)
            ->get(route('admin.submissions.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_view_submission_detail(): void
    {
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'form_type' => 'contact',
            'data' => [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'message' => 'Test message',
            ],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.show', $submission));

        $response->assertStatus(200);
        $response->assertViewIs('admin::submissions.show');
        $response->assertViewHas('submission');
        $response->assertSee('Test User');
        $response->assertSee('test@example.com');
    }

    /** @test */
    public function subscriber_cannot_view_submission_detail(): void
    {
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->subscriber)
            ->get(route('admin.submissions.show', $submission));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_view_submission_from_another_tenant(): void
    {
        // Create another tenant
        $otherTenant = Tenant::create([
            'name' => [
                'cs' => 'Jiný Tenant',
                'en' => 'Other Tenant',
            ],
            'slug' => 'other-tenant',
        ]);

        // Create submission for other tenant
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.show', $submission));

        // Submission from another tenant returns 404 because of route model binding with tenant scope
        $response->assertStatus(404);
    }

    /** @test */
    public function admin_dashboard_shows_correct_statistics(): void
    {
        // Create submissions (3 this week from Monday, 2 older)
        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now()->startOfWeek()->addDays(2), // Wednesday this week
        ]);

        FormSubmission::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now()->subWeeks(2), // 2 weeks ago
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalSubmissions', 5);
        $response->assertViewHas('submissionsThisWeek', 3);
        $response->assertSee('5'); // Total submissions displayed
    }

    /** @test */
    public function admin_can_search_submissions(): void
    {
        FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'data' => ['name' => 'John Doe', 'email' => 'john@example.com', 'message' => 'Test'],
        ]);

        FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'data' => ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'message' => 'Test'],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.index', ['search' => 'john@example.com']));

        $response->assertStatus(200);
        $response->assertSee('john@example.com');
        $response->assertDontSee('jane@example.com');
    }

    /** @test */
    public function admin_can_filter_submissions_by_type(): void
    {
        FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'form_type' => 'contact',
        ]);

        FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'form_type' => 'newsletter',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.index', ['form_type' => 'contact']));

        $response->assertStatus(200);
        $response->assertViewHas('submissions', function ($submissions) {
            return $submissions->count() === 1
                && $submissions->first()->form_type === 'contact';
        });
    }

    /** @test */
    public function admin_only_sees_submissions_from_their_tenant(): void
    {
        // Create another tenant with submissions
        $otherTenant = Tenant::create([
            'name' => [
                'cs' => 'Jiný Tenant',
                'en' => 'Other Tenant',
            ],
            'slug' => 'other-tenant',
        ]);

        FormSubmission::factory()->count(2)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('submissions', function ($submissions) {
            return $submissions->count() === 2;
        });
    }
}
