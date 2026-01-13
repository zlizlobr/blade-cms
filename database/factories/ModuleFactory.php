<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Module>
 */
class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Module',
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'version' => '1.0.0',
            'status' => ModuleStatus::INSTALLED,
            'core_compatibility' => '^1.0',
            'dependencies' => null,
            'installed_at' => now(),
            'enabled_at' => null,
            'tenant_id' => null,
        ];
    }

    /**
     * Indicate that the module is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModuleStatus::ACTIVE,
            'enabled_at' => now(),
        ]);
    }

    /**
     * Indicate that the module is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ModuleStatus::INACTIVE,
            'enabled_at' => null,
        ]);
    }

    /**
     * Indicate that the module has dependencies.
     */
    public function withDependencies(array $dependencies): static
    {
        return $this->state(fn (array $attributes) => [
            'dependencies' => $dependencies,
        ]);
    }

    /**
     * Indicate specific version.
     */
    public function version(string $version): static
    {
        return $this->state(fn (array $attributes) => [
            'version' => $version,
        ]);
    }
}
