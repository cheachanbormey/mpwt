<?php

namespace Database\Factories;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Faker needs absolute dates, so build the range with Carbon.
        $start = Carbon::instance(fake()->dateTimeBetween('-3 months', '+3 months'));
        $end = Carbon::instance(fake()->dateTimeBetween($start->copy(), $start->copy()->addDays(5)));

        return [
            'staff_id' => Staff::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $start->diffInDays($end) + 1,
            'reason' => fake()->sentence(10),
            'status' => LeaveRequest::STATUS_PENDING,
            'approved_by_staff_id' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ];
    }

    /**
     * An approved request.
     */
    public function approved(?Staff $approver = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaveRequest::STATUS_APPROVED,
            'approved_by_staff_id' => $approver?->getKey(),
            'approved_at' => now(),
        ]);
    }

    /**
     * A rejected request.
     */
    public function rejected(?Staff $approver = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaveRequest::STATUS_REJECTED,
            'approved_by_staff_id' => $approver?->getKey(),
            'approved_at' => now(),
            'rejection_reason' => fake()->sentence(8),
        ]);
    }
}
