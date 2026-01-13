<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Events\ModuleActivated;
use App\Domain\Module\Events\ModuleDeactivated;
use App\Domain\Module\Events\ModuleInstalled;
use App\Domain\Module\Events\ModuleUninstalled;
use App\Domain\Module\Exceptions\CircularDependencyException;
use App\Domain\Module\Exceptions\ModuleDependencyException;
use App\Domain\Module\Exceptions\ModuleException;
use App\Domain\Module\Exceptions\ModuleNotFoundException;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Services\ModuleServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ModuleManagementTest extends TestCase
{
    use RefreshDatabase;

    private ModuleServiceInterface $moduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleService = app(ModuleServiceInterface::class);
    }

    /** @test */
    public function it_installs_a_new_module(): void
    {
        Event::fake();

        $metadata = [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
            'description' => 'A test module',
            'core_compatibility' => '^1.0',
            'dependencies' => [],
        ];

        $module = $this->moduleService->install('test-module', $metadata);

        $this->assertInstanceOf(Module::class, $module);
        $this->assertEquals('Test Module', $module->name);
        $this->assertEquals('test-module', $module->slug);
        $this->assertEquals('1.0.0', $module->version);
        $this->assertEquals(ModuleStatus::INSTALLED, $module->status);
        $this->assertNotNull($module->installed_at);

        Event::assertDispatched(ModuleInstalled::class);
    }

    /** @test */
    public function it_throws_exception_when_installing_duplicate_module(): void
    {
        $metadata = [
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
        ];

        $this->moduleService->install('test-module', $metadata);

        $this->expectException(ModuleException::class);
        $this->expectExceptionMessage('already installed');

        $this->moduleService->install('test-module', $metadata);
    }

    /** @test */
    public function it_activates_an_installed_module(): void
    {
        Event::fake();

        $module = Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
        ]);

        $activatedModule = $this->moduleService->activate('test-module');

        $this->assertEquals(ModuleStatus::ACTIVE, $activatedModule->status);
        $this->assertNotNull($activatedModule->enabled_at);

        Event::assertDispatched(ModuleActivated::class);
    }

    /** @test */
    public function it_throws_exception_when_activating_module_with_missing_dependencies(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        $this->expectException(ModuleDependencyException::class);
        $this->expectExceptionMessage('unsatisfied dependencies');

        $this->moduleService->activate('module-a');
    }

    /** @test */
    public function it_throws_exception_when_activating_module_with_circular_dependency(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-a' => '^1.0'],
        ]);

        $this->expectException(ModuleException::class);
        $this->expectExceptionMessage('circular dependency');

        $this->moduleService->activate('module-a');
    }

    /** @test */
    public function it_deactivates_an_active_module(): void
    {
        Event::fake();

        $module = Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
            'enabled_at' => now(),
        ]);

        $deactivatedModule = $this->moduleService->deactivate('test-module');

        $this->assertEquals(ModuleStatus::INACTIVE, $deactivatedModule->status);
        $this->assertNull($deactivatedModule->enabled_at);

        Event::assertDispatched(ModuleDeactivated::class);
    }

    /** @test */
    public function it_throws_exception_when_deactivating_module_with_active_dependents(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $this->expectException(ModuleException::class);
        $this->expectExceptionMessage('active modules depend on it');

        $this->moduleService->deactivate('module-b');
    }

    /** @test */
    public function it_uninstalls_an_inactive_module(): void
    {
        Event::fake();

        Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $result = $this->moduleService->uninstall('test-module');

        $this->assertTrue($result);
        $this->assertDatabaseMissing('modules', ['slug' => 'test-module']);

        Event::assertDispatched(ModuleUninstalled::class);
    }

    /** @test */
    public function it_throws_exception_when_uninstalling_active_module(): void
    {
        Module::create([
            'name' => 'Test Module',
            'slug' => 'test-module',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $this->expectException(ModuleException::class);
        $this->expectExceptionMessage('must be deactivated before uninstalling');

        $this->moduleService->uninstall('test-module');
    }

    /** @test */
    public function it_throws_exception_when_module_not_found(): void
    {
        $this->expectException(ModuleNotFoundException::class);
        $this->expectExceptionMessage('not found');

        $this->moduleService->activate('non-existent-module');
    }

    /** @test */
    public function it_gets_all_active_modules(): void
    {
        Module::create([
            'name' => 'Active Module 1',
            'slug' => 'active-1',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        Module::create([
            'name' => 'Active Module 2',
            'slug' => 'active-2',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        Module::create([
            'name' => 'Inactive Module',
            'slug' => 'inactive-1',
            'version' => '1.0.0',
            'status' => ModuleStatus::INACTIVE,
        ]);

        $activeModules = $this->moduleService->getActiveModules();

        $this->assertCount(2, $activeModules);
        $this->assertTrue($activeModules->contains('slug', 'active-1'));
        $this->assertTrue($activeModules->contains('slug', 'active-2'));
        $this->assertFalse($activeModules->contains('slug', 'inactive-1'));
    }

    /** @test */
    public function it_checks_if_module_can_be_activated(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $this->assertTrue($this->moduleService->canActivate('module-a'));
        $this->assertFalse($this->moduleService->canActivate('module-b')); // Already active
    }

    /** @test */
    public function it_checks_if_module_can_be_deactivated(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
        ]);

        $this->assertTrue($this->moduleService->canDeactivate('module-a'));
        $this->assertFalse($this->moduleService->canDeactivate('module-b')); // Has dependent
    }

    /** @test */
    public function it_gets_module_dependencies(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => [
                'module-b' => '^1.0',
                'module-c' => '~2.0',
            ],
        ]);

        $dependencies = $this->moduleService->getModuleDependencies('module-a');

        $this->assertArrayHasKey('module-b', $dependencies);
        $this->assertArrayHasKey('module-c', $dependencies);
        $this->assertEquals('^1.0', $dependencies['module-b']);
        $this->assertEquals('~2.0', $dependencies['module-c']);
    }

    /** @test */
    public function it_returns_activation_order_for_multiple_modules(): void
    {
        Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => [],
        ]);

        $order = $this->moduleService->getActivationOrder(['module-a', 'module-b']);

        $this->assertEquals(['module-b', 'module-a'], $order);
    }

    /** @test */
    public function complete_module_lifecycle_works(): void
    {
        Event::fake();

        // Install
        $metadata = [
            'name' => 'Lifecycle Test',
            'slug' => 'lifecycle-test',
            'version' => '1.0.0',
            'description' => 'Testing complete lifecycle',
        ];

        $module = $this->moduleService->install('lifecycle-test', $metadata);
        $this->assertEquals(ModuleStatus::INSTALLED, $module->status);

        // Activate
        $module = $this->moduleService->activate('lifecycle-test');
        $this->assertEquals(ModuleStatus::ACTIVE, $module->status);

        // Deactivate
        $module = $this->moduleService->deactivate('lifecycle-test');
        $this->assertEquals(ModuleStatus::INACTIVE, $module->status);

        // Uninstall
        $result = $this->moduleService->uninstall('lifecycle-test');
        $this->assertTrue($result);

        // Verify events
        Event::assertDispatched(ModuleInstalled::class);
        Event::assertDispatched(ModuleActivated::class);
        Event::assertDispatched(ModuleDeactivated::class);
        Event::assertDispatched(ModuleUninstalled::class);
    }
}
