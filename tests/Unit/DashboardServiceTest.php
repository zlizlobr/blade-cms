<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Dashboard\Services\DashboardService;
use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Test Tenant',
                'en' => 'Test Tenant',
            ],
            'slug' => 'test-tenant',
        ]);

        app()->instance('tenant.id', $this->tenant->id);

        $this->service = DashboardService::create();
    }

    public function test_can_create_service_instance_with_factory_method(): void
    {
        $service = DashboardService::create();

        $this->assertInstanceOf(DashboardService::class, $service);
    }

    public function test_returns_statistics_array_with_correct_keys(): void
    {
        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('totalSubmissions', $stats);
        $this->assertArrayHasKey('submissionsThisWeek', $stats);
        $this->assertArrayHasKey('activeUsers', $stats);
        $this->assertArrayHasKey('recentSubmissions', $stats);
    }

    public function test_counts_total_submissions_correctly(): void
    {
        FormSubmission::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertEquals(5, $stats['totalSubmissions']);
    }

    public function test_counts_submissions_this_week_correctly(): void
    {
        // Create old submission
        FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now()->subWeeks(2),
        ]);

        // Create submissions this week
        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now()->startOfWeek()->addDays(1),
        ]);

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertEquals(4, $stats['totalSubmissions']);
        $this->assertEquals(3, $stats['submissionsThisWeek']);
    }

    public function test_counts_active_users_correctly(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $user1->tenants()->attach($this->tenant->id);
        $user2->tenants()->attach($this->tenant->id);
        // user3 not attached to tenant

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertEquals(2, $stats['activeUsers']);
    }

    public function test_returns_recent_submissions_limited_to_five(): void
    {
        FormSubmission::factory()->count(10)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertCount(5, $stats['recentSubmissions']);
    }

    public function test_recent_submissions_are_ordered_by_latest(): void
    {
        $oldest = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now()->subDays(5),
        ]);

        $newest = FormSubmission::factory()->create([
            'tenant_id' => $this->tenant->id,
            'created_at' => now(),
        ]);

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertEquals($newest->id, $stats['recentSubmissions']->first()->id);
        $this->assertEquals($oldest->id, $stats['recentSubmissions']->last()->id);
    }

    public function test_statistics_only_include_tenant_data(): void
    {
        $otherTenant = Tenant::create([
            'name' => [
                'cs' => 'Other Tenant',
                'en' => 'Other Tenant',
            ],
            'slug' => 'other-tenant',
        ]);

        // Create submissions for current tenant
        FormSubmission::factory()->count(3)->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Create submissions for other tenant
        FormSubmission::factory()->count(7)->create([
            'tenant_id' => $otherTenant->id,
        ]);

        $stats = $this->service->getStatistics($this->tenant->id);

        $this->assertEquals(3, $stats['totalSubmissions']);
        $this->assertCount(3, $stats['recentSubmissions']);
    }
}
