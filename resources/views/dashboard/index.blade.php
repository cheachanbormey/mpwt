@extends('layouts.app')

@section('title', 'ផ្ទាំងគ្រប់គ្រង')

@section('content')

<div class="dashboard-container">

    {{-- Page header --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div>

            <h1>
                សូមស្វាគមន៍, {{ $user->display_name }}
            </h1>

            <p>
                {{ $user->isAdmin()
                    ? 'ទិន្នន័យទូយកពីប្រព័ន្ធទាំងអស់'
                    : 'ព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នក' }}
                &middot;
                {{ now()->translatedFormat('l, d F Y') }}
            </p>

            <p class="mb-0">
                <i class="bi bi-building"></i>
                ក្រសួងសាធារណការ និងដឹកជញ្ជូន
            </p>

        </div>

        <span class="badge role-badge role-{{ $user->role }} fs-6">
            <i class="bi bi-{{ $user->isAdmin() ? 'shield-lock' : 'person' }}"></i>
            {{ $user->role_label }}
        </span>

    </div>


    {{-- Headline figures --}}
    <div class="row g-3 mb-3">
        @foreach ($stats['headline'] as $card)
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon {{ $card['class'] }}">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="stat-content">
                        <span>{{ $card['label'] }}</span>
                        <h2>{{ $card['value'] }}</h2>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    {{-- Secondary figures --}}
    <div class="row g-3 mb-4">
        @foreach ($stats['secondary'] as $card)
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon {{ $card['class'] }}">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div class="stat-content">
                        <span>{{ $card['label'] }}</span>
                        <h2>{{ $card['value'] }}</h2>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <div class="row g-3">

        {{-- Attendance --}}
        <div class="col-lg-8">

            <div class="dashboard-card">

                <div class="attendance-header">

                    <div>
                        <h3>
                            <i class="bi bi-clock-history"></i>
                            {{ $user->isAdmin() ? 'វត្តមានថ្មែរ' : 'វត្តមានរបស់ខ្ញុំ' }}
                        </h3>
                    </div>

                    <a href="{{ $user->isAdmin() ? route('admin.attendances.index') : route('staff.attendance') }}"
                       class="btn btn-sm btn-outline-primary">
                        មើលទាំងអស់
                    </a>

                </div>

                @if ($attendances->isEmpty())
                    <div class="module-empty">
                        <i class="bi bi-inbox"></i>
                        <p>មិនទាន់មានកំណត់ត្រាវត្តមាន។</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table attendance-table">

                            <thead>
                                <tr>
                                    @if ($user->isAdmin())
                                        <th>បុគ្គលិក</th>
                                    @endif
                                    <th>កាលបរិច្ឆេទ</th>
                                    <th>ម៉ោងចូល</th>
                                    <th>ម៉ោងចេញ</th>
                                    <th>ម៉ោងធ្វើការ</th>
                                    <th>ស្ថារាង</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($attendances as $attendance)
                                    @php
                                        $labels = [
                                            'present' => 'វត្តមាន', 'absent' => 'អវត្តមាន',
                                            'late' => 'យឺត', 'leave' => 'សុំច្បាប់',
                                            'holiday' => 'បុណ្យ', 'weekend' => 'ចុចអាទិត្យ',
                                        ];
                                        $badge = [
                                            'present' => 'present', 'late' => 'late',
                                            'absent' => 'absent', 'leave' => 'leave',
                                        ][$attendance->status] ?? 'absent';
                                    @endphp

                                    <tr>
                                        @if ($user->isAdmin())
                                            <td>{{ $attendance->staff?->name_km ?? '—' }}</td>
                                        @endif
                                        <td class="font-monospace">{{ $attendance->work_date->format('d/m/Y') }}</td>
                                        <td class="font-monospace">{{ $attendance->check_in?->format('H:i') ?? '—' }}</td>
                                        <td class="font-monospace">{{ $attendance->check_out?->format('H:i') ?? '—' }}</td>
                                        <td class="font-monospace">{{ $attendance->worked_hours ?? '—' }}</td>
                                        <td>
                                            <span class="badge-status {{ $badge }}">
                                                {{ $labels[$attendance->status] ?? $attendance->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                @endif

            </div>

        </div>


        {{-- Side column --}}
        <div class="col-lg-4">

            {{-- Meetings --}}
            <div class="dashboard-card mb-3">

                <div class="card-header-custom">
                    <div>
                        <h3><i class="bi bi-calendar-event"></i> កិច្ចប្រជុំនៃថ្ងៃខ្លាំង</h3>
                    </div>

                    <a href="{{ route('staff.meetings.index') }}" class="btn btn-sm btn-outline-primary">
                        ទាំងអស់
                    </a>
                </div>

                @if ($upcomingMeetings->isEmpty())
                    <p class="text-body-secondary p-3 mb-0">មិនមានកិច្ចប្រជុំនៅថ្ងៃខ្លាំង។</p>
                @else
                    <div class="p-3 d-flex flex-column gap-2">
                        @foreach ($upcomingMeetings as $meeting)
                            @if ($user->isAdmin())
                                <a href="{{ route('admin.meetings.edit', $meeting->id) }}"
                                   class="btn btn-sm btn-light border text-start">
                                    <strong class="d-block">{{ $meeting->title }}</strong>
                                    <small class="text-body-secondary">
                                        {{ $meeting->meeting_date->format('d/m/Y') }}
                                        &middot;
                                        {{ \Illuminate\Support\Carbon::parse($meeting->start_time)->format('H:i') }}
                                    </small>
                                </a>
                            @else
                                <a href="{{ route('staff.meetings.show', $meeting->id) }}"
                                   class="btn btn-sm btn-light border text-start">
                                    <strong class="d-block">{{ $meeting->title }}</strong>
                                    <small class="text-body-secondary">
                                        {{ $meeting->meeting_date->format('d/m/Y') }}
                                        &middot;
                                        {{ \Illuminate\Support\Carbon::parse($meeting->start_time)->format('H:i') }}
                                    </small>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

            </div>


            {{-- Pending leave (admin only) --}}
            @if ($user->isAdmin())
                <div class="dashboard-card mb-3">
                    <div class="card-header-custom">
                        <div>
                            <h3><i class="bi bi-envelope-paper"></i> សុំច្បាប់កំពុងរង់ចាំ</h3>
                        </div>

                        <a href="{{ route('admin.leave_requests.index') }}" class="btn btn-sm btn-outline-primary">
                            ទាំងអស់
                        </a>
                    </div>

                    @if ($pendingLeave->isEmpty())
                        <p class="text-body-secondary p-3 mb-0">មិនមានសុំច្បាប់កំពុងរង់ចាំ។</p>
                    @else
                        <div class="table-responsive">
                            <table class="table attendance-table mb-0">
                                <tbody>
                                    @foreach ($pendingLeave as $request)
                                        <tr>
                                            <td>{{ $request->staff?->name_km ?? '—' }}</td>
                                            <td class="text-end">
                                                <small>{{ $request->start_date->format('d/m/Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 border-top">
                            <small class="text-body-secondary">
                                <i class="bi bi-printer"></i>
                                សុំច្បាប់ត្រូវបោះពុម្ពដើម្បីចុចហត្ថលេខាដោយដៃ។
                            </small>
                        </div>
                    @endif
                </div>
            @endif


            {{-- Quick links --}}
            <div class="dashboard-card">
                <div class="card-header-custom">
                    <div><h3><i class="bi bi-lightning"></i> ចម្លាប់រឿង</h3></div>
                </div>

                <div class="p-3 d-grid gap-2">
                    <a href="{{ route('staff.leave-requests.create') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-envelope-plus"></i> ស្នើសុំច្បាប់ថ្មី
                    </a>
                    <a href="{{ route('staff.attendance') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-clock-history"></i> វត្តមានរបស់ខ្ញុំ
                    </a>
                    <a href="{{ route('staff.profile') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-person-circle"></i> ប្រវត្តិរូបរបស់ខ្ញុំ
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
