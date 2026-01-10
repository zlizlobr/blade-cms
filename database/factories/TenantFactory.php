<?php

namespace Database\Factories;

use App\Domain\Tenant\Enums\PlanType;
use App\Domain\Tenant\Enums\TenantStatus;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'plan' => PlanType::FREE,
            'status' => TenantStatus::ACTIVE,
        ];
    }

    /**
     * Indicate that the tenant has a specific plan.
     */
    public function plan(PlanType $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => $plan,
        ]);
    }

    /**
     * Indicate that the tenant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TenantStatus::INACTIVE,
        ]);
    }
}
