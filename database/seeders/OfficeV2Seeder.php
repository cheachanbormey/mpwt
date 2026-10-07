<?php

namespace Database\Seeders;

use App\Models\DepartmentV2;
use App\Models\OfficeV2;
use Illuminate\Database\Seeder;

class OfficeV2Seeder extends Seeder
{
    /**
     * Seed the offices_v2 table.
     */
    public function run(): void
    {
        $offices = [
            ['office_code' => 'OFF-001', 'office_name' => 'ការិយាល័យកណ្ដាលកិច្ច', 'department' => 'DEP-001', 'location' => 'រាជធានីភ្នំពេញ'],
            ['office_code' => 'OFF-002', 'office_name' => 'ផ្ទាំងបន្ទាន់ប្រឡង់', 'department' => 'DEP-001', 'location' => 'រាជធានីភ្នំពេញ'],
            ['office_code' => 'OFF-003', 'office_name' => 'ការិយាល័យហិរញ្ញវត្តិ', 'department' => 'DEP-002', 'location' => 'រាជធានីភ្នំពេញ'],
            ['office_code' => 'OFF-004', 'office_name' => 'ការិយាល័យគណនេយ្យ', 'department' => 'DEP-002', 'location' => 'ខេត្តកំពង'],
            ['office_code' => 'OFF-005', 'office_name' => 'ផ្ទាំងធនធានមនុស្ស', 'department' => 'DEP-003', 'location' => 'រាជធានីភ្នំពេញ'],
            ['office_code' => 'OFF-006', 'office_name' => 'ការិយាល័យការដឹកជញ្ជូន', 'department' => 'DEP-004', 'location' => 'ខេត្តកំពង'],
            ['office_code' => 'OFF-007', 'office_name' => 'ផ្ទាំងសាងសង់', 'department' => 'DEP-005', 'location' => 'ខេត្តកំពង'],
            ['office_code' => 'OFF-008', 'office_name' => 'ផ្ទាំងពិពណ៌នា', 'department' => 'DEP-005', 'location' => 'ខេត្តពោគ'],
            ['office_code' => 'OFF-009', 'office_name' => 'ផ្ទាំងអប់រំ', 'department' => 'DEP-006', 'location' => 'ខេត្តសៀមរាប'],
            ['office_code' => 'OFF-010', 'office_name' => 'ផ្ទាំងសុវត្ថិភាព', 'department' => 'DEP-007', 'location' => 'រាជធានីភ្នំពេញ'],
        ];

        foreach ($offices as $office) {
            $departmentId = DepartmentV2::query()
                ->where('dep_code', $office['department'])
                ->value('id');

            if ($departmentId === null) {
                continue;
            }

            OfficeV2::firstOrCreate(
                ['office_code' => $office['office_code']],
                [
                    'dep_id' => $departmentId,
                    'office_name' => $office['office_name'],
                    'location' => $office['location'],
                    'status' => OfficeV2::STATUS_ACTIVE,
                ]
            );
        }
    }
}
