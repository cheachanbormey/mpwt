<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAgenda;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MeetingAgendaSeeder extends Seeder
{
    /**
     * Seed the meeting_agendas table.
     */
    public function run(): void
    {
        // Safe to run repeatedly: do not add more rows on a second `db:seed`.
        if (MeetingAgenda::exists()) {
            return;
        }

        $presenters = Staff::whereIn('employment_status', [Staff::STATUS_ACTIVE])->pluck('id')->all();

        $topics = [
            'របបអត្ថប្បវត្តិសារីរុង',
            'វត្តមានបុគ្គលិកក្នុងរបៀបអន្តប៑',
            'ស្ថានភាពការងារបុគ្គលិក',
            'ផែនការបង្កើតបុគ្គលិក',
            'ការកែលម្អប្រព័ន្ធធនធានមនុស្ស',
            'បញ្ហាបង្កើត',
            'សុវត្ថិភាពកម្មវិធី',
            'ទាហានទីនោះដោយអ្នកចូលរួម',
            'ផែនការអភិវឌ្ឍន៍',
            'សេចក្តីសន្និដ្ឋាន',
        ];

        foreach (Meeting::all() as $meeting) {
            $count = fake()->numberBetween(3, 6);
            $shuffled = $topics;
            shuffle($shuffled);

            for ($no = 1; $no <= $count; $no++) {
                MeetingAgenda::firstOrCreate(
                    [
                        'meeting_id' => $meeting->id,
                        'agenda_no' => $no,
                    ],
                    [
                        'presenter_staff_id' => $presenters !== [] ? fake()->randomElement($presenters) : null,
                        'title' => $shuffled[$no - 1] ?? 'ចំណុចទី '.$no,
                        'description' => fake()->sentence(15),
                        'duration_minutes' => fake()->randomElement([10, 15, 20, 30]),
                        'sort_order' => $no,
                    ]
                );
            }
        }
    }
}
