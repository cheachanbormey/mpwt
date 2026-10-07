<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\StaffV2;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Seed the attendances table with 45 working days of history.
     */
    public function run(): void
    {
        // Attendance rows are keyed to the rebuilt staff table, so seeding
        // against the legacy list would write foreign keys that point nowhere.
        $staff = StaffV2::whereNotNull('office_id')->get();

        if ($staff->isEmpty()) {
            return;
        }

        $rows = [];

        // Walk back 60 calendar days and keep weekdays only.
        $date = now()->subDays(60)->startOfDay();

        while ($date->isFuture() === false && $date->greaterThanOrEqualTo(now()->subDays(60)->startOfDay())) {
            if ($date->isWeekday()) {
                foreach ($staff as $person) {
                    $roll = fake()->randomFloat(2, 0, 100);

                    $status = match (true) {
                        $roll < 3 => Attendance::STATUS_ABSENT,
                        $roll < 10 => Attendance::STATUS_LEAVE,
                        $roll < 25 => Attendance::STATUS_LATE,
                        default => Attendance::STATUS_PRESENT,
                    };

                    $isWorking = in_array($status, [
                        Attendance::STATUS_PRESENT,
                        Attendance::STATUS_LATE,
                    ], true);

                    $checkIn = $date->copy()->setTime(
                        $status === Attendance::STATUS_LATE ? 8 : 7,
                        fake()->numberBetween(0, 59)
                    );

                    $checkOut = $isWorking
                        ? $checkIn->copy()->addHours(fake()->numberBetween(8, 9))
                        : null;

                    $rows[] = [
                        'staff_id' => $person->id,
                        'work_date' => $date->toDateString(),
                        'check_in' => $isWorking ? $checkIn : null,
                        'check_out' => $checkOut,
                        'worked_hours' => $isWorking
                            ? round(($checkOut->diffInMinutes($checkIn)) / 60, 2)
                            : null,
                        'status' => $status,
                        'check_in_source' => fake()->randomElement(['manual', 'qr', 'system']),
                        'notes' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $date->addDay();
        }

        // Insert in chunks to stay clear of the max_allowed_packet limit.
        // upsert() keeps the seeder safe to run more than once because
        // (staff_id, work_date) is a unique key.
        foreach (array_chunk($rows, 500) as $chunk) {
            Attendance::upsert(
                $chunk,
                ['staff_id', 'work_date'],
                ['check_in', 'check_out', 'worked_hours', 'status', 'check_in_source', 'notes', 'updated_at']
            );
        }
    }
}
