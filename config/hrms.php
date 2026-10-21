<?php

use App\Models\Attendance;
use App\Models\Department;
// use App\Models\DepartmentV2;
use App\Models\EmploymentStatus;
use App\Models\Group;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Location;
use App\Models\Meeting;
use App\Models\MeetingAgenda;
use App\Models\MeetingParticipant;
use App\Models\MeetingType;
use App\Models\Office;
// use App\Models\OfficeV2;
use App\Models\Position;
use App\Models\Staff;
// use App\Models\StaffV2;

/*
|--------------------------------------------------------------------------
| HRMS Admin Modules
|--------------------------------------------------------------------------
|
| Every module an administrator can manage is described here. One registry
| drives the sidebar, the routes, the validation rules and the generic
| CRUD controller, so adding a module means adding one entry instead of a
| controller, a request class and three views.
|
| FIELD TYPES
| -----------
|   text      single line string
|   textarea  multi line string
|   email     validated email address
|   number    integer
|   decimal   decimal value
|   date      yyyy-mm-dd
|   time      HH:MM
|   datetime  yyyy-mm-dd HH:MM
|   boolean   checkbox
|   select    dropdown fed by another module or a fixed list
|
| FIELD OPTIONS
| -------------
|   'options_module' => 'offices'  fill the dropdown from that module
|   'options'        => [...]       fixed list of value => label pairs
|   'option_label'   => 'name'      column shown in a dropdown
|   'unique'         => true        value must be unique, ignoring self
|   'readonly'       => true        displayed but never written
|
| OPTIONAL MODULE KEYS
| ---------------------
|   'show'            => true     adds the detail page and its button
|   'status_field'    => 'status' adds activate / deactivate; the column
|                                   shown as a badge in the list
|   'status_values'   => [...]    the two values it toggles between,
|                                   defaulting to active / inactive
|   'detail_relations' => [...]   relations listed under the detail page,
|                                   e.g. a department's subordinate offices
|
*/

return [

    /*
    |----------------------------------------------------------------------
    | Employment states (lookup table created in Phase 03)
    |----------------------------------------------------------------------
    */

    // 'employment_statuses' => [
    //     'label' => 'ស្ថានភាពការងារ',
    //     'icon' => 'bi-toggle-on',
    //     'order' => 1,
    //     'singular' => 'ស្ថានភាពការងារ',
    //     // 'description' => 'ស្ថារាងការងាររបស់បុគ្គលិក (ការងារ, សាកល្បង, ផ្អាក, ចូលនៃមុខវិជ្ជា …)',
    //     'model' => EmploymentStatus::class,
    //     'columns' => ['code', 'name', 'sort_order', 'is_active'],
    //     'search' => ['code', 'name'],
    //     'fields' => [
    //         'code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 30, 'unique' => true],
    //         'name' => ['type' => 'text', 'label' => 'ឈ្មោះ', 'required' => true, 'max' => 191],
    //         'sort_order' => ['type' => 'number', 'label' => 'លំដាប់', 'min' => 0, 'max' => 999],
    //         'is_active' => ['type' => 'boolean', 'label' => 'អាចជ្រើសរើសបាន'],
    //     ],
    // ],

    /*
    |----------------------------------------------------------------------
    | Organisation
    |----------------------------------------------------------------------
    */

    'departments' => [
        'label' => 'អគ្គនាយកដ្ខាន',
        'icon' => 'bi-diagram-3',
        'order' => 2,
        'singular' => 'អគ្គនាយកដ្ខាន',
        'description' => 'អគ្គនាយកដ្ខានទាំងអស់នៃអង្គភាព',
        'model' => Department::class,
        'columns' => ['dep_code', 'name_kh', 'name_en', 'staff_id', 'is_active'],
        'search' => ['dep_code', 'name_kh', 'name_en'],
        'fields' => [
            'dep_code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 50, 'unique' => true],
            'name_kh' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            // 'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 1000],
            'staff_id' => ['type' => 'select', 'label' => 'ប្រធានអគ្គនាយកដ្ឋាន', 'options_module' => 'staff', 'option_label' => 'name_km',],
            'is_active' => ['type' => 'boolean', 'label' => 'ស្ថានភាព'],
        ],
    ],

    'positions' => [
        'label' => 'តួនាទី',
        'icon' => 'bi-briefcase',
        'order' => 3,
        'singular' => 'តួនាទី',
        'description' => 'តួនាទីការងារទាំងអស់',
        'model' => Position::class,
        'columns' => ['name_kh', 'name_en', 'is_active'],
        'search' => ['name_kh', 'name_en'],
        'fields' => [
            // 'code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 50, 'unique' => true],
            'name_kh' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 1000],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
        ],
    ],

    'offices' => [
        'label' => 'នាយកដ្ឋាន',
        'icon' => 'bi-building',
        'order' => 4,
        'singular' => 'នាយកដ្ឋាន',
        'description' => 'នាយកដ្ឋានស្ថិតក្រោមផ្ទាំងនីមួយៗ',
        'model' => Office::class,
        'columns' => ['office_code', 'name_kh', 'location_id', 'is_active'],
        'search' => ['office_code', 'name_kh', 'name_en'],
        'fields' => [
            'dep_code' => [
                'type' => 'select', 'label' => 'អគ្គនាយកដ្ឋាន', 'required' => true,
                'options_module' => 'departments', 'option_label' => 'name',
            ],
            'office_code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 50, 'unique' => true],
            'name_kh' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            // 'phone' => ['type' => 'text', 'label' => 'លេខទូរស័ព្ទ', 'max' => 30],
            // 'address' => ['type' => 'textarea', 'label' => 'អាសយដ្ឋាន', 'max' => 1000],
            'location_id' => [
                'type' => 'select', 'label' => 'ទីតាំង', 'required' => true,
                'options_module' => 'departments', 'option_label' => 'name',
            ],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | People
    |----------------------------------------------------------------------
    */

    'staff' => [
        'label' => 'បុគ្គលិក',
        'icon' => 'bi-people',
        'order' => 5,
        'singular' => 'បុគ្គលិក',
        'description' => 'ព័ត៌មានបុគ្គលិកទាំងអស់ក្នុងប្រព័ន្ធ',
        'model' => Staff::class,
        'columns' => ['staff_id',  'name_km', 'position_id', 'office_code' , 'telegram', 'phone', 'email', 'employment_status'],
        'search' => ['staff_id', 'name_km', 'name_en', 'phone'],
        'fields' => [
            'staff_id' => ['type' => 'text', 'label' => 'លេខបុគ្គលិក', 'required' => true, 'max' => 50, 'unique' => true],
            'name_km' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            'gender' => ['type' => 'select', 'label' => 'ភេទ', 'options' => [
                        'male' => 'ប្រុស', 'female' => 'ស្រី', 'other' => 'ផ្សេងៗ',
            ]],
            // 'date_of_birth' => ['type' => 'date', 'label' => 'ថ្ងៃខែឆ្នាំកើត'],
            // 'national_id' => ['type' => 'text', 'label' => 'លេខអត្តសញ្ញាណ', 'max' => 30],
            'phone' => ['type' => 'text', 'label' => 'លេខទូរស័ព្ទ', 'max' => 30],
            'email' => ['type' => 'email', 'label' => 'អ៊ីមែល'],
            'telegram' => ['type' => 'telegram', 'label' => 'តេឡេក្រាម'],
            // 'address' => ['type' => 'textarea', 'label' => 'អាសយដ្ឋាន', 'max' => 1000],
            'office_code' => ['type' => 'select', 'label' => 'ការិយាល័យ', 'options_module' => 'offices', 'option_label' => 'name'],
            'position_id' => ['type' => 'select', 'label' => 'តួនាទី', 'options_module' => 'positions', 'option_label' => 'name'],
            'system_role' => ['type' => 'select', 'label' => 'ស្ថានភាព', 'required' => true, 'options_module' => 'employment_statuses', 'option_label' => 'name'],
            'employment_status' => ['type' => 'select', 'label' => 'តួនាទីក្នុងប្រព័ន្ធ', 'options' => ['admin' => 'អ្នកគ្រប់គ្រង', 'staff' => 'បុគ្គលិក' ], ],
            // 'hire_date' => ['type' => 'date', 'label' => 'កាលបរិច្ឆេទចូលរួមការងារ'],
            // 'base_salary' => ['type' => 'decimal', 'label' => 'ប្រាក់ឈ្នួលមូលដ្ឋាន', 'min' => 0],
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Attendance
    |----------------------------------------------------------------------
    */

    'attendances' => [
        'label' => 'វត្តមាន',
        'icon' => 'bi-clock-history',
        'order' => 6,
        'singular' => 'កំណត់ត្រាវត្តមាន',
        'description' => 'កំណត់ត្រាវត្តមានប្រចាំថ្ងៃរបស់បុគ្គលិក',
        'model' => Attendance::class,
        'show' => true,
        // Roll-up screens offered beside the row list.
        'reports' => [
            ['route' => 'admin.attendances.day', 'label' => 'របាយការណ៍វត្តមានប្រចាំថ្ងៃ', 'icon' => 'bi-calendar-day'],
            ['route' => 'admin.attendances.month', 'label' => 'របាយការណ៍វត្តមានប្រចាំខែ', 'icon' => 'bi-calendar-month'],
        ],
        'columns' => ['work_date', 'staff.name_km', 'check_in', 'check_out', 'worked_hours', 'status', 'check_in_source'],
        'search' => ['staff.name_km', 'staff.staff_id', 'work_date' ],
        'filters' => [
            'staff_id' => ['label' => 'បុគ្គលិក', 'options_module' => Staff::class, 'option_label' => 'name_kh'],
            'status' => ['label' => 'ស្ថាភាព', 'options' => [
                'present' => 'វត្តមាន', 'absent' => 'អវត្តមាន', 'late' => 'យឺត',
                'leave' => 'សុំច្បាប់'
            ]],
            'from' => ['label' => 'ចាប់ពីថ្ងៃ', 'column' => 'work_date', 'operator' => '>=', 'type' => 'date'],
            'to' => ['label' => 'ដល់ថ្ងៃ', 'column' => 'work_date', 'operator' => '<=', 'type' => 'date'],
        ],
        'fields' => [
            'staff_id' => ['type' => 'select', 'label' => 'បុគ្គលិក', 'required' => true, 'options_module' => Staff::class, 'option_label' => 'name_km'],
            'work_date' => ['type' => 'date', 'label' => 'កាលបរិច្ឆេទ', 'required' => true],
            'check_in' => ['type' => 'datetime', 'label' => 'ម៉ោងចូល', 'nullable' => true],
            'check_out' => ['type' => 'datetime', 'label' => 'ម៉ោងចេញ', 'nullable' => true],
            'worked_hours' => ['type' => 'decimal', 'label' => 'ម៉ោងធ្វើការ', 'min' => 0, 'max' => 24],
            'status' => ['type' => 'select', 'label' => 'ស្ថានភាព', 'required' => true, 'options' => [
                'present' => 'វត្តមាន', 'absent' => 'អវត្តមាន', 'late' => 'យឺត',
                'leave' => 'សុំច្បាប់', 
            ]],
            'check_in_source' => ['type' => 'select', 'label' => 'ប្រភពកំណត់ត្រា', 'options' => [
                'manual' => 'ដោយដៃ', 'qr' => 'QR Code',
            ]],
            // 'notes' => ['type' => 'text', 'label' => 'សំគាល់', 'max' => 191],
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Leave
    |----------------------------------------------------------------------
    */

    'leave_types' => [
        'label' => 'ប្រភេទច្បាប់',
        'icon' => 'bi-file-earmark-text',
        'order' => 7,
        'singular' => 'ប្រភេទច្បាប់',
        'description' => 'ប្រភេទច្បាប់ដែលអនុញ្ញាតឱ្យបុគ្គលិកស្នើសុំ',
        'model' => LeaveType::class,
        'columns' => ['name_kh', 'default_days_per_year', 'requires_approval', 'is_active'],
        'search' => ['name_kh', 'name_en'],
        'fields' => [
            'name_kh' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            // 'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 1000],
            'default_days_per_year' => ['type' => 'number', 'label' => 'ចំនួនថ្ងៃក្នុងមួយឆ្នាំ', 'min' => 0, 'max' => 366],
            /*
             * Reframed for this project: leave is signed by hand on paper,
             * there is no online approval step.
             */
            'requires_approval' => ['type' => 'boolean', 'label' => 'ត្រូវមានការចុចហត្ថលេខាដោយអ្នកគ្រប់គ្រង'],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
        ],
    ],

    'leave_requests' => [
        'label' => 'សំណើសុំឈប់សម្រាក',
        'icon' => 'bi-envelope-paper',
        'order' => 8,
        'singular' => 'សំណើសុំឈប់សម្រាក',
        'description' => 'បញ្ជីសំណើសុំឈប់សម្រាកទាំងអស់។ សំណើសុំឈប់សម្រាកត្រូវបានបោះពុម្ព និងចុចហត្ថលេខាដោយដៃ។',
        'model' => LeaveRequest::class,
        'columns' => ['created_at', 'staff.name_km', 'leaveType.name', 'start_date', 'end_date', 'total_days', 'status'],
        'search' => ['reason', 'staff.name_km', 'start_date' ],
        'filters' => [
            'staff_id' => ['label' => 'បុគ្គលិក', 'options_module' => 'staff', 'option_label' => 'name_km'],
            'status' => ['label' => 'ស្ថានភាព', 'options' => [
                'pending' => 'កំពុងរង់ចាំ', 'approved' => 'បានអនុម័ត',
                'rejected' => 'បានបដិសេធ', 'cancelled' => 'បានលុបចោល',
            ]],
            'from' => ['label' => 'ចាប់ពីកាលបរិច្ឆេទ', 'type' => 'date', 'column' => 'start_date', 'operator' => '>='],
            'to' => ['label' => 'ដល់កាលបរិច្ឆេទ', 'type' => 'date', 'column' => 'end_date', 'operator' => '<='],
        ],
        'fields' => [
            'staff_id' => ['type' => 'select', 'label' => 'បុគ្គលិក', 'required' => true, 'options_module' => 'staff', 'option_label' => 'name_km'],
            'leave_type_id' => ['type' => 'select', 'label' => 'ប្រភេទសំឡេង', 'required' => true, 'options_module' => 'leave_types', 'option_label' => 'name'],
            'start_date' => ['type' => 'date', 'label' => 'ចាប់ពីកាលបរិច្ឆេទ', 'required' => true],
            'end_date' => ['type' => 'date', 'label' => 'ដល់កាលបរិច្ឆេទ', 'required' => true, 'after' => 'start_date'],
            'total_days' => ['type' => 'decimal', 'label' => 'ចំនួនថ្ងៃ', 'min' => 0],
            'return_date' => ['type' => 'date', 'label' => 'កាលបរិច្ឆេទចូល', 'required' => true, 'after' => 'end_date'],
            'reason' => ['type' => 'textarea', 'label' => 'មូលដ្ឋាន', 'required' => true, 'max' => 1000],
            /*
             * status is deliberately READ-ONLY here.
             * There is no online approval workflow, so nothing in the
             * application may change it. See README / report.
             */
            'status' => [
                'type' => 'select', 'label' => 'ស្ថានភាព (មិនអាចកែប្រែ)',
                'readonly' => true, 'default' => 'pending',
                'options' => [
                    'pending' => 'កំពុងរង់ចាំ', 'approved' => 'បានអនុម័ត',
                    'rejected' => 'បានបដិសេធ', 'cancelled' => 'បានលុបចោល',
                ],
            ],
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Groups
    |----------------------------------------------------------------------
    */

    'groups' => [
        'label' => 'ក្រុមប្រជុំ',
        'icon' => 'bi-people-fill',
        'order' => 9,
        'singular' => 'ក្រុមប្រជុំ',
        'description' => 'សមាជិកក្រុម និងប្រធានក្រុម',
        'model' => Group::class,
        'columns' => ['name_kh', 'leader.name_km', 'is_active'],
        'search' => ['name_kh', 'name_en'],
        'fields' => [
            // 'code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 50, 'unique' => true],
            'name' => ['type' => 'text', 'label' => 'ឈ្មោះក្រុមជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះក្រុមជាភាសាអង់គ្លេស', 'max' => 191],
            'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 1000],
            'leader_staff_id' => ['type' => 'select', 'label' => 'ប្រធានក្រុម', 'options_module' => 'staff', 'option_label' => 'name_km'],
            'location_id' => ['type' => 'select', 'label' => 'ទីតាំង', 'options_module' => 'locations', 'option_label' => 'name'],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Meetings
    |----------------------------------------------------------------------
    */

    'locations' => [
        'label' => 'ទីកន្លែង',
        'icon' => 'bi-geo-alt',
        'order' => 10,
        'singular' => 'ទីកន្លែង',
        'description' => 'ទីកន្លែងសម្រាប់កិច្ចប្រជុំ',
        'model' => Location::class,
        'columns' => ['name', 'name_en', 'capacity', 'is_active'],
        'search' => ['name', 'name_en'],
        'fields' => [
            'name' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            // 'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            'buiding' => ['type' => 'text', 'label' => 'ឈ្មោះអគារ', 'max' => 191],
            'floor' => ['type' => 'text', 'label' => 'ជាន់', 'placeholder'=> 'ឧទាហរណ៍៖ ជាន់ទី ១', 'max' => 100],
            'capacity' => ['type' => 'number', 'label' => 'ចំនួនអាចដល់', 'min' => 0, 'max' => 10000],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
            'facilities' => ['type' => 'textarea' , 'label' => 'បរិក្ខា និងឧបករណ៍បច្ចេកវិទ្យា', 'placeholder'=> 'ឧទាហរណ៍៖ Smart Screen, High-Speed WiFi', 'max' => 1000],
        ],
    ],

    'meeting_types' => [
        'label' => 'ប្រភេទកិច្ចប្រជុំ',
        'icon' => 'bi-collection',
        'order' => 11,
        'singular' => 'ប្រភេទកិច្ចប្រជុំ',
        'description' => 'ប្រភេទកិច្ចប្រជុំទាំងអស់',
        'model' => MeetingType::class,
        'columns' => ['code', 'name', 'default_duration_minutes', 'is_active'],
        'search' => ['code', 'name', 'name_en'],
        'fields' => [
            'code' => ['type' => 'text', 'label' => 'លេខកូដ', 'required' => true, 'max' => 50, 'unique' => true],
            'name' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
            'name_en' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
            'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 1000],
            'default_duration_minutes' => ['type' => 'number', 'label' => 'រយៈពេលលំនាំ (នាទី)', 'min' => 0, 'max' => 1440],
            'is_active' => ['type' => 'boolean', 'label' => 'សកម្ម'],
        ],
    ],

    'meetings' => [
        'label' => 'កិច្ចប្រជុំ',
        'icon' => 'bi-calendar-event',
        'order' => 12,
        'singular' => 'កិច្ចប្រជុំ',
        'description' => 'កិច្ចប្រជុំទាំងអស់',
        'model' => Meeting::class,
        'columns' => ['meeting_date', 'title', 'meetingType.name', 'location.name', 'start_time', 'end_time', 'status'],
        'search' => ['title', 'agenda_summary'],
        'filters' => [
            'status' => ['label' => 'ស្ថារាង', 'options' => [
                'scheduled' => 'បានកំណត់', 'ongoing' => 'កំពុងប្រព្រោះ',
                'completed' => 'បានបញ្ចប់', 'cancelled' => 'បានលុបចោល',
            ]],
            'meeting_date' => ['label' => 'កាលបរិច្ឆេទ', 'type' => 'date'],
        ],
        'fields' => [
            'title' => ['type' => 'text', 'label' => 'ចំណងជើង', 'required' => true, 'max' => 191],
            'meeting_type_id' => ['type' => 'select', 'label' => 'ប្រភេទ', 'required' => true, 'options_module' => 'meeting_types', 'option_label' => 'name'],
            'location_id' => ['type' => 'select', 'label' => 'ទីកន្លែង', 'options_module' => 'locations', 'option_label' => 'name'],
            'meeting_date' => ['type' => 'date', 'label' => 'កាលបរិច្ឆេទ', 'required' => true],
            'start_time' => ['type' => 'time', 'label' => 'ម៉ោងចាប់ផ្ដើម', 'required' => true],
            'end_time' => ['type' => 'time', 'label' => 'ម៉ោងបញ្ចប់', 'required' => true],
            'status' => ['type' => 'select', 'label' => 'ស្ថារាង', 'required' => true, 'options' => [
                'scheduled' => 'បានកំណត់', 'ongoing' => 'កំពុងប្រព្រោះ',
                'completed' => 'បានបញ្ចប់', 'cancelled' => 'បានលុបចោល',
            ]],
            'agenda_summary' => ['type' => 'textarea', 'label' => 'សេចក្តីសង្ខេប', 'max' => 2000],
            'created_by_staff_id' => ['type' => 'select', 'label' => 'អ្នកបង្កើត', 'options_module' => 'staff', 'option_label' => 'name_km'],
        ],
    ],

    'meeting_participants' => [
        'label' => 'អ្នកចូលរួម',
        'icon' => 'bi-person-check',
        'order' => 13,
        'singular' => 'អ្នកចូលរួម',
        'description' => 'បញ្ជីអ្នកចុះឈ្មោះក្នុងកិច្ចប្រជុំនីមួយៗ',
        'model' => MeetingParticipant::class,
        'columns' => ['meeting.title', 'staff.name_km', 'attendance_status', 'attended_at', 'notes'],
        'search' => ['notes'],
        'filters' => [
            'meeting_id' => ['label' => 'កិច្ចប្រជុំ', 'options_module' => 'meetings', 'option_label' => 'title'],
            'attendance_status' => ['label' => 'ស្ថារាង', 'options' => [
                'invited' => 'បានអញ្ជើញ', 'accepted' => 'បានទទួល', 'declined' => 'បានបដិសេធ',
                'attended' => 'បានចូលរួម', 'absent' => 'មិនបានមក',
            ]],
        ],
        'fields' => [
            'meeting_id' => ['type' => 'select', 'label' => 'កិច្ចប្រជុំ', 'required' => true, 'options_module' => 'meetings', 'option_label' => 'title'],
            'staff_id' => ['type' => 'select', 'label' => 'បុគ្គលិក', 'required' => true, 'options_module' => 'staff', 'option_label' => 'name_km'],
            'attendance_status' => ['type' => 'select', 'label' => 'ស្ថារាង', 'required' => true, 'options' => [
                'invited' => 'បានអញ្ជើញ', 'accepted' => 'បានទទួល', 'declined' => 'បានបដិសេធ',
                'attended' => 'បានចូលរួម', 'absent' => 'មិនបានមក',
            ]],
            'attended_at' => ['type' => 'datetime', 'label' => 'ម៉ោងចូលរួម'],
            'notes' => ['type' => 'text', 'label' => 'សំគាល់', 'max' => 191],
        ],
    ],

    'meeting_agendas' => [
        'label' => 'រាប់ការប្រជុំ',
        'icon' => 'bi-list-ol',
        'order' => 14,
        'singular' => 'រាប់ការប្រជុំ',
        'description' => 'បញ្ជីប្រធានកិច្ចប្រជុំតាមលំដាប់',
        'model' => MeetingAgenda::class,
        'columns' => ['meeting.title', 'agenda_no', 'title', 'presenter.name_km', 'duration_minutes'],
        'search' => ['title', 'description'],
        'filters' => [
            'meeting_id' => ['label' => 'កិច្ចប្រជុំ', 'options_module' => 'meetings', 'option_label' => 'title'],
        ],
        'fields' => [
            'meeting_id' => ['type' => 'select', 'label' => 'កិច្ចប្រជុំ', 'required' => true, 'options_module' => 'meetings', 'option_label' => 'title'],
            'agenda_no' => ['type' => 'number', 'label' => 'លេខលំដាប់', 'required' => true, 'min' => 1, 'max' => 999],
            'title' => ['type' => 'text', 'label' => 'ចំណងជើង', 'required' => true, 'max' => 191],
            'presenter_staff_id' => ['type' => 'select', 'label' => 'អ្នកបង្ហាញ', 'options_module' => 'staff', 'option_label' => 'name_km'],
            'duration_minutes' => ['type' => 'number', 'label' => 'រយៈពេល (នាទី)', 'min' => 0, 'max' => 1440],
            'sort_order' => ['type' => 'number', 'label' => 'លំដាប់បង្ហាញ', 'min' => 0, 'max' => 999],
            'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 2000],
        ],
    ],


    /*
    |----------------------------------------------------------------------
    | Departments v2
    |----------------------------------------------------------------------
    |
    | A second, independent department table. It is NOT a rename of the
    | original `departments` table above: those rows, and every office and
    | staff record pointing at them, are left untouched.
    |
    | `subordinate_offices` is not a column. It is the offices relation
    | declared in detail_relations below, so the detail page can never show
    | offices that no longer belong to the department.
    |
    | `director_dep` points at staff, so a department can be led by an
    | existing employee. It is nullable: a department may exist before
    | anyone is appointed, and removing the staff row leaves the department
    | in place.
    |
    */

    // 'departments_v2' => [
    //     'label' => 'ផ្ទាំង (កំណែថ្មី)',
    //     'icon' => 'bi-diagram-3',
    //     'order' => 15,
    //     'singular' => 'ផ្ទាំង',
    //     'description' => 'កំណត់ត្រាផ្ទាំងការងារជំនួសដែលមានលេខកូដ និងនាយកផ្ទាំង',
    //     'model' => DepartmentV2::class,

    //     'show' => true,
    //     'status_field' => 'status',
    //     'status_values' => ['active' => 'active', 'inactive' => 'inactive'],

    //     'columns' => ['dep_code', 'dep_kh_name', 'dep_en_name', 'director.name_km', 'status'],
    //     'search' => ['dep_code', 'dep_kh_name', 'dep_en_name'],

    //     'filters' => [
    //         'status' => ['label' => 'ស្ថានភាព', 'options' => DepartmentV2::statusLabels()],
    //     ],

    //     // Rendered as a table on the department's detail page.
    //     'detail_relations' => [
    //         'offices' => [
    //             'label' => 'ការងារក្រោមផ្ទាំង',
    //             'status_column' => 'status',
    //             'columns' => [
    //                 'office_code' => 'លេខកូដ',
    //                 'office_name' => 'ឈ្មោះការងារ',
    //                 'location' => 'ទីតាំង',
    //                 'status' => 'ស្ថានភាព',
    //             ],
    //             'labels' => [
    //                 'status' => OfficeV2::statusLabels(),
    //             ],
    //         ],
    //     ],

    //     'fields' => [
    //         'dep_code' => ['type' => 'text', 'label' => 'លេខកូដផ្ទាំង', 'required' => true, 'max' => 50, 'unique' => true],
    //         'dep_kh_name' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាខ្មែរ', 'required' => true, 'max' => 191],
    //         'dep_en_name' => ['type' => 'text', 'label' => 'ឈ្មោះជាភាសាអង់គ្លេស', 'max' => 191],
    //         'director_dep' => ['type' => 'select', 'label' => 'នាយកផ្ទាំង', 'options_module' => 'staff', 'option_label' => 'name_km'],
    //         'status' => [
    //             'type' => 'select', 'label' => 'ស្ថានភាព',
    //             'options' => DepartmentV2::statusLabels(), 'default' => DepartmentV2::STATUS_ACTIVE,
    //         ],
    //     ],
    // ],

    /*
    |----------------------------------------------------------------------
    | Offices v2
    |----------------------------------------------------------------------
    |
    | A second, independent office table. `dep_id` cascades, so removing a
    | department removes the offices that belong to it - the same rule the
    | original offices table follows.
    |
    */

    // 'offices_v2' => [
    //     'label' => 'ការងារ (កំណែថ្មី)',
    //     'icon' => 'bi-building',
    //     'order' => 16,
    //     'singular' => 'ការងារ',
    //     'description' => 'ការងារស្ថិតក្រោមផ្ទាំងនីមួយៗ',
    //     'model' => OfficeV2::class,

    //     'show' => true,
    //     'status_field' => 'status',
    //     'status_values' => ['active' => 'active', 'inactive' => 'inactive'],

    //     'columns' => ['office_code', 'office_name', 'dep_id', 'department.dep_kh_name', 'location', 'status'],
    //     'search' => ['office_code', 'office_name', 'location'],

    //     'filters' => [
    //         // Filter the office list by department.
    //         'dep_id' => [
    //             'label' => 'ផ្ទាំង',
    //             'options_module' => 'departments_v2',
    //             'option_label' => 'dep_kh_name',
    //         ],
    //         'status' => ['label' => 'ស្ថានភាព', 'options' => OfficeV2::statusLabels()],
    //     ],

    //     'fields' => [
    //         'dep_id' => [
    //             'type' => 'select', 'label' => 'ផ្ទាំង', 'required' => true,
    //             'options_module' => 'departments_v2', 'option_label' => 'dep_kh_name',
    //         ],
    //         'office_code' => ['type' => 'text', 'label' => 'លេខកូដការងារ', 'required' => true, 'max' => 50, 'unique' => true],
    //         'office_name' => ['type' => 'text', 'label' => 'ឈ្មោះការងារ', 'required' => true, 'max' => 191],
    //         'location' => ['type' => 'text', 'label' => 'ទីតាំង', 'max' => 191],
    //         'description' => ['type' => 'textarea', 'label' => 'ពិពណ៌នា', 'max' => 2000],
    //         'status' => [
    //             'type' => 'select', 'label' => 'ស្ថានភាព',
    //             'options' => OfficeV2::statusLabels(), 'default' => OfficeV2::STATUS_ACTIVE,
    //         ],
    //     ],
    // ],

];
