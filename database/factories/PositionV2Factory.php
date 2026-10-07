<?php

namespace Database\Factories;

use App\Models\PositionV2;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PositionV2>
 */
class PositionV2Factory extends Factory
{
    protected $model = PositionV2::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'position_name_kh' => 'តួនាទី'.fake()->unique()->numberBetween(1, 999),
            'position_name_en' => fake()->unique()->jobTitle(),
            'description' => fake()->sentence(8),
            'status' => PositionV2::STATUS_ACTIVE,
        ];
    }

    /**
     * Position that is not offered to staff.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PositionV2::STATUS_INACTIVE,
        ]);
    }

    /**
     * Position carrying only a Khmer name.
     */
    public function withoutEnglishName(): static
    {
        return $this->state(fn (array $attributes) => [
            'position_name_en' => null,
        ]);
    }
}
