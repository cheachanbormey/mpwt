<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Seed the offices table.
     */
    public function run(): void
    {
        $offices = [
            ['office_code' => 'OFF-001', 'dep_code' => 'DEP-003', 'name_kh' => 'ការិយាល័យធនធានមនុស្ស', 'name_en' => 'Human Resources Office', 'location_id' => '1'],
            ['office_code' => 'OFF-002', 'dep_code' => 'DEP-003', 'name_kh' => 'ការិយាល័យវត្តមាន', 'name_en' => 'Attendance Office', 'location_id' => '1'],
            ['office_code' => 'OFF-003', 'dep_code' => 'DEP-001', 'name_kh' => 'ការិយាល័យព័ត៌មាន', 'name_en' => 'Information Office', 'location_id' => '3'],
            ['office_code' => 'OFF-004', 'dep_code' => 'DEP-001', 'name_kh' => 'ការិយាល័យប្រព័ន្ធ', 'name_en' => 'System Office', 'location_id' => '2'],
            ['office_code' => 'OFF-005', 'dep_code' => 'DEP-002', 'name_kh' => 'ការិយាល័យហិរញ្ញវត្តិ', 'name_en' => 'Finance Office', 'location_id' => '1'],
            ['office_code' => 'OFF-006', 'dep_code' => 'DEP-004', 'name_kh' => 'ការិយាល័យសន្តិសុខ', 'name_en' => 'Security Office', 'location_id' => '2'],
            ['office_code' => 'OFF-007', 'dep_code' => 'DEP-005', 'name_kh' => 'ការិយាល័យស្តារសញ្ញា', 'name_en' => 'Public Works Office', 'location_id' => '3'],
            ['office_code' => 'OFF-008', 'dep_code' => 'DEP-004', 'name_kh' => 'ការិយាល័យបង្កើត', 'name_en' => 'Training Office', 'location_id' => '1'],
            ['office_code' => 'OFF-009', 'dep_code' => 'DEP-002', 'name_kh' => 'ការិយាល័យសុវត្ថិភាព', 'name_en' => 'Security Office', 'location_id' => '2'],
            ['office_code' => 'OFF-010', 'dep_code' => 'DEP-003', 'name_kh' => 'ការិយាល័យសន្តិភាពកម្មវិធី', 'name_en' => 'Programme Security Office', 'location_id' => '2'],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(
                ['office_code' => $office['office_code']],
                [
                    'dep_code' => Department::where('dep_code', $office['dep_code'])->value('id'),
                    'name_kh' => $office['name_kh'],
                    'name_en' => $office['name_en'],
                    'location_id' => $office['location_id'],
                    'is_active' => true,
                ]
            );
        }
    }
}
