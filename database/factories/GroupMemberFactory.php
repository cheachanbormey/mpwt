<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupMember>
 */
class GroupMemberFactory extends Factory
{
    protected $model = GroupMember::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'staff_id' => Staff::factory(),
            'role_in_group' => fake()->randomElement(['សមាជិក', 'រដ្ឋលេខាធិការ', 'សេកា', 'អ្នកជំនួយ']),
            'is_leader' => false,
            'joined_at' => fake()->dateTimeBetween('-4 years', '-1 month'),
        ];
    }

    /**
     * The leader of the group.
     */
    public function leader(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_leader' => true,
            'role_in_group' => 'ប្រធាន',
        ]);
    }

    /**
     * Attach an existing member of staff to an existing group.
     */
    public function forGroupAndStaff(Group $group, Staff $staff): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => $group->getKey(),
            'staff_id' => $staff->getKey(),
        ]);
    }
}
