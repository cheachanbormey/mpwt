<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\PositionV2;
use App\Models\StaffV2;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffV2>
 */
class StaffV2Factory extends Factory
{
    protected $model = StaffV2::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'office_id' => null,
            'position_id' => null,
            'staff_id' => 'ST-'.fake()->unique()->numerify('#####'),
            'full_name' => fake()->name('kh_KH'),
            'gender' => fake()->randomElement(['male', 'female']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('0########'),
            'telegram' => fake()->userName(),
            'status' => StaffV2::STATUS_ACTIVE,
            'system_role' => StaffV2::ROLE_STAFF,
        ];
    }

    /**
     * Staff member who has left the ministry.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StaffV2::STATUS_INACTIVE,
        ]);
    }

    /**
     * Female staff member.
     */
    public function female(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'female',
        ]);
    }

    /**
     * Staff member holding the given position.
     */
    public function forPosition(PositionV2 $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position_id' => $position->getKey(),
        ]);
    }

    /**
     * Staff member working in the given office.
     */
    public function forOffice(Office $office): static
    {
        return $this->state(fn (array $attributes) => [
            'office_id' => $office->getKey(),
        ]);
    }

    /**
     * Staff member who administers the system.
     */
    public function administrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'system_role' => StaffV2::ROLE_ADMIN,
        ]);
    }

    /**
     * Staff member reachable only through their telegram handle.
     */
    public function withoutEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => null,
        ]);
    }
}
