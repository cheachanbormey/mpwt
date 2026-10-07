<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Order matters: every seeder depends on the ones above it.
     */
    public function run(): void
    {
        // Baseline login account (kept from Phase 01)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Layer 1 - lookup tables with no dependencies
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            LocationSeeder::class,
            LeaveTypeSeeder::class,
            MeetingTypeSeeder::class,
            DepartmentV2Seeder::class,
            PositionV2Seeder::class,
        ]);

        // Layer 2 - offices depend on departments
        $this->call([
            OfficeSeeder::class,
            OfficeV2Seeder::class,
        ]);

        // Layer 3 - staff depends on offices, positions and users
        $this->call([
            StaffSeeder::class,
            // Attendance rows point at this table, so it must be populated
            // before Layer 4 runs.
            StaffV2Seeder::class,
        ]);

        // Layer 4 - depends on staff
        $this->call([
            GroupSeeder::class,
            AttendanceSeeder::class,
        ]);

        // Layer 5 - depends on staff plus lookup tables
        $this->call([
            LeaveRequestSeeder::class,
            MeetingSeeder::class,
        ]);

        // Layer 6 - junction tables
        $this->call([
            GroupMemberSeeder::class,
            MeetingParticipantSeeder::class,
            MeetingAgendaSeeder::class,
        ]);

        // Layer 7 - roles, once staff records exist to link accounts to
        $this->call(RoleSeeder::class);

        // Layer 8 - mirrors the account links RoleSeeder just worked out onto
        // the rebuilt staff table the clock reads from
        $this->call(StaffV2UserLinkSeeder::class);
    }
}
