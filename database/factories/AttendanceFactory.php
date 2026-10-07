<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\StaffV2;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-30 days', 'now')->setTime(fake()->numberBetween(7, 9), fake()->numberBetween(0, 59));

        return [
            'staff_id' => StaffV2::factory(),
            'work_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'check_in' => $checkIn,
            'check_out' => (clone $checkIn)->modify('+'.fake()->numberBetween(7, 9).' hours'),
            'worked_hours' => fake()->randomFloat(2, 7, 9.5),
            'status' => Attendance::STATUS_PRESENT,
            'check_in_source' => fake()->randomElement(['manual', 'qr', 'system']),
            'notes' => null,
        ];
    }

    /**
     * Marked as absent.
     */
    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_in' => null,
            'check_out' => null,
            'worked_hours' => null,
            'status' => Attendance::STATUS_ABSENT,
        ]);
    }

    /**
     * Marked as late.
     */
    public function late(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Attendance::STATUS_LATE,
        ]);
    }

    /**
     * Attendance for one specific day.
     */
    public function on(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'work_date' => $date,
        ]);
    }
}
