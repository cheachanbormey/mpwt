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
            ['code' => 'GRP-001', 'name' => 'ក្រុមការងារវត្តមាន', 'name_en' => 'Attendance Team', 'leader' => 'ST-00004'],
            ['code' => 'GRP-002', 'name' => 'ក្រុមការងារប្រព័ន្ធព័ត៌មាន', 'name_en' => 'Information System Team', 'leader' => 'ST-00007'],
            ['code' => 'GRP-003', 'name' => 'ក្រុមការងារហិរញ្ញវត្តិ', 'name_en' => 'Finance Team', 'leader' => 'ST-00009'],
            ['code' => 'GRP-004', 'name' => 'ក្រុមការងារស្តារសញ្ញា', 'name_en' => 'Public Works Team', 'leader' => 'ST-00012'],
            ['code' => 'GRP-005', 'name' => 'ក្រុមបុគ្គលិកសុវត្ថិភាព', 'name_en' => 'Security Team', 'leader' => 'ST-00016'],
            ['code' => 'GRP-006', 'name' => 'ក្រុមបង្កើតប្រចាំខែ', 'name_en' => 'Monthly Training Team', 'leader' => 'ST-00014'],
        ];

        $staff = Staff::pluck('id', 'staff_code');

        foreach ($groups as $group) {
            Group::firstOrCreate(
                ['code' => $group['code']],
                [
                    'name' => $group['name'],
                    'name_en' => $group['name_en'],
                    'description' => 'ក្រុមការងារការងារ'.$group['name_en'],
                    'leader_staff_id' => $staff[$group['leader']] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }
}
