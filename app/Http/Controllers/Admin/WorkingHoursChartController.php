<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WorkingHoursChartController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->query('period', '14');

        if (! in_array($period, ['7', '14', 'month'], true)) {
            $period = '14';
        }

        $today = Carbon::today();

        $startDate = match ($period) {
            '7' => $today->copy()->subDays(6),
            'month' => $today->copy()->startOfMonth(),
            default => $today->copy()->subDays(13),
        };

        $records = Attendance::query()
            ->whereBetween('work_date', [
                $startDate->toDateString(),
                $today->toDateString(),
            ])
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->orderBy('work_date')
            ->get([
                'work_date',
                'check_in',
                'check_out',
            ]);

        // Group records by attendance date.
        $dailyRecords = $records->groupBy(
            fn ($record) => Carbon::parse(
                $record->work_date
            )->toDateString()
        );

        $labels = [];
        $hours = [];

        for (
            $date = $startDate->copy();
            $date->lte($today);
            $date->addDay()
        ) {
            $key = $date->toDateString();

            $labels[] = $date->format('j M');

            $dayRecords = $dailyRecords->get($key, collect());

            if ($dayRecords->isEmpty()) {
                $hours[] = null;
                continue;
            }

            $totalMinutes = $dayRecords->sum(function ($record) {
                $checkIn = Carbon::parse($record->check_in);
                $checkOut = Carbon::parse($record->check_out);

                // Support shifts that finish after midnight.
                if ($checkOut->lt($checkIn)) {
               $checkOut->addDay();
                }

                return $checkIn->diffInMinutes($checkOut);
            });

            // Average hours per attendance record for that day.
            $hours[] = round(
                $totalMinutes / $dayRecords->count() / 60,
                2
            );
        }

        $validHours = collect($hours)
            ->filter(fn ($value) => $value !== null);

        $averageHours = $validHours->isNotEmpty()
            ? round($validHours->avg(), 1)
            : 0;

        $maxHours = $validHours->isNotEmpty()
            ? round($validHours->max(), 1)
            : 0;

        return view('admin.attendance.working-hours', [
            'labels' => $labels,
            'hours' => $hours,
            'averageHours' => $averageHours,
            'maxHours' => $maxHours,
            'period' => $period,
        ]);
    }
}
