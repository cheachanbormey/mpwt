<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed the departments table.
     */
    public function run(): void
    {
        $departments = [
            ['code' => 'DEP-001', 'name' => 'អគ្គមន៍បន្ទាន់ប្រឡង់', 'name_en' => 'Information Technology', 'description' => 'ផ្ទាំងជំនួសប្រព័ន្ធ និងព័ត៌មានគ្រប់គ្រង'],
            ['code' => 'DEP-002', 'name' => 'អគ្គមន៍ច្បាស់ការណ៍', 'name_en' => 'Finance', 'description' => 'សាធារណកិច្ច និងហិរញ្ញវត្តិ'],
            ['code' => 'DEP-003', 'name' => 'ធនធានមនុស្ស', 'name_en' => 'Human Resources', 'description' => 'គ្រប់គ្រងបុគ្គលិក និងវត្តមាន'],
            ['code' => 'DEP-004', 'name' => 'ការដឹកជញ្ជូន', 'name_en' => 'Transport', 'description' => 'រតបភាព និងការដឹកជញ្ជូន'],
            ['code' => 'DEP-005', 'name' => 'ការស្តារសញ្ញា', 'name_en' => 'Public Works', 'description' => 'សាងសង់ និងការិយាល័យស្រុក'],
            ['code' => 'DEP-006', 'name' => 'ផ្ទាំងការអប់រំ', 'name_en' => 'Education', 'description' => 'ការអប់រំ និងបង្កើត'],
            ['code' => 'DEP-007', 'name' => 'សុវត្ថិភាព និងសន្តិភាព', 'name_en' => 'Security', 'description' => 'សុវត្ថិភាព និងសុវត្ថិភាពកម្មវិធី'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['code' => $department['code']], $department);
        }
    }
}
