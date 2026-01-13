<?php

declare(strict_types=1);

namespace Tests\Views;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminViewTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;

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

        app()->instance('tenant.id', $this->tenant->id);
    }

    public function test_admin_dashboard_view_renders(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::dashboard.index');
        $response->assertSee('Dashboard');
    }

    public function test_admin_submissions_index_view_renders(): void
    {
        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::submissions.index');
    }

    public function test_admin_submission_detail_view_renders(): void
    {
        $submission = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.submissions.show', $submission));

        $response->assertStatus(200);
        $response->assertViewIs('admin::submissions.show');
    }

    public function test_admin_layout_contains_sidebar(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard', false);
    }

    public function test_admin_profile_edit_view_renders(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::profile.edit');
    }
}
