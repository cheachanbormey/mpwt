<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Seed the positions table.
     */
    public function run(): void
    {
        $positions = [
            ['name_kh' => 'មន្រី្តរដ្ឋបាល', 'name_en' => 'Minister', 'description' => ''],
            ['name_kh' => 'មន្រី្តរដ្ឋលេខាធិការ', 'name_en' => 'Deputy Minister', 'description' => ''],
            ['name_kh' => 'មន្រី្តគ្រប់គ្រងទូទៅ', 'name_en' => 'Director General', 'description' => ''],
            ['name_kh' => 'ប្រធានការិយាល័យ', 'name_en' => 'Office Director', 'description' => ''],
            ['name_kh' => 'អ្នកគ្រប់គ្រងប្រព័ន្ធ', 'name_en' => 'Admin', 'description' => ''],
            // ['name_kh' => 'ជំនាញខាងវិស័យ', 'name_en' => 'Chief of Division', 'description' => ''],
            // ['name_kh' => 'បុគ្គលិកចាស់', 'name_en' => 'Senior Officer', 'description' => ''],
            // ['name_kh' => 'បុគ្គលិក', 'name_en' => 'Officer', 'description' => ''],
            // ['name_kh' => 'សន្តិភាព', 'name_en' => 'Security Guard', 'description' => ''],
            ['name_kh' => 'មន្រី្តជំនួយការ', 'name_en' => 'Assistant', 'description' => ''],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(['name_kh' => $position['name_kh']], $position);
        }
    }
}
