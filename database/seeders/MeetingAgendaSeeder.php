<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingAgenda;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class MeetingAgendaSeeder extends Seeder
{
    public function run(): void
    {
        if (MeetingAgenda::exists()) {
            return;
        }

        // ឈ្មោះអ្នកបទបង្ហាញ (មិនមែន id)
        $presenters = Staff::where('status', Staff::STATUS_ACTIVE)
            ->pluck('name_km')   
            ->all();

        $topics = [
            'ការអញ្ជើញមកដល់របស់ក្រុមការងារ',
            'កិច្ចស្វាគមន៍សិស្សកម្មសិក្សាថ្មី',
            'របាយការណ៍លទ្ធផលការងាររបស់សិស្សកម្មសិក្សា',
            'កម្មវិធី Mini App (មុខងារបន្តសុពលភាពឆ្លៀក)',
            'គេហទំព័រផ្លូវការរបស់ក្រសួងជំនាន់ទី៣ (MPWT Website)',
            'មុខងារផ្ទៀងផ្ទាត់ទិន្នន័យឌីជីថល (Vehicle Check)',
            'កម្មវិធីដកពិន្ទុ និងពិន័យបទល្មើសចរាចរណ៍ផ្លូវគោក (DL Point Deduction and Fine Mobile App)',
            'សំណួរចម្លើយ និងផ្សេងៗ',
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
                        'topic' => $shuffled[$no - 1] ?? 'ចំណុចទី '.$no,
                        'presenter_name' => $presenters !== []
                            ? fake()->randomElement($presenters)
                            : 'មិនទាន់កំណត់',
                    ]
                );
            }
        }
    }
}
// namespace Database\Seeders;

// use App\Models\Meeting;
// use App\Models\MeetingAgenda;
// use App\Models\Staff;
// use Illuminate\Database\Seeder;

// class MeetingAgendaSeeder extends Seeder
// {
//     /**
//      * Seed the meeting_agendas table.
//      */
//     public function run(): void
//     {
//         // Safe to run repeatedly: do not add more rows on a second `db:seed`.
//         if (MeetingAgenda::exists()) {
//             return;
//         }

//         $presenters = Staff::whereIn('status', [Staff::STATUS_ACTIVE])->pluck('id')->all();

//         $topic = [
//             'ការអញ្ជើញមកដល់របស់ក្រុមការងារ',
//             'កិច្ចស្វាគមន៍សិស្សកម្មសិក្សាថ្មី',
//             'របាយការណ៍លទ្ធផលការងាររបស់សិស្សកម្មសិក្សា',
//             'កម្មវិធី Mini App (មុខងារបន្តសុពលភាពឆ្លៀក)',
//             'គេហទំព័រផ្លូវការរបស់ក្រសួងជំនាន់ទី៣ (MPWT Website)',
//             'មុខងារផ្ទៀងផ្ទាត់ទិន្នន័យឌីជីថល (Vehicle Check)',
//             'កម្មវិធីដកពិន្ទុ និងពិន័យបទល្មើសចរាចរណ៍ផ្លូវគោក (DL Point Deduction and Fine Mobile App)',
//             'សំណួរចម្លើយ និងផ្សេងៗ',
//         ];

//         foreach (Meeting::all() as $meeting) {
//             $count = fake()->numberBetween(3, 6);
//             $shuffled = $topic;
//             shuffle($shuffled);

//             for ($no = 1; $no <= $count; $no++) {
//                 MeetingAgenda::firstOrCreate(
//                     [
//                         'meeting_id' => $meeting->id,
//                         'agenda_no' => $no,
//                     ],
//                     [
//                         'presenter_name' => $presenters !== [] ? fake()->randomElement($presenters) : null,
//                         // 'title' => $shuffled[$no - 1] ?? 'ចំណុចទី '.$no,
//                         // 'description' => fake()->sentence(15),
//                         // 'duration_minutes' => fake()->randomElement([10, 15, 20, 30]),
//                         // 'sort_order' => $no,
//                     ]
//                 );
// //             }
//         }
//     }
// }
