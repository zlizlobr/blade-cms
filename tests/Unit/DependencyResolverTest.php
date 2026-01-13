<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Exceptions\CircularDependencyException;
use App\Domain\Module\Models\Module;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Module\Services\DependencyResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DependencyResolverTest extends TestCase
{
    use RefreshDatabase;

    private DependencyResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $repository = app(ModuleRepositoryInterface::class);
        $this->resolver = new DependencyResolver($repository);
    }

    /** @test */
    public function it_resolves_simple_dependencies(): void
    {
        $moduleA = Module::create([
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
            'dependencies' => [],
        ]);

        $dependencies = $this->resolver->resolveDependencies($moduleA);

        $this->assertArrayHasKey('module-b', $dependencies);
        $this->assertEquals('^1.0', $dependencies['module-b']);
    }

    /** @test */
    public function it_resolves_nested_dependencies(): void
    {
        $moduleA = Module::create([
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
            'dependencies' => ['module-c' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module C',
            'slug' => 'module-c',
            'version' => '1.0.0',
            'status' => ModuleStatus::ACTIVE,
            'dependencies' => [],
        ]);

        $dependencies = $this->resolver->resolveDependencies($moduleA);

        $this->assertArrayHasKey('module-b', $dependencies);
        $this->assertArrayHasKey('module-c', $dependencies);
    }

    /** @test */
    public function it_checks_dependencies_are_satisfied(): void
    {
        $moduleA = Module::create([
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
            'dependencies' => [],
        ]);

        $this->assertTrue($this->resolver->checkDependenciesSatisfied($moduleA));
    }

    /** @test */
    public function it_detects_missing_dependencies(): void
    {
        $moduleA = Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        $this->assertFalse($this->resolver->checkDependenciesSatisfied($moduleA));
    }

    /** @test */
    public function it_detects_inactive_dependencies(): void
    {
        $moduleA = Module::create([
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
            'status' => ModuleStatus::INACTIVE,
            'dependencies' => [],
        ]);

        $this->assertFalse($this->resolver->checkDependenciesSatisfied($moduleA));
    }

    /** @test */
    public function it_detects_circular_dependency(): void
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

        $this->assertTrue($this->resolver->detectCircularDependency('module-a'));
        $this->assertTrue($this->resolver->detectCircularDependency('module-b'));
    }

    /** @test */
    public function it_detects_circular_dependency_with_three_modules(): void
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
            'dependencies' => ['module-c' => '^1.0'],
        ]);

        Module::create([
            'name' => 'Module C',
            'slug' => 'module-c',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-a' => '^1.0'],
        ]);

        $this->assertTrue($this->resolver->detectCircularDependency('module-a'));
    }

    /** @test */
    public function it_returns_false_for_non_circular_dependencies(): void
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

        $this->assertFalse($this->resolver->detectCircularDependency('module-a'));
    }

    /** @test */
    public function it_returns_activation_order(): void
    {
        $moduleA = Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        $moduleB = Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-c' => '^1.0'],
        ]);

        $moduleC = Module::create([
            'name' => 'Module C',
            'slug' => 'module-c',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => [],
        ]);

        $order = $this->resolver->getActivationOrder([$moduleA, $moduleB, $moduleC]);

        // C has no dependencies, B depends on C, A depends on B
        // Order should be: C, B, A
        $this->assertEquals(['module-c', 'module-b', 'module-a'], $order);
    }

    /** @test */
    public function it_throws_exception_for_circular_dependency_in_activation_order(): void
    {
        $moduleA = Module::create([
            'name' => 'Module A',
            'slug' => 'module-a',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-b' => '^1.0'],
        ]);

        $moduleB = Module::create([
            'name' => 'Module B',
            'slug' => 'module-b',
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'dependencies' => ['module-a' => '^1.0'],
        ]);

        $this->expectException(CircularDependencyException::class);

        $this->resolver->getActivationOrder([$moduleA, $moduleB]);
    }
}
