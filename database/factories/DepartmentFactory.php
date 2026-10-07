<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'ផ្ទាំងផ្ទាល់មុខ',
            'ផ្ទាំងបន្ទាន់ប្រឡង់',
            'ផ្ទាំងបញ្ហាសាស្ត្រ',
            'ផ្ទាំងការដឹកជញ្ជូន',
            'ផ្ទាំងថ្នាក់រៀន',
            'ផ្ទាំងប្រឡង់ និងរតបភាព',
        ]).' '.fake()->unique()->numberBetween(1, 999);

        return [
            'code' => 'DEP-'.fake()->unique()->numerify('###'),
            'name' => $name,
            'name_en' => Str::title(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(8),
            'is_active' => true,
        ];
    }

    /**
     * Inactive department.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
