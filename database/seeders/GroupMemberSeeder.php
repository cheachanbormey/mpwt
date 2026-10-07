<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    /**
     * Seed the group_members table.
     */
    public function run(): void
    {
        $allStaff = Staff::pluck('id')->all();

        if ($allStaff === []) {
            return;
        }

        // Safe to run repeatedly: do not add more rows on a second `db:seed`.
        if (GroupMember::exists()) {
            return;
        }

        foreach (Group::all() as $group) {
            // The leader is always a member of their own group.
            $members = $group->leader_staff_id ? [$group->leader_staff_id] : [];

            // Add 4-7 further members.
            $pool = array_values(array_diff($allStaff, $members));
            shuffle($pool);

            $count = fake()->numberBetween(4, 7);

            foreach (array_slice($pool, 0, $count) as $staffId) {
                $members[] = $staffId;
            }

            $roles = ['សមាជិក', 'រដ្ឋលេខាធិការ', 'សេកា', 'អ្នកជំនួយ'];

            foreach (array_unique($members) as $staffId) {
                GroupMember::firstOrCreate(
                    [
                        'group_id' => $group->id,
                        'staff_id' => $staffId,
                    ],
                    [
                        'role_in_group' => fake()->randomElement($roles),
                        'is_leader' => $staffId === $group->leader_staff_id,
                        'joined_at' => fake()->dateTimeBetween('-4 years', '-1 month'),
                    ]
                );
            }
        }
    }
}
