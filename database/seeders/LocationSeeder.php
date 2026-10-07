<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seed the locations table (meeting rooms and venues).
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'បន្ទប់ប្រជុំរដ្ឋសភា', 'name_en' => 'Minister Meeting Room', 'capacity' => 40, 'address' => 'អគារ A ជាន់ផ្ទាំងទី ១'],
            ['name' => 'បន្ទប់ប្រជុំធនធានមនុស្ស', 'name_en' => 'HR Meeting Room', 'capacity' => 20, 'address' => 'អគារ B ជាន់ផ្ទាំងទី ៣'],
            ['name' => 'បន្ទប់ប្រជុំតូច', 'name_en' => 'Small Meeting Room', 'capacity' => 10, 'address' => 'អគារ B ជាន់ផ្ទាំងទី ៣'],
            ['name' => 'សាលាមានធានារ៉ាប់', 'name_en' => 'Ceremony Hall', 'capacity' => 200, 'address' => 'អគារ C ជាន់ផ្ទាំងទី ១'],
            ['name' => 'បន្ទប់ប្រជុំអន្តរជាតិ', 'name_en' => 'International Conference Room', 'capacity' => 100, 'address' => 'អគារ A ជាន់ផ្ទាំងទី ២'],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location + ['is_active' => true]);
        }
    }
}
