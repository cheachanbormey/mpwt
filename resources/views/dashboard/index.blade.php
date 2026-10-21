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
        ? 'ទិន្នន័យទូទៅពីប្រព័ន្ធ'
        : 'ព័ត៌មានផ្ទាល់ខ្លួនរបស់អ្នក' }}
                    &middot;
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>

                <p class="mb-0">
                    <i class="bi bi-building"></i>
                    ក្រសួងសាធារណការ និងដឹកជញ្ជូន
                </p>

            </div>

            <!-- <span class="badge role-badge role-{{ $user->role }} fs-6">
                <i class="bi bi-{{ $user->isAdmin() ? 'shield-lock' : 'person' }}"></i>
                {{ $user->role_label }}
            </span> -->

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


        <!-- 
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
        </div> -->


        <div class="row g-3">

            {{-- Attendance --}}
            <div class="col-lg-12">

                <div class="dashboard-card">

                    <div class="attendance-header">

                        <div>
                            <h3>
                                <i class="bi bi-clock-history"></i>
                                {{ $user->isAdmin() ? 'កំណត់ត្រាវត្តមាន' : 'វត្តមានទាំងអស់' }}
                            </h3>
                        </div>

                        <a href="{{ $user->isAdmin() ? route('admin.attendances.index') : route('staff.staff.attendance') }}"
                            class="btn btn-sm btn-outline-primary">
                            មើលទាំងអស់
                        </a>

                    </div>

                    @if (blank($attendances))
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
                                        <th>ស្ថានភាព</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($attendances as $attendance)
                                        @php
                                            $labels = [
                                                'present' => 'វត្តមាន',
                                                'absent' => 'អវត្តមាន',
                                                'late' => 'យឺត',
                                                'leave' => 'សុំច្បាប់',
                                            ];
                                            $badge = [
                                                'present' => 'present',
                                                'late' => 'late',
                                                'absent' => 'absent',
                                                'leave' => 'leave',
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
            {{-- Pending leave (admin only) --}}
            <!-- @if ($user->isAdmin())
                    <div class="dashboard-card mb-3">
                        <div class="card-header-custom">
                            <div>
                                <h3><i class="bi bi-envelope-paper"></i> សុំច្បាប់កំពុងរង់ចាំ</h3>
                            </div>

                            <a href="{{ route('admin.leave_requests.index') }}" class="btn btn-sm btn-outline-primary">
                                ទាំងអស់
                            </a>
                        </div>

                        @if (blank($pendingLeave))
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
                @endif  -->

        </div>

    </div>

@endsection