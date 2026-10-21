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
            ['name' => 'បន្ទប់ប្រជុំរដ្ឋសភា', 
             'building' => 'អគារ A',
             'floor' => 'ជាន់ទី ១',
             'capacity' => 40,
             'facilities' => 'Projector, Whiteboard'],            
            ['name' => 'បន្ទប់ប្រជុំធនធានមនុស្ស', 
             'building' => 'អគារ B', 
             'floor' => 'ជាន់ទី ៣', 
             'capacity' => 20, 
             'facilities' => 'Projector, Whiteboard'],
            ['name' => 'បន្ទប់ប្រជុំតូច', 
             'building' => 'អគារ B', 
             'floor' => 'ជាន់ទី ៣', 
             'capacity' => 10, 
             'facilities' => 'Projector, Whiteboard'],
            ['name' => 'សាលាមានធានារ៉ាប់', 
             'building' => 'អគារ C', 
             'floor' => 'ជាន់ទី ១', 
             'capacity' => 200, 
             'facilities' => 'Projector, Whiteboard'],
            ['name' => 'បន្ទប់ប្រជុំអន្តរជាតិ', 
             'building' => 'អគារ A', 
             'floor' => 'ជាន់ទី ៥', 
             'capacity' => 100, 
             'facilities' => 'Projector, Whiteboard'],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location + ['is_active' => true]);
        }
    }
}
