<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AttendanceDayRequest;
use App\Http\Requests\Admin\AttendanceMonthRequest;
use App\Models\Attendance;
use App\Models\Office;
use App\Models\PositionV2;
use App\Models\StaffV2;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Attendance overviews for an administrator.
 *
 * The row-by-row record list, its search and its filters are handled by the
 * generic module controller. These two screens answer the questions that a
 * flat list cannot: how did one specific day go, and how did a whole month go
 * per person.
 */
class AttendanceReportController extends Controller
{
    /**
     * How many rows one page of either report holds.
     */
    private const PER_PAGE = 25;

    /**
     * Every record on one day, with a roll-up of that day.
     */
    public function day(AttendanceDayRequest $request): View
    {
        $day = Carbon::parse($request->day());

        $records = Attendance::query()
            ->with('staff')
            ->whereDate('work_date', $day->toDateString())
            ->when($request->validated('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->validated('q'), function ($q, $term) {
                $q->whereHas('staff', fn ($staff) => $staff
                    ->where('full_name', 'like', '%'.$term.'%')
                    ->orWhere('staff_id', 'like', '%'.$term.'%'));
            })
            ->orderBy('staff_id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Totals come from the whole day, not the current page, so the summary
        // stays true when the day is longer than one page.
        $dayTotals = Attendance::query()
            ->whereDate('work_date', $day->toDateString())
            ->selectRaw('status, COUNT(*) as total, SUM(worked_hours) as hours')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('admin.attendance.day', [
            'day' => $day,
            'records' => $records,
            'filters' => $request->validated(),
            'summary' => $this->summaryFrom($dayTotals),
        ]);
    }

    /**
     * One row per member of staff for a whole month.
     */
    public function month(AttendanceMonthRequest $request): View
    {
        $month = Carbon::parse($request->month().'-01');

        $rows = Staff::query()
            ->with(['office', 'position'])
            ->when($request->validated('office_id'), fn ($q, $officeId) => $q->where('office_id', $officeId))
            ->when($request->validated('position_id'), fn ($q, $positionId) => $q->where('position_id', $positionId))
            // Only people who actually clocked on during the month.
            ->whereHas('attendances', fn ($q) => $q->inMonth($month->format('Y-m')))
            ->withSum(['attendances as hours_total' => fn ($q) => $q->inMonth($month->format('Y-m'))], 'worked_hours')
            ->withCount(['attendances as present_count' => fn ($q) => $q
                ->inMonth($month->format('Y-m'))
                ->where('status', Attendance::STATUS_PRESENT)])
            ->withCount(['attendances as late_count' => fn ($q) => $q
                ->inMonth($month->format('Y-m'))
                ->where('status', Attendance::STATUS_LATE)])
            ->withCount(['attendances as absent_count' => fn ($q) => $q
                ->inMonth($month->format('Y-m'))
                ->where('status', Attendance::STATUS_ABSENT)])
            ->withCount(['attendances as leave_count' => fn ($q) => $q
                ->inMonth($month->format('Y-m'))
                ->where('status', Attendance::STATUS_LEAVE)])
            ->orderBy('name_km')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.attendance.month', [
            'month' => $month,
            'rows' => $rows,
            'filters' => $request->validated(),
            'offices' => Office::query()->orderBy('name_kh')->pluck('name_kh', 'id'),
            'positions' => Position::query()->orderBy('name_kh')->pluck('name_kh', 'id'),
        ]);
    }

    /**
     * Fold per-status totals into one set of headline numbers.
     *
     * @param  Collection<string, object>  $totals
     * @return array<string, float|int>
     */
    private function summaryFrom($totals): array
    {
        $summary = [
            'total' => 0,
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'leave' => 0,
            'holiday' => 0,
            'weekend' => 0,
            'hours' => 0.0,
        ];

        foreach ($totals as $status => $row) {
            $summary[$status] = (int) $row->total;
            $summary['total'] += (int) $row->total;
            $summary['hours'] += (float) $row->hours;
        }

        $summary['hours'] = round($summary['hours'], 1);

        return $summary;
    }
}
