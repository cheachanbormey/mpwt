<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MeetingParticipantSeeder extends Seeder
{
    /**
     * Seed the meeting_participants table.
     */
    public function run(): void
    {
        $staffIds = Staff::pluck('id')->all();

        if ($staffIds === [] || Meeting::count() === 0) {
            return;
        }

        $groupIds = Staff::pluck('id')->all();

        if ($groupIds === [] || Meeting::count() === 0) {
            return;
        }

        // Safe to run repeatedly: do not add more rows on a second `db:seed`.
        if (MeetingParticipant::exists()) {
            return;
        }

        foreach (Meeting::all() as $meeting) {
            $organiser = $meeting->created_by_staff_id;
            $pool = array_values(array_diff($staffIds, array_filter([$organiser])));
            shuffle($pool);

            $invitees = array_slice($pool, 0, fake()->numberBetween(4, 10));

            if ($organiser) {
                array_unshift($invitees, $organiser);
            }

            foreach (array_unique($invitees) as $staffId) {
                $status = match (true) {
                    $staffId === $organiser => MeetingParticipant::STATUS_ATTENDED,
                    $meeting->status === Meeting::STATUS_COMPLETED => fake()->randomElement([
                        MeetingParticipant::STATUS_ATTENDED,
                        MeetingParticipant::STATUS_ABSENT,
                        MeetingParticipant::STATUS_ACCEPTED,
                    ]),
                    $meeting->status === Meeting::STATUS_CANCELLED => MeetingParticipant::STATUS_DECLINED,
                    default => MeetingParticipant::STATUS_INVITED,
                };

                MeetingParticipant::firstOrCreate(
                    [
                        'meeting_id' => $meeting->id,
                        'staff_id' => $staffId,
                    ],
                    [
                        'attendance_status' => $status,
                        'attended_at' => $status === MeetingParticipant::STATUS_ATTENDED
                            ? $meeting->meeting_date->copy()->setTime(9, 0)
                            : null,
                        // 'notes' => null,
                    ]
                );
            }
        }
    }
}
