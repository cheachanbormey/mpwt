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
            ['name_kh' => 'កិច្ចប្រជុំប្រចាំសប្តាហ៍', 'name_en' => 'Weekly Meeting', 'type'=> 'private'], 
            ['name_kh' => 'កិច្ចប្រជុំប្រចាំខែ', 'name_en' => 'Monthly Meeting', 'type'=> 'public'],
            ['name_kh' => 'កិច្ចប្រជុំការងារ', 'name_en' => 'Task Force Meeting', 'type'=> 'private'], 
            ['name_kh' => 'កិច្ចប្រជុំទូទៅ', 'name_en' => 'General Meeting', 'type'=> 'private'],
            ['name_kh' => 'កិច្ចប្រជុំបុគ្គលិក', 'name_en' => 'Staff Meeting', 'type'=> 'public'], 
        ];

        foreach ($meetingTypes as $meetingType) {
            MeetingType::firstOrCreate(['name_kh' => $meetingType['name_kh']], $meetingType + ['is_active' => true]);
        }
    }
}
