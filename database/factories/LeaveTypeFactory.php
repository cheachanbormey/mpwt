<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'LT-'.fake()->unique()->numerify('###'),
            'name' => 'សម្បាច់ '.fake()->unique()->numberBetween(1, 999),
            'name_en' => 'Leave '.fake()->unique()->numberBetween(1, 999),
            'description' => fake()->sentence(8),
            'default_days_per_year' => fake()->randomElement([10, 15, 20, 30, 60, 90, 180]),
            'is_paid' => true,
            'requires_approval' => true,
            'is_active' => true,
        ];
    }

    /**
     * Unpaid leave.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
        ]);
    }
}
