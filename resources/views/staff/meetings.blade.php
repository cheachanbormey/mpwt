@extends('layouts.app')

@section('title', 'កិច្ចប្រជុំរបស់ខ្ញុំ')
@section('titleSuffix', $staff->staff_code)

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-calendar-event"></i>
            កិច្ចប្រជុំរបស់ខ្ញុំ
        </h1>

        <p>កិច្ចប្រជុំដែលខ្ញុំត្រូវបានចុះឈ្មោះ ឬបានអញ្ជើញ</p>

    </div>


    <form method="GET" action="{{ route('staff.meetings.index') }}" class="module-filters">

        <select name="status" class="form-select module-filter">
            <option value="">ស្ថានភាព: ទាំងអស់</option>
            @foreach ([
                'scheduled' => 'បានកំណត់',
                'ongoing' => 'កំពុងប្រព្រោះ',
                'completed' => 'បានបញ្ចប់',
                'cancelled' => 'បានលុបចោល',
            ] as $value => $label)
                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>

        <div class="form-check ms-1">
            <input class="form-check-input"
                   type="checkbox"
                   name="upcoming"
                   id="upcoming"
                   value="1"
                   {{ ($filters['upcoming'] ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="upcoming">តែកិច្ចប្រជុំនៃថ្ងៃខ្លាំង</label>
        </div>

        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-funnel"></i>
            ស្វែងរក
        </button>

        @if ($filters !== [])
            <a href="{{ route('staff.meetings.index') }}" class="btn btn-outline-secondary">សម្អាត</a>
        @endif

    </form>


    <div class="dashboard-card">

        @if ($meetings->isEmpty())
            <div class="module-empty">
                <i class="bi bi-calendar-x"></i>
                <p>មិនទាន់មានកិច្ចប្រជុំដែលអ្នកត្រូវចូលរួម។</p>
            </div>
        @else

            <div class="table-responsive">
                <table class="table attendance-table">

                    <thead>
                        <tr>
                            <th>កាលបរិច្ឆេទ</th>
                            <th>ម៉ោង</th>
                            <th>ចំណងជើង</th>
                            <th>ប្រភេទ</th>
                            <th>ទីកន្លែង</th>
                            <th>ស្ថានភាព</th>
                            <th>ចុច</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($meetings as $meeting)
                            @php
                                $labels = [
                                    'scheduled' => 'បានកំណត់', 'ongoing' => 'កំពុងប្រព្រោះ',
                                    'completed' => 'បានបញ្ចប់', 'cancelled' => 'បានលុបចោល',
                                ];
                                $badge = [
                                    'scheduled' => 'late', 'ongoing' => 'present',
                                    'completed' => 'present', 'cancelled' => 'absent',
                                ][$meeting->status] ?? 'absent';
                            @endphp

                            <tr>
                                <td class="font-monospace">{{ $meeting->meeting_date->format('d/m/Y') }}</td>
                                <td class="font-monospace">
                                    {{ \Illuminate\Support\Carbon::parse($meeting->start_time)->format('H:i') }}
                                    –
                                    {{ \Illuminate\Support\Carbon::parse($meeting->end_time)->format('H:i') }}
                                </td>
                                <td>{{ $meeting->title }}</td>
                                <td>{{ $meeting->meetingType?->name ?? '—' }}</td>
                                <td>{{ $meeting->location?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge-status {{ $badge }}">
                                        {{ $labels[$meeting->status] ?? $meeting->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('staff.meetings.show', $meeting->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        មើល
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="module-pagination">{{ $meetings->links() }}</div>

        @endif

    </div>

</div>

@endsection
