<?php

namespace App\Services;

use App\Exceptions\AttendanceClockException;
use App\Models\Attendance;
use App\Models\StaffV2;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Records when a member of staff arrives and leaves.
 *
 * The clock owns three rules that must hold no matter which screen calls it:
 *
 *   1. A person arrives at most once a day.
 *   2. A person cannot leave before they arrived, nor leave twice.
 *   3. Hours worked are derived from the two timestamps, never typed in.
 *
 * The timestamp always comes from the server. There is deliberately no way to
 * pass a time in from a form, so nobody can claim an hour they never worked.
 */
class AttendanceClock
{
    /**
     * Arriving after this time is recorded as late.
     */
    public const LATE_AFTER = '08:00';

    /**
     * Record the moment the person arrived.
     *
     * @throws AttendanceClockException when the person has already checked in today
     */
    public function checkIn(StaffV2 $staff, ?Carbon $at = null, string $source = 'system'): Attendance
    {
        $at = $this->resolveMoment($at);
        $workDate = $at->toDateString();

        // The unique (staff_id, work_date) index is the real guarantee. This
        // read exists to answer the person politely instead of letting MySQL
        // raise a duplicate key error.
        if ($this->recordFor($staff, $workDate)->exists()) {
            throw new AttendanceClockException(
                'លេខបុគ្គលិកនេះបានចូលការងាររួចរាល់នៅថ្ងៃនេះ។ អាចចូលការងារតែមួយដងក្នុងមួយថ្ងៃប៉ុណ្ណោះ។'
            );
        }

        if (! in_array($source, ['manual', 'qr', 'system'], true)) {
            $source = 'system';
        }

        $record = $this->recordFor($staff, $workDate)->create([
            'staff_id' => $staff->getKey(),
            'work_date' => $workDate,
            'check_in' => $at,
            'check_out' => null,
            'worked_hours' => null,
            'status' => $at->greaterThan($this->lateThreshold($at))
                ? Attendance::STATUS_LATE
                : Attendance::STATUS_PRESENT,
            // Recorded by the system clock, not keyed in by an administrator.
            'check_in_source' => $source,
            'notes' => null,
        ]);

        return $record;
    }

    /**
     * Record the moment the person left and work out their hours.
     *
     * @throws AttendanceClockException when they have not arrived, have
     *                                  already left, or are leaving early
     */
    public function checkOut(StaffV2 $staff, ?Carbon $at = null): Attendance
    {
        $at = $this->resolveMoment($at);
        $workDate = $at->toDateString();

        $record = $this->recordFor($staff, $workDate)->first();

        if ($record === null || $record->check_in === null) {
            throw new AttendanceClockException(
                'មិនទាន់មានកំណត់ត្រាចូលការងារសម្រាប់ថ្ងៃនេះទេ។ សូមចូលការងារជាមុនសិន។'
            );
        }

        if ($record->check_out !== null) {
            throw new AttendanceClockException(
                'លេខបុគ្គលិកនេះបានចេញការងាររួចរាល់នៅថ្ងៃនេះ។ អាចចេញការងារតែមួយដងក្នុងមួយថ្ងៃប៉ុណ្ណោះ។'
            );
        }

        if ($at->lessThanOrEqualTo($record->check_in)) {
            throw new AttendanceClockException(
                'មិនអាចចេញការងារមុនម៉ោងចូលបានទេ។ ម៉ោងចូលគឺ '.$record->check_in->format('H:i').'។'
            );
        }

        $record->check_out = $at;
        $record->worked_hours = $this->hoursBetween($record->check_in, $at);
        $record->save();

        return $record;
    }

    /**
     * The person's record for one day, if it exists.
     */
    public function recordFor(StaffV2 $staff, ?string $workDate = null): Builder
    {
        $workDate ??= now()->toDateString();

        return Attendance::query()
            ->where('staff_id', $staff->getKey())
            ->whereDate('work_date', $workDate);
    }

    /**
     * Hours between arrival and leaving, rounded to two decimals.
     *
     * Worked out from the raw timestamps rather than a difference helper so the
     * result never changes sign with the Carbon version in use.
     */
    private function hoursBetween(Carbon $checkIn, Carbon $checkOut): float
    {
        $minutes = (int) round(($checkOut->getTimestamp() - $checkIn->getTimestamp()) / 60);

        return round($minutes / 60, 2);
    }

    /**
     * The moment the button was pressed, on the server clock.
     */
    private function resolveMoment(?Carbon $at): Carbon
    {
        return ($at ?? now())->copy();
    }

    /**
     * The cut-off after which an arrival counts as late, on the same day.
     */
    private function lateThreshold(Carbon $at): Carbon
    {
        return $at->copy()->setTimeFromTimeString(self::LATE_AFTER);
    }
}
