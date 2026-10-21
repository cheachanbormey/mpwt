<?php

namespace App\Http\Controllers\Staff;

use App\Exceptions\AttendanceClockException;
use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceClock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    use ResolvesCurrentStaff;

    public function __construct(
        private readonly AttendanceClock $clock
    ) {
        //
    }

    /**
     * Show the signed-in person's attendance, plus today's clock buttons.
     */
    public function index(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'string', 'in:'.implode(',', [
                Attendance::STATUS_PRESENT,
                Attendance::STATUS_ABSENT,
                Attendance::STATUS_LATE,
                Attendance::STATUS_LEAVE,
            ])],
        ]);

        // The staff id always comes from the session, never from the query string.
        $query = $staff->attendances()
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('work_date', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('work_date', '<=', $to))
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->orderByDesc('work_date');

        $attendances = $query->paginate(20)->withQueryString();

        $summary = [
            'total' => $staff->attendances()->count(),
            'present' => $staff->attendances()->where('status', Attendance::STATUS_PRESENT)->count(),
            'late' => $staff->attendances()->where('status', Attendance::STATUS_LATE)->count(),
            'absent' => $staff->attendances()->where('status', Attendance::STATUS_ABSENT)->count(),
            'leave' => $staff->attendances()->where('status', Attendance::STATUS_LEAVE)->count(),
            'hours' => (float) $staff->attendances()->sum('worked_hours'),
        ];

        return view('staff.attendance', [
            'staff' => $staff,
            'attendances' => $attendances,
            'summary' => $summary,
            'filters' => $validated,
            'today' => $this->clock->recordFor($staff)->first(),
        ]);
    }

    /**
     * Record the moment the person arrived.
     *
     * The time is taken from the server, so the form carries no timestamp.
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $staff = $this->currentStaffOrFail($request);

        try {
            $this->clock->checkIn($staff);
        } catch (AttendanceClockException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'បានចូលការងារដោយជោគជ័យ។ សូមកុំប្រុងប្រយ័ត្នចូលការងារម្តងទៀតទេ។');
    }

    /**
     * Check-in via QR code.
     */
    public function checkInQr(Request $request): RedirectResponse
    {
        $staff = $this->currentStaffOrFail($request);

        try {
            $this->clock->checkIn($staff, null, 'qr');
        } catch (AttendanceClockException $exception) {
            return redirect()->route('staff.attendance')->with('error', $exception->getMessage());
        }

        return redirect()->route('staff.attendance')->with('success', 'បានចូលការងារដោយ QR Code ដោយជោគជ័យ។');
    }

    /**
     * Record the moment the person left and work out their hours.
     */
    public function checkOut(Request $request): RedirectResponse
    {
        $staff = $this->currentStaffOrFail($request);

        try {
            $attendance = $this->clock->checkOut($staff);
        } catch (AttendanceClockException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'បានចេញការងារដោយជោគជ័យ។ ម៉ោងធ្វើការសរុប '.$attendance->worked_hours.' ម៉ោង។');
    }

    /**
     * Generate QR code for check-in.
     */
    public function qrCode(Request $request): Response
    {
        $this->currentStaffOrFail($request);

        $url = route('staff.staff.attendance.check-in.qr');
        $qr = QrCode::format('svg')->size(250)->margin(1)->generate($url);

        return response($qr, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Show QR code page for staff.
     */
    public function showQr(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        return view('staff.attendance-qr', [
            'staff' => $staff,
            'qrUrl' => route('staff.staff.attendance.qr.image'),
            'checkInUrl' => route('staff.staff.attendance.check-in.qr'),
        ]);
    }
}
