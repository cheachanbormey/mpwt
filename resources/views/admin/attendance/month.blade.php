@extends('layouts.app')

@section('title', 'វត្តមានប្រចាំខែ')
@section('titleSuffix', 'ការគ្រប់គ្រង')

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-calendar-month"></i>
            វត្តមានប្រចាំខែ
        </h1>

        <p>សង្ខេបវត្តមានរបស់បុគ្គលិកម្នាក់ៗសម្រាប់ខែ {{ $month->format('m/Y') }}</p>

    </div>

    {{-- Report switcher --}}
    <div class="module-filters mb-3">
        <a href="{{ route('admin.attendances.day') }}" class="btn btn-outline-primary">
            <i class="bi bi-calendar-day"></i>
            ប្រចាំថ្ងៃ
        </a>

        <a href="{{ route('admin.attendances.month') }}" class="btn btn-primary">
            <i class="bi bi-calendar-month"></i>
            ប្រចាំខែ
        </a>

        <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-list-ul"></i>
            កំណត់ត្រាទាំងអស់
        </a>
    </div>


    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.attendances.month') }}" class="module-filters">

        <div class="module-search">
            <i class="bi bi-calendar-range"></i>
            <input type="month" name="month" value="{{ $filters['month'] ?? '' }}" class="form-control" title="ខែ">
        </div>

        <select name="office_id" class="form-select module-filter">
            <option value="">ការងារ: ទាំងអស់</option>
            @foreach ($offices as $id => $name)
                <option value="{{ $id }}" {{ (string) ($filters['office_id'] ?? '') === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <select name="position_id" class="form-select module-filter">
            <option value="">តួនាទី: ទាំងអស់</option>
            @foreach ($positions as $id => $name)
                <option value="{{ $id }}" {{ (string) ($filters['position_id'] ?? '') === (string) $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-funnel"></i>
            ស្វែងរក
        </button>

        @if ($filters !== [])
            <a href="{{ route('admin.attendances.month') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
                សម្អាត
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="dashboard-card">

        @if ($rows->isEmpty())
            <div class="module-empty">
                <i class="bi bi-inbox"></i>
                <p>មិនមានទិន្នន័យសម្រាប់ខែនេះទេ។</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table attendance-table module-table">

                    <thead>
                        <tr>
                            <th>លេខបុគ្គលិក</th>
                            <th>ឈ្មោះ</th>
                            <th>ការងារ</th>
                            <th>តួនាទី</th>
                            <th class="text-end">វត្តមាន</th>
                            <th class="text-end">យឺត</th>
                            <th class="text-end">អវត្តមាន</th>
                            <th class="text-end">សុំច្បាប់</th>
                            <th class="text-end">ម៉ោងសរុប</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $row)
                            <tr>
                                <td class="font-monospace">{{ $row->staff_id }}</td>
                                <td>{{ $row->full_name }}</td>
                                <td>{{ $row->office?->name ?? '—' }}</td>
                                <td>{{ $row->position?->position_name_kh ?? '—' }}</td>
                                <td class="text-end font-monospace">{{ $row->present_count }}</td>
                                <td class="text-end font-monospace">{{ $row->late_count }}</td>
                                <td class="text-end font-monospace">{{ $row->absent_count }}</td>
                                <td class="text-end font-monospace">{{ $row->leave_count }}</td>
                                <td class="text-end font-monospace">{{ number_format((float) $row->hours_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="module-pagination">{{ $rows->links() }}</div>
        @endif

    </div>

</div>

@endsection