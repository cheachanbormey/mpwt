<?php

namespace Database\Factories;

use App\Models\EmploymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmploymentStatus>
 */
class EmploymentStatusFactory extends Factory
{
    protected $model = EmploymentStatus::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->slug(2),
            'name' => $this->faker->word(),
            'sort_order' => $this->faker->numberBetween(1, 50),
            'is_active' => true,
        ];
    }

    /**
     * A state that can no longer be chosen on forms.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
