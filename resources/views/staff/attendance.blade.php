@extends('layouts.app')

@section('title', 'វត្តមានរបស់ខ្ញុំ')
@section('titleSuffix', $staff->staff_id)

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-clock-history"></i>
            វត្តមានរបស់ខ្ញុំ
        </h1>

        <p>តែកំណត់ត្រាវត្តមានរបស់ {{ $staff->full_name }} ប៉ុណ្ណោះ</p>

    </div>


    {{-- Today's clock --}}
    <div class="dashboard-card mb-4">

        <div class="card-header-custom">
            <h3>
                <i class="bi bi-alarm"></i>
                កាត់ការងារថ្ងៃនេះ
            </h3>

            <p>{{ now()->format('d/m/Y') }}</p>
        </div>

        <div class="card-body">

            @if ($today === null)
                <p class="mb-0 text-body-secondary">
                    <i class="bi bi-info-circle"></i>
                    អ្នកមិនទាន់ចូលការងារនៅថ្ងៃនេះទេ។ ចុចប៊ូតុងខាងក្រោមដើម្បីកត់ត្រាម៉ោងចូល។
                </p>
            @else
                <div class="row g-3 mb-3">
                    <div class="col-md-3 col-6">
                        <span class="text-body-secondary d-block small">ម៉ោងចូល</span>
                        <strong class="font-monospace">{{ $today->check_in?->format('H:i') ?? '—' }}</strong>
                    </div>

                    <div class="col-md-3 col-6">
                        <span class="text-body-secondary d-block small">ម៉ោងចេញ</span>
                        <strong class="font-monospace">{{ $today->check_out?->format('H:i') ?? '—' }}</strong>
                    </div>

                    <div class="col-md-3 col-6">
                        <span class="text-body-secondary d-block small">ម៉ោងធ្វើការ</span>
                        <strong class="font-monospace">{{ $today->worked_hours ?? '—' }}</strong>
                    </div>

                    <div class="col-md-3 col-6">
                        <span class="text-body-secondary d-block small">ស្ថារាង</span>
                        <span class="badge-status {{ ['present' => 'present', 'late' => 'late', 'absent' => 'absent', 'leave' => 'leave'][$today->status] ?? 'absent' }}">
                            {{ \App\Models\Attendance::statusLabels()[$today->status] ?? $today->status }}
                        </span>
                    </div>
                </div>

                @if ($today->isClosed())
                    <p class="mb-0 text-body-secondary">
                        <i class="bi bi-check-circle text-success"></i>
                        អ្នកបានចូលនិងចេញការងាររួចរាល់សម្រាប់ថ្ងៃនេះ។
                    </p>
                @endif
            @endif

        </div>

        <div class="module-actions">

            @if ($today === null)
                <form method="POST" action="{{ route('staff.attendance.check-in') }}">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-box-arrow-in-right"></i>
                        ចូលការងារ
                    </button>
                </form>
            @elseif ($today->isOpen())
                <form method="POST" action="{{ route('staff.attendance.check-out') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-box-arrow-right"></i>
                        ចេញការងារ
                    </button>
                </form>
            @endif

            <a href="{{ route('staff.attendance.qr') }}" class="btn btn-outline-primary">
                <i class="bi bi-qr-code"></i>
                QR Code
            </a>

            <a href="{{ route('staff.attendance') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i>
                ផ្លាស់ប្ដូរ
            </a>

        </div>

    </div>


    {{-- Summary --}}
    <div class="row g-3 mb-4">

        @php
            $cards = [
                ['label' => 'ស្រុង', 'value' => $summary['total'], 'class' => 'blue', 'icon' => 'bi-journal-check'],
                ['label' => 'វត្តមាន', 'value' => $summary['present'], 'class' => 'blue', 'icon' => 'bi-check-circle'],
                ['label' => 'យឺត', 'value' => $summary['late'], 'class' => 'orange', 'icon' => 'bi-clock'],
                ['label' => 'អវត្តមាន', 'value' => $summary['absent'], 'class' => 'gray', 'icon' => 'bi-x-circle'],
                ['label' => 'សុំច្បាប់', 'value' => $summary['leave'], 'class' => 'purple', 'icon' => 'bi-envelope'],
                ['label' => 'ម៉ោងធ្វើការសរុប', 'value' => number_format($summary['hours'], 1), 'class' => 'purple', 'icon' => 'bi-hourglass-split'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-xl-2 col-md-4 col-6">
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


    {{-- Filters --}}
    <form method="GET" action="{{ route('staff.attendance') }}" class="module-filters">

        <div class="module-search">
            <i class="bi bi-calendar-range"></i>
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control" title="ចាប់ពីកាលបរិច្ឆេទ">
        </div>

        <div class="module-search">
            <i class="bi bi-calendar-range"></i>
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control" title="ដល់កាលបរិច្ឆេទ">
        </div>

        <select name="status" class="form-select module-filter">
            <option value="">ស្ថារាង: ទាំងអស់</option>
            @foreach ([
                'present' => 'វត្តមាន',
                'absent' => 'អវត្តមាន',
                'late' => 'យឺត',
                'leave' => 'សុំច្បាប់',
                'holiday' => 'បុណ្យ',
                'weekend' => 'ចុចអាទិត្យ',
            ] as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-funnel"></i>
            ត្រាស់
        </button>

        @if ($filters !== [])
            <a href="{{ route('staff.attendance') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
                សម្អាត
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="dashboard-card">

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
                            <th>កាលបរិច្ឆេទ</th>
                            <th>ម៉ោងចូល</th>
                            <th>ម៉ោងចេញ</th>
                            <th>ម៉ោងធ្វើការ</th>
                            <th>ប្រភព</th>
                            <th>ស្ថារាង</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            @php
                                $statusLabels = [
                                    'present' => 'វត្តមាន',
                                    'absent' => 'អវត្តមាន',
                                    'late' => 'យឺត',
                                    'leave' => 'សុំច្បាប់',
                                    'holiday' => 'បុណ្យ',
                                    'weekend' => 'ចុចអាទិត្យ',
                                ];
                                $badgeClass = [
                                    'present' => 'present',
                                    'late' => 'late',
                                    'absent' => 'absent',
                                    'leave' => 'leave',
                                ][$attendance->status] ?? 'absent';
                                $sourceLabels = ['manual' => 'ដោយដៃ', 'qr' => 'QR Code', 'system' => 'ស្រោះ'];
                            @endphp

                            <tr>
                                <td class="font-monospace">{{ $attendance->work_date->format('d/m/Y') }}</td>
                                <td class="font-monospace">{{ $attendance->check_in?->format('H:i') ?? '—' }}</td>
                                <td class="font-monospace">{{ $attendance->check_out?->format('H:i') ?? '—' }}</td>
                                <td class="font-monospace">{{ $attendance->worked_hours ?? '—' }}</td>
                                <td>{{ $sourceLabels[$attendance->check_in_source] ?? $attendance->check_in_source ?? '—' }}</td>
                                <td>
                                    <span class="badge-status {{ $badgeClass }}">
                                        {{ $statusLabels[$attendance->status] ?? $attendance->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="module-pagination">{{ $attendances->links() }}</div>

        @endif

    </div>

</div>

@endsection
