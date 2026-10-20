<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Meeting;
use App\Models\MeetingType;
use App\Models\MeetingAgenda;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;


class MeetingSeeder extends Seeder
{
    /**
     * Seed the meetings table.
     */
    public function run(): void
    {
        $meetingTypes = MeetingType::all();
        $locations = Location::all();
        $meetingAgenda = MeetingAgenda::all();
        $creators = Staff::whereIn('status', [
            Staff::STATUS_ACTIVE,
        ])->get();

        if ($meetingTypes->isEmpty() || $creators->isEmpty() || $locations->isEmpty()) {
            return;
        }

        // Safe to run repeatedly: do not duplicate meetings on a second `db:seed`.
        if (Meeting::exists()) {
            return;
        }

        $titles = [
            'កិច្ចប្រជុំវត្តមានប្រចាំសប្តាហ៍',
            'កិច្ចប្រជុំពិនិត្យការងារវត្តមាន',
            'កិច្ចប្រជុំប្រចាំខែថ្នាក់គ្រប់គ្រង',
            'កិច្ចប្រជុំការងារប្រព័ន្ធព័ត៌មាន',
            'កិច្ចប្រជុំអភិបាលការងារ',
            'កិច្ចប្រជុំការងារស្ថានភាពកម្មវិធី',
            'កិច្ចប្រជុំគ្រូបុគ្គលិក',
            'កិច្ចប្រជុំពិភ័យការងារ',
        ];

        for ($i = 0; $i < 12; $i++) {
            // Faker returns a plain DateTime, so convert to Carbon first.
            $date = Carbon::instance(fake()->dateTimeBetween('-2 months', '+3 weeks'));

            $status = match (true) {
                $date->isPast() && fake()->boolean(85) => Meeting::STATUS_COMPLETED,
                $date->isPast() => Meeting::STATUS_CANCELLED,
                fake()->boolean(15) => Meeting::STATUS_ONGOING,
                default => Meeting::STATUS_SCHEDULED,
            };

            $startHour = fake()->numberBetween(8, 14);

            Meeting::create([
                'meeting_type_id' => $meetingTypes->random()->id,
                'location_id' => $locations->random()->id,
                // 'location_id' => fake()->boolean(90) ? $locations->random()->id : null,
                // 'location_id' => $locations->isNotEmpty() && fake()->boolean(90)
                //     ? $locations->random()->id
                //     : null,

                // 'agenda_id' => $meetingAgenda->isNotEmpty() && fake()->boolean(90)
                //     ? $meetingAgenda->random()->id
                //     : null,
                'created_by_staff_id' => $creators->random()->id,
                'title' => $titles[array_rand($titles)].' លើទី '.($i + 1),
                'agenda_summary' => fake()->paragraph(3),
                'meeting_date' => $date,
                'start_time' => sprintf('%02d:00:00', $startHour),
                'end_time' => sprintf('%02d:00:00', $startHour + fake()->numberBetween(1, 3)),
                // 'agenda_id' => $meetingAgenda->random()->id,
                // 'agenda_id' => fake()->boolean(90) ? $meetingAgenda->random()->id : null,
                'status' => $status,
            ]);
        }
    }
}
