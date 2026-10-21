@extends('layouts.app')

@section('title', 'វត្តមានប្រចាំថ្ងៃ')
@section('titleSuffix', 'ការគ្រប់គ្រង')

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-calendar-day"></i>
            វត្តមានប្រចាំថ្ងៃ
        </h1>

        <p>កំណត់ត្រាវត្តមានទាំងអស់នៅថ្ងៃ {{ $day->format('d/m/Y') }}</p>

    </div>

    {{-- Report switcher --}}
    <div class="module-filters mb-3">
        <a href="{{ route('admin.attendances.day') }}" class="btn btn-primary">
            <i class="bi bi-calendar-day"></i>
            ប្រចាំថ្ងៃ
        </a>

        <a href="{{ route('admin.attendances.month') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar-month"></i>
            ប្រចាំខែ
        </a>

        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul"></i>
            កំណត់ត្រាទាំងអស់
        </a>
    </div>


    {{-- Day summary --}}
    @php
        $cards = [
            ['label' => 'កំណត់ត្រាសរុប', 'value' => $summary['total'], 'class' => 'blue', 'icon' => 'bi-journal-check'],
            ['label' => 'វត្តមាន', 'value' => $summary['present'], 'class' => 'blue', 'icon' => 'bi-check-circle'],
            ['label' => 'យឺត', 'value' => $summary['late'], 'class' => 'orange', 'icon' => 'bi-clock'],
            ['label' => 'អវត្តមាន', 'value' => $summary['absent'], 'class' => 'gray', 'icon' => 'bi-x-circle'],
            ['label' => 'សុំច្បាប់', 'value' => $summary['leave'], 'class' => 'purple', 'icon' => 'bi-envelope'],
            ['label' => 'ម៉ោងធ្វើការ', 'value' => $summary['hours'], 'class' => 'purple', 'icon' => 'bi-hourglass-split'],
        ];
    @endphp

    <div class="row g-3 mb-4">
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
    <form method="GET" action="{{ route('admin.attendances.day') }}" class="module-filters">

        <div class="module-search">
            <i class="bi bi-calendar-range"></i>
            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="form-control" title="កាលបរិច្ឆេទ">
        </div>

        <div class="module-search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control"
                   placeholder="ស្វែងរកបុគ្គលិក…">
        </div>

        <select name="status" class="form-select module-filter">
            <option value="">ស្ថានភាព: ទាំងអស់</option>
            @foreach (\App\Models\Attendance::statusLabels() as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-funnel"></i>
            ស្វែងរក
        </button>

        @if ($filters !== [])
            <a href="{{ route('admin.attendances.day') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
                សម្អាត
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="dashboard-card">

        @if ($records->isEmpty())
            <div class="module-empty">
                <i class="bi bi-inbox"></i>
                <p>មិនមានកំណត់ត្រាវត្តមានសម្រាប់ថ្ងៃនេះទេ។</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table attendance-table module-table">

                    <thead>
                        <tr>
                            <th>លេខបុគ្គលិក</th>
                            <th>ឈ្មោះ</th>
                            <th>ម៉ោងចូល</th>
                            <th>ម៉ោងចេញ</th>
                            <th>ម៉ោងធ្វើការ</th>
                            <th>ប្រភព</th>
                            <th>ស្ថានភាព</th>
                            <th class="text-end" style="width: 60px">ពិនិត្យ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $sourceLabels = ['manual' => 'ដោយដៃ', 'qr' => 'QR Code', 'system' => 'ស្រោះ'];
                            $badgeClass = [
                                'present' => 'present',
                                'late' => 'late',
                                'absent' => 'absent',
                                'leave' => 'leave',
                            ];
                        @endphp

                        @foreach ($records as $record)
                            <tr>
                                <td class="font-monospace">{{ $record->staff?->staff_id ?? '—' }}</td>
                                <td>{{ $record->staff?->full_name ?? '—' }}</td>
                                <td class="font-monospace">{{ $record->check_in?->format('H:i') ?? '—' }}</td>
                                <td class="font-monospace">{{ $record->check_out?->format('H:i') ?? '—' }}</td>
                                <td class="font-monospace">{{ $record->worked_hours ?? '—' }}</td>
                                <td>{{ $sourceLabels[$record->check_in_source] ?? $record->check_in_source ?? '—' }}</td>
                                <td>
                                    <span class="badge-status {{ $badgeClass[$record->status] ?? 'absent' }}">
                                        {{ \App\Models\Attendance::statusLabels()[$record->status] ?? $record->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.attendances.show', $record->getKey()) }}"
                                       class="btn btn-sm btn-outline-secondary" title="មើលព័ត៌មាន">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="module-pagination">{{ $records->links() }}</div>
        @endif

    </div>

</div>

@endsection