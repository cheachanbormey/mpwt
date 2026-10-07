<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Seed the leave_types table.
     */
    public function run(): void
    {
        $leaveTypes = [
            ['code' => 'LT-001', 'name' => 'សម្បាច់ឆ្នាំ', 'name_en' => 'Annual Leave', 'default_days_per_year' => 30, 'is_paid' => true, 'description' => 'សម្បាច់ប្រចាំឆ្នាំសម្រាប់បុគ្គលិកគ្រប់មុខ'],
            ['code' => 'LT-002', 'name' => 'សម្បាច់ពេលមករួច', 'name_en' => 'Sick Leave', 'default_days_per_year' => 60, 'is_paid' => true, 'description' => 'សម្បាច់ព្រះរាជពិធីព្រះបរមនាថ'],
            ['code' => 'LT-003', 'name' => 'សម្បាច់មរណភាព', 'name_en' => 'Maternity Leave', 'default_days_per_year' => 90, 'is_paid' => true, 'description' => 'សម្បាច់មរណភាព'],
            ['code' => 'LT-004', 'name' => 'សម្បាច់បុគ្គលិក', 'name_en' => 'Staff Leave', 'default_days_per_year' => null, 'is_paid' => true, 'description' => 'សម្បាច់ដោយមូលហេតុផុតក្រោយ'],
            ['code' => 'LT-005', 'name' => 'សម្បាច់បំពែលនិយោគ', 'name_en' => 'Compensation Leave', 'default_days_per_year' => 15, 'is_paid' => true, 'description' => 'សម្បាច់ជំនួសម៉ោងធ្វើការលើកទេ'],
            ['code' => 'LT-006', 'name' => 'សម្បាច់គ្មានប្រាក់', 'name_en' => 'Unpaid Leave', 'default_days_per_year' => 30, 'is_paid' => false, 'description' => 'សម្បាច់ដែលមិនទទួលប្រាក់ឈ្នួល'],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(['code' => $leaveType['code']], $leaveType + [
                'requires_approval' => true,
                'is_active' => true,
            ]);
        }
    }
}
