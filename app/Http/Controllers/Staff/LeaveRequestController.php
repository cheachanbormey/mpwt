<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Staff self-service for leave requests.
 *
 * IMPORTANT
 * ---------
 * This application has NO online approval workflow. A request is created
 * with the "pending" status, printed on paper, and signed by a supervisor.
 * Nothing in this controller (or anywhere else) can approve, reject or
 * cancel a request, and `approved_by_staff_id` / `approved_at` /
 * `rejection_reason` are never written by the application.
 *
 * Do not add an approve() or reject() method here.
 */
class LeaveRequestController extends Controller
{
    use ResolvesCurrentStaff;

    /**
     * Show the signed-in person's own leave requests.
     */
    public function index(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:'.implode(',', [
                LeaveRequest::STATUS_PENDING,
                LeaveRequest::STATUS_APPROVED,
                LeaveRequest::STATUS_REJECTED,
                LeaveRequest::STATUS_CANCELLED,
            ])],
        ]);

        $requests = $staff->leaveRequests()
            ->with('leaveType')
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('staff.leave-requests.index', [
            'staff' => $staff,
            'leaveRequests' => $requests,
            'filters' => $validated,
        ]);
    }

    /**
     * Show the submission form.
     */
    public function create(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        return view('staff.leave-requests.create', [
            'staff' => $staff,
            'leaveTypes' => LeaveType::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new request. It is always created as "pending".
     */
    public function store(Request $request): RedirectResponse
    {
        $staff = $this->currentStaffOrFail($request);

        $validated = $request->validate([
            'leave_type_id' => ['required', 'integer', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'leave_type_id.required' => 'សូមជ្រើសរើសប្រភេទសំឡេង។',
            'start_date.required' => 'សូមបញ្ចូលកាលបរិច្ឆេទចាប់ផ្ដើម។',
            'end_date.required' => 'សូមបញ្ចូលកាលបរិច្ឆេទបញ្ចប់។',
            'end_date.after_or_equal' => 'កាលបរិច្ឆេទបញ្ចប់ត្រូវមានយ៉ាងតិចកាលបរិច្ឆេទចាប់ផ្ដើម។',
            'reason.required' => 'សូមសរសេរមូលដ្ឋានសំឡេង។',
            'reason.min' => 'មូលដ្ឋានត្រូវមានយ៉ាងតិច ៥ តួអក្សរ។',
        ]);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();

        $totalDays = $this->countWorkingDays($start, $end);

        if ($totalDays < 1) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'ជួរកាលបរិច្ឆេទដែលជ្រើសរើសគឺជាថ្ងៃសំឡេងសុខទំនោរទាំងអស់ ដូច្នេះមិនមានថ្ងៃសំឡេង។',
                ]);
        }

        // Overlap check against this person's own existing requests.
        $overlaps = $staff->leaveRequests()
            ->overlapping($start, $end)
            ->exists();

        if ($overlaps) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_date' => 'រក្សាសំឡេងនេះជានិងសំឡេងដែលបានស្នើរួចរាល់នោះមានថ្ងៃជាន់គ្នា។',
                ]);
        }

        LeaveRequest::create([
            'staff_id' => $staff->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $start,
            'end_date' => $end,
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            // Always pending. The paper form is what gets signed.
            'status' => LeaveRequest::STATUS_PENDING,
        ]);

        return redirect()->route('staff.leave-requests.index')
            ->with('success', 'សំឡេងត្រូវបានបញ្ជូន។ សូមបោះពុម្ពដើម្បីយកទៅចុចហត្ថលេខាដោយអ្នកគ្រប់គ្រង។');
    }

    /**
     * Count Monday-Friday days between two dates, inclusive.
     *
     * Saturday and Sunday are not deducted as leave days.
     */
    private function countWorkingDays(Carbon $start, Carbon $end): int
    {
        $days = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekday()) {
                $days++;
            }
        }

        return $days;
    }
}
