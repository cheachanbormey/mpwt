<?php

namespace Database\Factories;

use App\Models\DepartmentV2;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DepartmentV2>
 */
class DepartmentV2Factory extends Factory
{
    protected $model = DepartmentV2::class;

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
            'dep_code' => 'DEP-'.fake()->unique()->numerify('###'),
            'dep_kh_name' => $name,
            'dep_en_name' => Str::title(fake()->unique()->words(2, true)),
            'director_dep' => null,
            'status' => DepartmentV2::STATUS_ACTIVE,
        ];
    }

    /**
     * Department led by an existing staff member.
     */
    public function directedBy(Staff $staff): static
    {
        return $this->state(fn (array $attributes) => [
            'director_dep' => $staff->getKey(),
        ]);
    }

    /**
     * Inactive department.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DepartmentV2::STATUS_INACTIVE,
        ]);
    }
}
