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
            ['code' => 'POS-001', 'name' => 'រដ្ឋបាល', 'name_en' => 'Minister', 'description' => 'មានសង្គមទាំងស្រុង'],
            ['code' => 'POS-002', 'name' => 'រដ្ឋលេខាធិការ', 'name_en' => 'Deputy Minister', 'description' => 'ជំនួសរដ្ឋបាល'],
            ['code' => 'POS-003', 'name' => 'មានគ្រប់គ្រងទូទៅ', 'name_en' => 'Director General', 'description' => 'ដឹកនាំផ្ទាំងទូទៅ'],
            ['code' => 'POS-004', 'name' => 'ប្រធានការិយាល័យ', 'name_en' => 'Office Director', 'description' => 'ដឹកនាំការិយាល័យ'],
            ['code' => 'POS-005', 'name' => 'ប្រធានផ្ទាំង', 'name_en' => 'Department Chief', 'description' => 'ដឹកនាំផ្ទាំង'],
            ['code' => 'POS-006', 'name' => 'ជំនាញខាងវិស័យ', 'name_en' => 'Chief of Division', 'description' => 'ជំនាញជាការិយាល័យ'],
            ['code' => 'POS-007', 'name' => 'បុគ្គលិកចាស់', 'name_en' => 'Senior Officer', 'description' => 'បុគ្គលិកជាន់ខ្ពស់'],
            ['code' => 'POS-008', 'name' => 'បុគ្គលិក', 'name_en' => 'Officer', 'description' => 'បុគ្គលិកទូទៅ'],
            ['code' => 'POS-009', 'name' => 'សន្តិភាព', 'name_en' => 'Security Guard', 'description' => 'ការការពារសុវត្ថិភាព'],
            ['code' => 'POS-010', 'name' => 'អ្នកជំនួយ', 'name_en' => 'Assistant', 'description' => 'អ្នកជំនួយការងារ'],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(['code' => $position['code']], $position);
        }
    }
}
