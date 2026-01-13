<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use App\Domain\User\Enums\UserRole;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModuleUITest extends TestCase
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

    public function test_modules_index_page_is_accessible_for_admin(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.modules.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin::modules.index');
    }

    public function test_modules_index_displays_all_modules(): void
    {
        Module::factory()->create([
            'name' => 'Blog Module',
            'slug' => 'blog',
            'status' => ModuleStatus::ACTIVE,
        ]);

        Module::factory()->create([
            'name' => 'Shop Module',
            'slug' => 'shop',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.modules.index'));

        $response->assertStatus(200);
        $response->assertSee('Blog Module');
        $response->assertSee('Shop Module');
    }

    public function test_module_show_page_displays_module_details(): void
    {
        $module = Module::factory()->create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'description' => 'Test module description',
            'status' => ModuleStatus::INSTALLED,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->get(route('admin.modules.show', $module->slug));

        $response->assertStatus(200);
        $response->assertViewIs('admin::modules.show');
        $response->assertSee('Test Module');
        $response->assertSee('Test module description');
    }

    public function test_can_activate_installed_module(): void
    {
        $module = Module::factory()->create([
            'slug' => 'test-activate',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => null,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.modules.activate', $module->slug));

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'slug' => 'test-activate',
            'status' => ModuleStatus::ACTIVE->value,
        ]);
    }

    public function test_can_deactivate_active_module(): void
    {
        $module = Module::factory()->create([
            'slug' => 'test-deactivate',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.modules.deactivate', $module->slug));

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('modules', [
            'slug' => 'test-deactivate',
            'status' => ModuleStatus::INACTIVE->value,
        ]);
    }

    public function test_cannot_activate_module_with_missing_dependencies(): void
    {
        Module::factory()->create([
            'slug' => 'dependency',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $module = Module::factory()->create([
            'slug' => 'test-with-deps',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['dependency' => '^1.0'],
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.modules.activate', $module->slug));

        $response->assertRedirect(route('admin.modules.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('modules', [
            'slug' => 'test-with-deps',
            'status' => ModuleStatus::INSTALLED->value,
        ]);
    }

    public function test_modules_index_is_not_accessible_for_guests(): void
    {
        $response = $this->get(route('admin.modules.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_modules_index_is_not_accessible_for_non_admin_users(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::SUBSCRIBER,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.modules.index'));

        $response->assertForbidden();
    }
}
