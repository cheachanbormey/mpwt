<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingAgenda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingAgenda>
 */
class MeetingAgendaFactory extends Factory
{
    protected $model = MeetingAgenda::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $agendaNo = fake()->unique()->numberBetween(1, 500);

        return [
            'meeting_id' => Meeting::factory(),
            'presenter_staff_id' => null,
            'agenda_no' => $agendaNo,
            'title' => 'ចំណុចទី '.$agendaNo.' - '.fake()->word(),
            'description' => fake()->sentence(12),
            'duration_minutes' => fake()->randomElement([5, 10, 15, 20, 30]),
            'sort_order' => $agendaNo,
        ];
    }

    /**
     * Attach the agenda item to an existing meeting with a free number.
     */
    public function forMeeting(Meeting $meeting, ?int $agendaNo = null): static
    {
        // Honour an explicit number, otherwise append after the last existing one.
        $number = $agendaNo ?? ((int) $meeting->agendas()->max('agenda_no') + 1);

        return $this->state(fn (array $attributes) => [
            'meeting_id' => $meeting->getKey(),
            'agenda_no' => $number,
            'sort_order' => $number,
        ]);
    }
}
