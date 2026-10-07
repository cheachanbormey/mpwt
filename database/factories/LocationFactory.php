<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'បន្ទប់ប្រជុំលេខ '.fake()->unique()->numberBetween(1, 999),
            'name_en' => 'Meeting Room '.fake()->unique()->numberBetween(1, 999),
            'address' => fake()->address(),
            'capacity' => fake()->randomElement([10, 20, 30, 50, 100, 200]),
            'is_active' => true,
        ];
    }

    /**
     * Small room.
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'capacity' => fake()->numberBetween(5, 15),
        ]);
    }
}
