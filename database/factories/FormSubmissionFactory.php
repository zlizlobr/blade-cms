<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormSubmission>
 */
class FormSubmissionFactory extends Factory
{
    protected $model = FormSubmission::class;

    public function definition(): array
    {
        return [
            'form_type' => 'contact',
            'tenant_id' => Tenant::factory(),
            'user_id' => null,
            'data' => [
                'name' => fake()->name(),
                'email' => fake()->safeEmail(),
                'message' => fake()->paragraph(),
            ],
        ];
    }

    /**
     * Indicate that the submission has a user.
     */
    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate the form type.
     */
    public function formType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'form_type' => $type,
        ]);
    }
}
