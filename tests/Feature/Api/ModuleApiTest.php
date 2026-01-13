<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModuleApiTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);
    }

    public function test_list_modules_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/modules');

        $response->assertStatus(401);
    }

    public function test_list_modules_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::SUBSCRIBER,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/v1/modules');

        $response->assertStatus(403);
    }

    public function test_can_list_all_modules(): void
    {
        Module::factory()->create([
            'name' => 'Test Module 1',
            'slug' => 'test-1',
            'status' => ModuleStatus::ACTIVE,
        ]);

        Module::factory()->create([
            'name' => 'Test Module 2',
            'slug' => 'test-2',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'version',
                        'status',
                        'description',
                        'core_compatibility',
                        'dependencies',
                        'is_active',
                        'is_inactive',
                        'is_installed',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_can_filter_modules_by_status(): void
    {
        Module::factory()->create([
            'slug' => 'active-module',
            'status' => ModuleStatus::ACTIVE,
        ]);

        Module::factory()->create([
            'slug' => 'inactive-module',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules?status=active');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', 'active-module');
    }

    public function test_can_get_specific_module(): void
    {
        $module = Module::factory()->create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.2.3',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules/test-module');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $module->id,
                    'name' => 'Test Module',
                    'slug' => 'test-module',
                    'version' => '1.2.3',
                ],
            ]);
    }

    public function test_returns_404_for_non_existent_module(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules/non-existent');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Module not found',
            ]);
    }

    public function test_can_install_module(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/modules', [
                'name' => 'New Module',
                'slug' => 'new-module',
                'version' => '1.0.0',
                'description' => 'A new test module',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'New Module',
                    'slug' => 'new-module',
                    'version' => '1.0.0',
                    'status' => 'installed',
                ],
            ]);

        $this->assertDatabaseHas('modules', [
            'slug' => 'new-module',
            'status' => ModuleStatus::INSTALLED->value,
        ]);
    }

    public function test_cannot_install_duplicate_module(): void
    {
        Module::factory()->create([
            'slug' => 'existing-module',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/modules', [
                'name' => 'Duplicate',
                'slug' => 'existing-module',
                'version' => '1.0.0',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('slug');
    }

    public function test_can_activate_module(): void
    {
        $module = Module::factory()->create([
            'slug' => 'test-activate',
            'status' => ModuleStatus::INSTALLED,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/modules/test-activate/activate');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'slug' => 'test-activate',
                    'status' => 'active',
                    'is_active' => true,
                ],
            ]);

        $this->assertDatabaseHas('modules', [
            'slug' => 'test-activate',
            'status' => ModuleStatus::ACTIVE->value,
        ]);
    }

    public function test_cannot_activate_module_with_missing_dependencies(): void
    {
        Module::factory()->create([
            'slug' => 'dependency',
            'status' => ModuleStatus::INACTIVE,
        ]);

        Module::factory()->create([
            'slug' => 'test-deps',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['dependency' => '^1.0'],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/modules/test-deps/activate');

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Cannot activate module',
            ]);
    }

    public function test_can_deactivate_module(): void
    {
        $module = Module::factory()->create([
            'slug' => 'test-deactivate',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/modules/test-deactivate/deactivate');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'slug' => 'test-deactivate',
                    'status' => 'inactive',
                    'is_inactive' => true,
                ],
            ]);

        $this->assertDatabaseHas('modules', [
            'slug' => 'test-deactivate',
            'status' => ModuleStatus::INACTIVE->value,
        ]);
    }

    public function test_can_check_if_module_can_be_activated(): void
    {
        Module::factory()->create([
            'slug' => 'test-check',
            'status' => ModuleStatus::INSTALLED,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules/test-check/can-activate');

        $response->assertStatus(200)
            ->assertJson([
                'can_activate' => true,
            ]);
    }

    public function test_can_check_if_module_can_be_deactivated(): void
    {
        Module::factory()->create([
            'slug' => 'test-check-deactivate',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules/test-check-deactivate/can-deactivate');

        $response->assertStatus(200)
            ->assertJson([
                'can_deactivate' => true,
            ]);
    }

    public function test_can_get_module_dependencies(): void
    {
        Module::factory()->create([
            'slug' => 'test-with-deps',
            'dependencies' => [
                'dep1' => '^1.0',
                'dep2' => '~2.3',
            ],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson('/api/v1/modules/test-with-deps/dependencies');

        $response->assertStatus(200)
            ->assertJson([
                'dependencies' => [
                    'dep1' => '^1.0',
                    'dep2' => '~2.3',
                ],
            ]);
    }
}
