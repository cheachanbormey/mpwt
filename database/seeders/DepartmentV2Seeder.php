<?php

namespace Database\Seeders;

use App\Models\DepartmentV2;
use Illuminate\Database\Seeder;

class DepartmentV2Seeder extends Seeder
{
    /**
     * Seed the departments_v2 table.
     */
    public function run(): void
    {
        $departments = [
            ['dep_code' => 'DEP-001', 'dep_kh_name' => 'អគ្គមន៍បន្ទាន់ប្រឡង់', 'dep_en_name' => 'Information Technology'],
            ['dep_code' => 'DEP-002', 'dep_kh_name' => 'អគ្គមន៍ច្បាស់ការណ៍', 'dep_en_name' => 'Finance'],
            ['dep_code' => 'DEP-003', 'dep_kh_name' => 'ធនធានមនុស្ស', 'dep_en_name' => 'Human Resources'],
            ['dep_code' => 'DEP-004', 'dep_kh_name' => 'ការដឹកជញ្ជូន', 'dep_en_name' => 'Transport'],
            ['dep_code' => 'DEP-005', 'dep_kh_name' => 'ការស្តារសញ្ញា', 'dep_en_name' => 'Public Works'],
            ['dep_code' => 'DEP-006', 'dep_kh_name' => 'ផ្ទាំងការអប់រំ', 'dep_en_name' => 'Education'],
            ['dep_code' => 'DEP-007', 'dep_kh_name' => 'សុវត្ថិភាព និងសន្តិភាព', 'dep_en_name' => 'Security'],
        ];

        foreach ($departments as $department) {
            DepartmentV2::firstOrCreate(
                ['dep_code' => $department['dep_code']],
                $department + ['status' => DepartmentV2::STATUS_ACTIVE]
            );
        }
    }
}
