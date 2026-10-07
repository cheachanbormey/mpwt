<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\EmploymentStatus;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Meeting;
use App\Models\MeetingAgenda;
use App\Models\MeetingParticipant;
use App\Models\Staff;
use App\Models\StaffV2;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase 02 - Database architecture verification.
 *
 * Runs against the in-memory SQLite database configured in phpunit.xml.
 */
class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every table required by the project scope.
     *
     * @var list<string>
     */
    private const TABLES = [
        'departments',
        'offices',
        'staff',
        'positions',
        'employment_statuses',
        'attendances',
        'leave_types',
        'leave_requests',
        'groups',
        'group_members',
        'locations',
        'meeting_types',
        'meetings',
        'meeting_participants',
        'meeting_agendas',
        'departments_v2',
        'offices_v2',
        'positions_v2',
        'staff_v2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Table existence
    |--------------------------------------------------------------------------
    */

    public function test_all_required_tables_exist(): void
    {
        foreach (self::TABLES as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table [{$table}] is missing"
            );
        }
    }

    public function test_no_unrelated_tables_were_created(): void
    {
        $expected = array_merge(self::TABLES, [
            'users',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'jobs',
            'job_batches',
            'failed_jobs',
            'migrations',
        ]);

        $actual = collect(DB::select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"
        ))->pluck('name')->all();

        $unexpected = array_diff($actual, $expected);

        $this->assertSame([], array_values($unexpected), 'Unexpected tables found: '.implode(', ', $unexpected));
    }

    /*
    |--------------------------------------------------------------------------
    | Columns
    |--------------------------------------------------------------------------
    */

    public function test_departments_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('departments', [
            'id', 'code', 'name', 'name_en', 'description', 'is_active',
            'created_at', 'updated_at',
        ]));
    }

    public function test_offices_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('offices', [
            'id', 'department_id', 'code', 'name', 'name_en', 'phone',
            'address', 'is_active', 'created_at', 'updated_at',
        ]));
    }

    public function test_staff_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('staff', [
            'id', 'user_id', 'office_id', 'position_id', 'staff_code',
            'name_km', 'name_en', 'gender', 'date_of_birth', 'national_id',
            'phone', 'email', 'address', 'photo', 'hire_date',
            'employment_status', 'base_salary', 'created_at',
            'updated_at', 'deleted_at',
        ]));
    }

    public function test_attendances_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('attendances', [
            'id', 'staff_id', 'work_date', 'check_in', 'check_out',
            'worked_hours', 'status', 'check_in_source', 'notes',
            'created_at', 'updated_at',
        ]));
    }

    public function test_leave_requests_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('leave_requests', [
            'id', 'staff_id', 'leave_type_id', 'start_date', 'end_date',
            'total_days', 'reason', 'status', 'approved_by_staff_id',
            'approved_at', 'rejection_reason', 'created_at', 'updated_at',
        ]));
    }

    public function test_group_members_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('group_members', [
            'id', 'group_id', 'staff_id', 'role_in_group', 'is_leader',
            'joined_at', 'created_at', 'updated_at',
        ]));
    }

    public function test_meetings_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('meetings', [
            'id', 'meeting_type_id', 'location_id', 'created_by_staff_id',
            'title', 'agenda_summary', 'meeting_date', 'start_time',
            'end_time', 'status', 'created_at', 'updated_at',
        ]));
    }

    public function test_meeting_participants_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('meeting_participants', [
            'id', 'meeting_id', 'staff_id', 'attendance_status',
            'attended_at', 'notes', 'created_at', 'updated_at',
        ]));
    }

    public function test_meeting_agendas_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('meeting_agendas', [
            'id', 'meeting_id', 'presenter_staff_id', 'agenda_no', 'title',
            'description', 'duration_minutes', 'sort_order',
            'created_at', 'updated_at',
        ]));
    }

    public function test_employment_statuses_table_has_the_expected_columns(): void
    {
        $this->assertTrue(Schema::hasColumns('employment_statuses', [
            'id', 'code', 'name', 'sort_order', 'is_active',
            'created_at', 'updated_at',
        ]));
    }

    public function test_users_table_has_a_role_column(): void
    {
        $this->assertTrue(Schema::hasColumns('users', ['role']));
    }

    /*
    |--------------------------------------------------------------------------
    | Employment statuses
    |--------------------------------------------------------------------------
    */

    public function test_the_migration_seeds_the_known_employment_states(): void
    {
        $codes = EmploymentStatus::query()->orderBy('id')->pluck('code')->all();

        $this->assertSame([
            Staff::STATUS_ACTIVE,
            Staff::STATUS_PROBATION,
            Staff::STATUS_SUSPENDED,
            Staff::STATUS_ON_LEAVE,
            Staff::STATUS_RETIRED,
            Staff::STATUS_TERMINATED,
        ], $codes);
    }

    public function test_every_seeded_employment_state_is_active(): void
    {
        $this->assertSame(0, EmploymentStatus::query()->where('is_active', false)->count());
    }

    /*
    |--------------------------------------------------------------------------
    | Foreign keys
    |--------------------------------------------------------------------------
    */

    public function test_foreign_keys_are_enforced_on_the_mysql_connection(): void
    {
        // SQLite does not enforce FKs by default, so this only asserts on MySQL.
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->assertTrue(true, 'Skipped: not running on MySQL');

            return;
        }

        $expected = [
            'offices:department_id' => 'departments',
            'staff:user_id' => 'users',
            'staff:office_id' => 'offices',
            'staff:position_id' => 'positions',
            'groups:leader_staff_id' => 'staff',
            'attendances:staff_id' => 'staff_v2',
            'leave_requests:staff_id' => 'staff',
            'leave_requests:leave_type_id' => 'leave_types',
            'leave_requests:approved_by_staff_id' => 'staff',
            'meetings:meeting_type_id' => 'meeting_types',
            'meetings:location_id' => 'locations',
            'meetings:created_by_staff_id' => 'staff',
            'group_members:group_id' => 'groups',
            'group_members:staff_id' => 'staff',
            'meeting_participants:meeting_id' => 'meetings',
            'meeting_participants:staff_id' => 'staff',
            'meeting_agendas:meeting_id' => 'meetings',
            'meeting_agendas:presenter_staff_id' => 'staff',
            'staff:employment_status' => 'employment_statuses',
        ];

        $rows = DB::select(
            'SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME IS NOT NULL'
        );

        $actual = [];

        foreach ($rows as $row) {
            $actual[strtolower($row->TABLE_NAME).':'.strtolower($row->COLUMN_NAME)] = strtolower($row->REFERENCED_TABLE_NAME);
        }

        foreach ($expected as $column => $target) {
            $this->assertArrayHasKey($column, $actual, "Missing FK on {$column}");
            $this->assertSame($target, $actual[$column], "FK on {$column} points to the wrong table");
        }

        $this->assertCount(count($expected), $actual, 'Unexpected number of foreign keys');
    }

    /*
    |--------------------------------------------------------------------------
    | Unique constraints
    |--------------------------------------------------------------------------
    */

    public function test_department_code_must_be_unique(): void
    {
        Department::create(['code' => 'DEP-X', 'name' => 'A']);

        $this->expectException(QueryException::class);
        Department::create(['code' => 'DEP-X', 'name' => 'B']);
    }

    public function test_staff_code_must_be_unique(): void
    {
        Staff::create(['staff_code' => 'ST-X', 'name_km' => 'A']);

        $this->expectException(QueryException::class);
        Staff::create(['staff_code' => 'ST-X', 'name_km' => 'B']);
    }

    public function test_a_staff_member_can_only_have_one_attendance_record_per_day(): void
    {
        $staff = StaffV2::factory()->create();

        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-05',
            'status' => Attendance::STATUS_PRESENT,
        ]);

        $this->expectException(QueryException::class);
        Attendance::create([
            'staff_id' => $staff->id,
            'work_date' => '2026-10-05',
            'status' => Attendance::STATUS_ABSENT,
        ]);
    }

    public function test_a_staff_member_cannot_join_the_same_group_twice(): void
    {
        $staff = Staff::factory()->create();
        $group = Group::factory()->create();

        GroupMember::create(['group_id' => $group->id, 'staff_id' => $staff->id]);

        $this->expectException(QueryException::class);
        GroupMember::create(['group_id' => $group->id, 'staff_id' => $staff->id]);
    }

    public function test_agenda_numbers_must_be_unique_inside_one_meeting(): void
    {
        $meeting = Meeting::factory()->create();

        MeetingAgenda::create(['meeting_id' => $meeting->id, 'agenda_no' => 1, 'title' => 'A']);

        $this->expectException(QueryException::class);
        MeetingAgenda::create(['meeting_id' => $meeting->id, 'agenda_no' => 1, 'title' => 'B']);
    }

    public function test_a_staff_member_cannot_be_registered_twice_for_one_meeting(): void
    {
        $staff = Staff::factory()->create();
        $meeting = Meeting::factory()->create();

        MeetingParticipant::create(['meeting_id' => $meeting->id, 'staff_id' => $staff->id]);

        $this->expectException(QueryException::class);
        MeetingParticipant::create(['meeting_id' => $meeting->id, 'staff_id' => $staff->id]);
    }

    public function test_one_user_account_can_only_be_linked_to_one_staff_record(): void
    {
        $user = User::factory()->create();

        Staff::factory()->create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);
        Staff::factory()->create(['user_id' => $user->id]);
    }
}
