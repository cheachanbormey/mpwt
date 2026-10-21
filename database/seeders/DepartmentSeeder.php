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
            ['dep_code' => 'DEP-001',
             'name_kh' => 'អគ្គនាយយកដ្ឋានរដ្ឋបាល និងហិរញ្ញវត្ថុ',
             'name_en' => 'General Department of Administration and Finance', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-002', 
             'name_kh' => 'អគ្គនាយកដ្ឋានផែនការនិងគោលនយោបាយ', 
             'name_en' => 'General Department of Planning and Policy', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-003', 
             'name_kh' => 'អគ្គនាយកដ្ឋានបច្ចេកទេស', 
             'name_en' => 'General Department of Technical Affairs', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-004', 
             'name_kh' => 'អគ្គនាយកដ្ឋានសាធារណការ', 
             'name_en' => 'General Department of Public Works', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-005', 
             'name_kh' => 'អគ្គនាយកដ្ឋានដឹកជញ្ជូនផ្លូវគោក', 
             'name_en' => 'General Department of Land Transport', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-006', 
             'name_kh' => 'អគ្គនាយកដ្ឋានដឹកជញ្ជូនផ្លូវទឹក​ និងកំពង់ផែ', 
             'name_en' => 'General Department of Waterway and Port Transport', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-007', 
             'name_kh' => 'អគ្គនាយកដ្ឋានភស្តុភារកម្ម', 
             'name_en' => 'General Department of Logistics', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-007', 
             'name_kh' => 'អគ្គនាយកដ្ឋានប្រព័ន្ធចម្រោះទឹកកខ្វក់', 
             'name_en' => 'General Department of Wastewater Treatment Systems', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
            ['dep_code' => 'DEP-007', 
             'name_kh' => 'អគ្គនាយកដ្ឋានបច្ចេកវិទ្យានិងទំនាក់ទំនងសាធារណៈ', 
             'name_en' => 'General Department of Technology and Public Relations', 
             'staff_id' => null, // Director: Pech Sotheara
             ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['dep_code' => $department['dep_code']], $department);
        }
    }
}
