<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionPolicyTest extends TestCase
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

    public function test_admin_can_view_any_form_submissions(): void
    {
        $this->assertTrue($this->admin->can('viewAny', FormSubmission::class));
    }

    public function test_subscriber_cannot_view_any_form_submissions(): void
    {
        $this->assertFalse($this->subscriber->can('viewAny', FormSubmission::class));
    }

    public function test_admin_can_view_specific_form_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->assertTrue($this->admin->can('view', $submission));
    }

    public function test_subscriber_cannot_view_specific_form_submission(): void
    {
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->assertFalse($this->subscriber->can('view', $submission));
    }

    public function test_anyone_can_create_form_submissions(): void
    {
        $this->assertTrue($this->admin->can('create', FormSubmission::class));
        $this->assertTrue($this->subscriber->can('create', FormSubmission::class));
    }

    public function test_guest_can_create_form_submissions(): void
    {
        // Test without authenticated user
        $response = $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'message' => 'Test message from guest',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('form_submissions', [
            'form_type' => 'contact',
            'data->email' => 'guest@example.com',
        ]);
    }
}
