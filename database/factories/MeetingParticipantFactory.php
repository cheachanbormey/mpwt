<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MeetingParticipant>
 */
class MeetingParticipantFactory extends Factory
{
    protected $model = MeetingParticipant::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meeting_id' => Meeting::factory(),
            'staff_id' => Staff::factory(),
            'attendance_status' => MeetingParticipant::STATUS_INVITED,
            'attended_at' => null,
            'notes' => null,
        ];
    }

    /**
     * Accepted the invitation.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_status' => MeetingParticipant::STATUS_ACCEPTED,
        ]);
    }

    /**
     * Declined the invitation.
     */
    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_status' => MeetingParticipant::STATUS_DECLINED,
        ]);
    }

    /**
     * Showed up at the meeting.
     */
    public function attended(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_status' => MeetingParticipant::STATUS_ATTENDED,
            'attended_at' => now(),
        ]);
    }

    /**
     * Did not show up.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_status' => MeetingParticipant::STATUS_ABSENT,
        ]);
    }

    /**
     * Register an existing member of staff for a meeting.
     */
    public function forMeetingAndStaff(Meeting $meeting, Staff $staff): static
    {
        return $this->state(fn (array $attributes) => [
            'meeting_id' => $meeting->getKey(),
            'staff_id' => $staff->getKey(),
        ]);
    }
}
