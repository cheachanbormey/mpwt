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
            ['name_kh' => 'ច្បាប់ប្រចាំឆ្នាំ', 'name_en' => 'Annual Leave', 'default_days_per_year' => 15 ],
            ['name_kh' => 'ព្យាបាលជម្ងឺ', 'name_en' => 'Sick Leave', 'default_days_per_year' => 30 ],
            ['name_kh' => 'លំហែមាតុភាព', 'name_en' => 'Maternity Leave', 'default_days_per_year' => 90 ],
            ['name_kh' => 'កិច្ចការផ្ទាល់ខ្លួន', 'name_en' => 'Personal Leave', 'default_days_per_year' => 7 ],
            ['name_kh' => 'រយៈពេលខ្លី', 'name_en' => 'Short Leave', 'default_days_per_year' => 15 ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(['name_kh' => $leaveType['name_kh']], $leaveType + [
                'requires_approval' => true,
                'is_active' => true,
            ]);
        }
    }
}
