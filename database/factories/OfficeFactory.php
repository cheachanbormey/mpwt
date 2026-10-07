<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => 'OFF-'.fake()->unique()->numerify('###'),
            'name' => 'ការិយាល័យសារីរុង '.fake()->unique()->numberBetween(1, 999),
            'name_en' => 'Office '.fake()->unique()->numberBetween(1, 999),
            'phone' => fake()->numerify('0########'),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }

    /**
     * Office attached to an existing department.
     */
    public function forDepartment(Department $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department_id' => $department->getKey(),
        ]);
    }

    /**
     * Inactive office.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
