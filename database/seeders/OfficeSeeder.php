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
            ['code' => 'OFF-001', 'department' => 'DEP-003', 'name' => 'ការិយាល័យធនធានមនុស្ស', 'name_en' => 'Human Resources Office', 'phone' => '023888100'],
            ['code' => 'OFF-002', 'department' => 'DEP-003', 'name' => 'ការិយាល័យវត្តមាន', 'name_en' => 'Attendance Office', 'phone' => '023888101'],
            ['code' => 'OFF-003', 'department' => 'DEP-001', 'name' => 'ការិយាល័យព័ត៌មាន', 'name_en' => 'Information Office', 'phone' => '023888102'],
            ['code' => 'OFF-004', 'department' => 'DEP-001', 'name' => 'ការិយាល័យប្រព័ន្ធ', 'name_en' => 'System Office', 'phone' => '023888103'],
            ['code' => 'OFF-005', 'department' => 'DEP-002', 'name' => 'ការិយាល័យហិរញ្ញវត្តិ', 'name_en' => 'Finance Office', 'phone' => '023888104'],
            ['code' => 'OFF-006', 'department' => 'DEP-004', 'name' => 'ការិយាល័យដឹកជញ្ជូន', 'name_en' => 'Transport Office', 'phone' => '023888105'],
            ['code' => 'OFF-007', 'department' => 'DEP-005', 'name' => 'ការិយាល័យស្តារសញ្ញា', 'name_en' => 'Public Works Office', 'phone' => '023888106'],
            ['code' => 'OFF-008', 'department' => 'DEP-006', 'name' => 'ការិយាល័យបង្កើត', 'name_en' => 'Training Office', 'phone' => '023888107'],
            ['code' => 'OFF-009', 'department' => 'DEP-007', 'name' => 'ការិយាល័យសុវត្ថិភាព', 'name_en' => 'Security Office', 'phone' => '023888108'],
            ['code' => 'OFF-010', 'department' => 'DEP-003', 'name' => 'ការិយាល័យសន្តិភាពកម្មវិធី', 'name_en' => 'Programme Security Office', 'phone' => '023888109'],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(
                ['code' => $office['code']],
                [
                    'department_id' => Department::where('code', $office['department'])->value('id'),
                    'name' => $office['name'],
                    'name_en' => $office['name_en'],
                    'phone' => $office['phone'],
                    'address' => 'រាជធានីភ្នំពេញ',
                    'is_active' => true,
                ]
            );
        }
    }
}
