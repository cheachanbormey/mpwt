<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'POS-'.fake()->unique()->numerify('###'),
            'name' => fake()->randomElement([
                'បុគ្គលិក',
                'បុគ្គលិកចាស់',
                'ជំនាញខាងវិស័យ',
                'មានគ្រប់គ្រង',
                'រដ្ឋបាល',
                'សន្តិភាព',
            ]).' '.fake()->unique()->numberBetween(1, 999),
            'name_en' => Str::title(fake()->unique()->words(2, true)),
            'description' => fake()->sentence(8),
            'is_active' => true,
        ];
    }

    /**
     * Inactive position.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
