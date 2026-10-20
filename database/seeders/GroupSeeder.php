<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Seed the groups table.
     */
    public function run(): void
    {
        $groups = [
            ['name_kh' => 'ក្រុមការងារវត្តមាន', 
             'name_en' => 'Attendance Team', 
             'description' => 'ក្រុមការងារវត្តមាន',
             'leader_staff_id' => 'ST-00004',
             'location_id' => 1],
            ['name_kh' => 'ក្រុមការងារប្រព័ន្ធព័ត៌មាន',
             'name_en' => 'Information System Team', 
             'description' => '',
             'leader_staff_id' => 'ST-00007',
             'location_id' => 2],
            ['name_kh' => 'ក្រុមការងារហិរញ្ញវត្តិ', 
             'name_en' => 'Finance Team', 
             'description' => 'ក្រុមការងារហិរញ្ញវត្តិ',
             'leader_staff_id' => 'ST-00009',
             'location_id' => 3],
            ['name_kh' => 'ក្រុមការងារស្តារសញ្ញា', 
             'name_en' => 'Public Works Team', 
             'description' => 'ក្រុមការងារស្តារសញ្ញា',
             'leader_staff_id' => 'ST-00012',
             'location_id' => 4],
            ['name_kh' => 'ក្រុមបុគ្គលិកសុវត្ថិភាព', 
             'name_en' => 'Security Team', 
             'description' => 'ក្រុមបុគ្គលិកសុវត្ថិភាព',
             'leader_staff_id' => 'ST-00016', 
             'location_id' => 5],
            ['name_kh' => 'ក្រុមបង្កើតប្រចាំខែ', 
             'name_en' => 'Monthly Training Team', 
             'description' => 'ក្រុមបង្កើតប្រចាំខែ',
             'leader_staff_id' => 'ST-00014', 
             'location_id' => 5],
        ];

        $staff = Staff::pluck('id', 'staff_id')->toArray();

        foreach ($groups as $group) {
            Group::firstOrCreate(
                // ['code' => $group['code']],
                ['name_kh' => $group['name_kh']],
                [
                    'name_en' => $group['name_en'],
                    'description' => $group['description'],
                    'leader_staff_id' => $staff[$group['leader_staff_id']] ?? null,
                    'location_id' => $group['location_id'],
                    'is_active' => true,
                ]
            );
        }
    }
}
