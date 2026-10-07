<?php

namespace Database\Seeders;

use App\Models\MeetingType;
use Illuminate\Database\Seeder;

class MeetingTypeSeeder extends Seeder
{
    /**
     * Seed the meeting_types table.
     */
    public function run(): void
    {
        $meetingTypes = [
            ['code' => 'MT-001', 'name' => 'កិច្ចប្រជុំប្រចាំសប្តាហ៍', 'name_en' => 'Weekly Meeting', 'default_duration_minutes' => 60, 'description' => 'កិច្ចប្រជុំសប្តាហ៍របស់ថ្នាក់គ្រប់គ្រង'],
            ['code' => 'MT-002', 'name' => 'កិច្ចប្រជុំប្រចាំខែ', 'name_en' => 'Monthly Meeting', 'default_duration_minutes' => 90, 'description' => 'កិច្ចប្រជុំប្រចាំខែវត្តមាន'],
            ['code' => 'MT-003', 'name' => 'កិច្ចប្រជុំការងារ', 'name_en' => 'Task Force Meeting', 'default_duration_minutes' => 120, 'description' => 'កិច្ចប្រជុំសម្រាប់ការងារការងារបុគ្គលិក'],
            ['code' => 'MT-004', 'name' => 'កិច្ចប្រជុំទូទៅ', 'name_en' => 'General Meeting', 'default_duration_minutes' => 60, 'description' => 'កិច្ចប្រជុំទូទៅរបស់ក្រសួង'],
            ['code' => 'MT-005', 'name' => 'កិច្ចប្រជុំបុគ្គលិក', 'name_en' => 'Staff Meeting', 'default_duration_minutes' => 45, 'description' => 'កិច្ចប្រជុំបុគ្គលិកតាមការិយាល័យ'],
        ];

        foreach ($meetingTypes as $meetingType) {
            MeetingType::firstOrCreate(['code' => $meetingType['code']], $meetingType + ['is_active' => true]);
        }
    }
}
