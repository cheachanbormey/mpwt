<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LeaveRequestSeeder extends Seeder
{
    /**
     * Seed the leave_requests table.
     */
    public function run(): void
    {
        $staff = Staff::all();
        $leaveTypes = LeaveType::all();

        if ($staff->isEmpty() || $leaveTypes->isEmpty()) {
            return;
        }

        // Safe to run repeatedly: do not add more rows on a second `db:seed`.
        if (LeaveRequest::exists()) {
            return;
        }

        // Managers used as approvers.
        $approvers = Staff::whereIn('employment_status', [
            Staff::STATUS_ACTIVE,
            Staff::STATUS_PROBATION,
        ])->get();

        foreach ($staff as $person) {
            // Keep the seeder safe to run repeatedly.
            if ($person->leaveRequests()->exists()) {
                continue;
            }

            $requests = fake()->numberBetween(0, 3);

            for ($i = 0; $i < $requests; $i++) {
                $type = $leaveTypes->random();

                // Faker returns a plain DateTime, so convert to Carbon first.
                $start = Carbon::instance(fake()->dateTimeBetween('-6 months', '+2 months'));
                $end = Carbon::instance(fake()->dateTimeBetween($start, $start->copy()->addDays(7)));

                $status = fake()->randomElement([
                    LeaveRequest::STATUS_APPROVED,
                    LeaveRequest::STATUS_PENDING,
                    LeaveRequest::STATUS_REJECTED,
                    LeaveRequest::STATUS_CANCELLED,
                ]);

                $decided = in_array($status, [
                    LeaveRequest::STATUS_APPROVED,
                    LeaveRequest::STATUS_REJECTED,
                ], true);

                // A decision can never be recorded before the leave starts.
                $approvedAt = null;

                if ($decided) {
                    $approvedAt = $start->isFuture()
                        ? now()
                        : Carbon::instance(fake()->dateTimeBetween($start->copy(), now()));
                }

                LeaveRequest::create([
                    'staff_id' => $person->id,
                    'leave_type_id' => $type->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'total_days' => $start->diffInDays($end) + 1,
                    'reason' => fake()->sentence(10),
                    'status' => $status,
                    'approved_by_staff_id' => $decided ? $approvers->random()->id : null,
                    'approved_at' => $approvedAt,
                    'rejection_reason' => $status === LeaveRequest::STATUS_REJECTED
                        ? 'មិនអាចអនុម័តបានដោយសារការងាររបស់សារីរុង'
                        : null,
                ]);
            }
        }
    }
}
