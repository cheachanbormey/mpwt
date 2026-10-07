<?php

namespace Database\Factories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'GRP-'.fake()->unique()->numerify('###'),
            'name' => 'ក្រុមការងារលេខ '.fake()->unique()->numberBetween(1, 999),
            'name_en' => 'Working Group '.fake()->unique()->numberBetween(1, 999),
            'description' => fake()->sentence(8),
            'leader_staff_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Group with no leader assigned.
     */
    public function withoutLeader(): static
    {
        return $this->state(fn (array $attributes) => [
            'leader_staff_id' => null,
        ]);
    }
}
