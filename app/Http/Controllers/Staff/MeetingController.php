<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingController extends Controller
{
    use ResolvesCurrentStaff;

    /**
     * Meetings the signed-in person is registered for, or has been invited to.
     *
     * "Applicable" means any meeting linked to this person through the
     * meeting_participants junction table, including meetings they created.
     */
    public function index(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:'.implode(',', [
                Meeting::STATUS_SCHEDULED,
                Meeting::STATUS_ONGOING,
                Meeting::STATUS_COMPLETED,
                Meeting::STATUS_CANCELLED,
            ])],
            'upcoming' => ['nullable', 'boolean'],
        ]);

        $meetings = $staff->meetings()
            ->with(['meetingType', 'location'])
            ->when($validated['status'] ?? null, fn ($q, $status) => $q->where('meetings.status', $status))
            ->when($validated['upcoming'] ?? false, fn ($q) => $q->whereDate('meeting_date', '>=', today()))
            ->orderBy('meeting_date')
            ->orderBy('start_time')
            ->paginate(15)
            ->withQueryString();

        return view('staff.meetings', [
            'staff' => $staff,
            'meetings' => $meetings,
            'filters' => $validated,
        ]);
    }

    /**
     * Show one meeting this person is registered for.
     */
    public function show(Request $request, int $meeting): View
    {
        $staff = $this->currentStaffOrFail($request);

        $model = Meeting::query()
            ->with(['meetingType', 'location', 'agendas.presenter', 'participants.staff'])
            ->findOrFail($meeting);

        // Authorisation: registered as a participant, or the creator.
        $registered = $model->participants()
            ->where('staff_id', $staff->id)
            ->exists();

        if (! $registered && $model->created_by_staff_id !== $staff->id) {
            abort(403, 'អ្នកមិនត្រូវបានចុះឈ្មោះក្នុងកិច្ចប្រជុំនេះទេ។');
        }

        $participation = MeetingParticipant::query()
            ->where('meeting_id', $model->id)
            ->where('staff_id', $staff->id)
            ->first();

        return view('staff.meetings-show', [
            'staff' => $staff,
            'meeting' => $model,
            'participation' => $participation,
        ]);
    }
}
