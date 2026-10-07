<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meeting>
 */
class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->numberBetween(8, 14).':00:00';

        return [
            'meeting_type_id' => MeetingType::factory(),
            'location_id' => null,
            'created_by_staff_id' => null,
            'title' => 'កិច្ចប្រជុំលេខ '.fake()->unique()->numberBetween(1, 9999),
            'agenda_summary' => fake()->paragraph(2),
            'meeting_date' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'start_time' => $start,
            'end_time' => date('H:i:s', strtotime($start.' +1 hour')),
            'status' => Meeting::STATUS_SCHEDULED,
        ];
    }

    /**
     * A completed meeting.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Meeting::STATUS_COMPLETED,
        ]);
    }

    /**
     * A cancelled meeting.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Meeting::STATUS_CANCELLED,
        ]);
    }

    /**
     * A meeting happening today or later.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'meeting_date' => fake()->dateTimeBetween('now', '+2 months'),
            'status' => Meeting::STATUS_SCHEDULED,
        ]);
    }
}
