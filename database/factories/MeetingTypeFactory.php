<?php

namespace Database\Factories;

use App\Models\MeetingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingType>
 */
class MeetingTypeFactory extends Factory
{
    protected $model = MeetingType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'MT-'.fake()->unique()->numerify('###'),
            'name' => fake()->randomElement([
                'កិច្ចប្រជុំប្រចាំសប្តាហ៍',
                'កិច្ចប្រជុំប្រចាំខែ',
                'កិច្ចប្រជុំទូទៅ',
                'កិច្ចប្រជុំការងារ',
                'កិច្ចប្រជុំបុគ្គលិក',
            ]).' '.fake()->unique()->numberBetween(1, 999),
            'name_en' => fake()->randomElement(['Weekly', 'Monthly', 'General', 'Task', 'Staff']).' Meeting',
            'description' => fake()->sentence(8),
            'default_duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120, 180]),
            'is_active' => true,
        ];
    }
}
