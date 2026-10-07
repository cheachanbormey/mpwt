<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\LeaveRequest;
use App\Models\Meeting;
use App\Models\MeetingParticipant;
use App\Models\Office;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesCurrentStaff;

    /**
     * The dashboard. Administrators see system-wide figures, staff see
     * their own.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard.index', [
            'user' => $user,
            'stats' => $user->isAdmin() ? $this->adminStats() : $this->staffStats($user),
            'attendances' => $user->isAdmin()
                ? $this->recentAttendance()
                : $this->ownRecentAttendance($user),
            'upcomingMeetings' => $user->isAdmin()
                ? $this->upcomingMeetings()
                : $this->ownUpcomingMeetings($user),
            'pendingLeave' => $user->isAdmin()
                ? LeaveRequest::pending()->with(['staff', 'leaveType'])->latest()->limit(5)->get()
                : collect(),
        ]);
    }

    /**
     * System-wide figures.
     *
     * @return array<string, mixed>
     */
    private function adminStats(): array
    {
        $today = today();

        return [
            'headline' => [
                ['label' => 'បុគ្គលិកសរុប', 'value' => Staff::count(), 'icon' => 'bi-people', 'class' => 'blue'],
                ['label' => 'ការងារ', 'value' => Office::count(), 'icon' => 'bi-building', 'class' => 'purple'],
                ['label' => 'វត្តមានថ្ងៃនេះ', 'value' => Attendance::whereDate('work_date', $today)->count(), 'icon' => 'bi-calendar-check', 'class' => 'orange'],
                ['label' => 'សុំច្បាប់កំពុងរង់ចាំ', 'value' => LeaveRequest::pending()->count(), 'icon' => 'bi-envelope-paper', 'class' => 'gray'],
            ],
            'secondary' => [
                ['label' => 'ក្រុមការងារ', 'value' => Group::count(), 'icon' => 'bi-people-fill', 'class' => 'purple'],
                ['label' => 'កិច្ចប្រជុំ', 'value' => Meeting::count(), 'icon' => 'bi-calendar-event', 'class' => 'blue'],
                ['label' => 'អ្នកចូលរួម', 'value' => MeetingParticipant::count(), 'icon' => 'bi-person-check', 'class' => 'orange'],
                ['label' => 'ការងារសកម្ម', 'value' => Staff::active()->count(), 'icon' => 'bi-person-badge', 'class' => 'gray'],
            ],
        ];
    }

    /**
     * Figures for one person's own record.
     *
     * @return array<string, mixed>
     */
    private function staffStats(User $user): array
    {
        $staff = $user->staff;

        if ($staff === null) {
            return ['headline' => [], 'secondary' => []];
        }

        // Attendance rows belong to the rebuilt staff module, so the totals
        // are counted there rather than against the legacy record.
        $attendance = $user->staffV2;

        return [
            'headline' => [
                ['label' => 'វត្តមាន', 'value' => $attendance?->attendances()->count() ?? 0, 'icon' => 'bi-journal-check', 'class' => 'blue'],
                ['label' => 'យឺត', 'value' => $attendance?->attendances()->where('status', Attendance::STATUS_LATE)->count() ?? 0, 'icon' => 'bi-clock', 'class' => 'orange'],
                ['label' => 'អវត្តមាន', 'value' => $attendance?->attendances()->where('status', Attendance::STATUS_ABSENT)->count() ?? 0, 'icon' => 'bi-x-circle', 'class' => 'gray'],
                ['label' => 'ម៉ោងធ្វើការសរុប', 'value' => round((float) ($attendance?->attendances()->sum('worked_hours') ?? 0), 1), 'icon' => 'bi-hourglass-split', 'class' => 'purple'],
            ],
            'secondary' => [
                ['label' => 'សំុច្បាប់ទាំងអស់', 'value' => $staff->leaveRequests()->count(), 'icon' => 'bi-envelope-paper', 'class' => 'blue'],
                ['label' => 'កំពុងរង់ចាំ', 'value' => $staff->leaveRequests()->pending()->count(), 'icon' => 'bi-hourglass', 'class' => 'orange'],
                ['label' => 'កិច្ចប្រជុំ', 'value' => $staff->meetings()->count(), 'icon' => 'bi-calendar-event', 'class' => 'purple'],
                ['label' => 'ក្រុមការងារ', 'value' => $staff->groups()->count(), 'icon' => 'bi-people-fill', 'class' => 'gray'],
            ],
        ];
    }

    /**
     * Latest attendance rows across everyone.
     */
    private function recentAttendance()
    {
        return Attendance::query()
            ->with('staff')
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }

    /**
     * Latest attendance rows for one person.
     */
    private function ownRecentAttendance(User $user)
    {
        $attendance = $user->staffV2;

        if ($attendance === null) {
            return collect();
        }

        return $attendance->attendances()
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    }

    /**
     * Meetings from today onwards.
     */
    private function upcomingMeetings()
    {
        return Meeting::query()
            ->with(['meetingType', 'location'])
            ->whereDate('meeting_date', '>=', today())
            ->orderBy('meeting_date')
            ->limit(5)
            ->get();
    }

    /**
     * Meetings one person is registered for.
     */
    private function ownUpcomingMeetings(User $user)
    {
        if ($user->staff === null) {
            return collect();
        }

        return $user->staff->meetings()
            ->with(['meetingType', 'location'])
            ->whereDate('meetings.meeting_date', '>=', today())
            ->orderBy('meeting_date')
            ->limit(5)
            ->get();
    }
}
