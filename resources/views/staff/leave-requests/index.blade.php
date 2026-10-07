@extends('layouts.app')

@section('title', 'សំឡេងស្មារណការរបស់ខ្ញុំ')
@section('titleSuffix', $staff->staff_code)

@section('content')

<div class="module-page">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div>
            <h1>
                <i class="bi bi-envelope-paper"></i>
                សំឡេងស្មារណការរបស់ខ្ញុំ
            </h1>
            <p>សំឡេងដែលខ្ញុំបានបញ្ជូន តែខ្ញុំ</p>
        </div>

        <a href="{{ route('staff.leave-requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            ស្នើសំឡេង
        </a>

    </div>


    {{--
        Manual workflow notice. There is no approve button anywhere,
        because approval happens on the printed form.
    --}}
    <div class="alert alert-info d-flex gap-2" role="alert">
        <i class="bi bi-printer"></i>
        <div>
            សំឡេងនីមួយៗត្រូវបាន<strong>បោះពុម្ព</strong>ដើម្បីយកទៅចុចហត្ថលេខាដោយអ្នកគ្រប់គ្រង។
            ប្រព័ន្ធនេះ<strong>មិនមាន</strong>ប៊ូតុនអនុម័ត ឬបដិសេធឡើយ។
        </div>
    </div>


    {{-- Status filter --}}
    <form method="GET" action="{{ route('staff.leave-requests.index') }}" class="module-filters">

        <select name="status" class="form-select module-filter">
            <option value="">ស្ថារាង: ទាំងអស់</option>
            @foreach ([
                'pending' => 'កំពុងរង់ចាំ',
                'approved' => 'បានអនុម័ត',
                'rejected' => 'បានបដិសេធ',
                'cancelled' => 'បានលុបចោល',
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
            <a href="{{ route('staff.leave-requests.index') }}" class="btn btn-outline-secondary">
                សម្អាត
            </a>
        @endif

    </form>


    <div class="dashboard-card">

        @if ($leaveRequests->isEmpty())
            <div class="module-empty">
                <i class="bi bi-inbox"></i>
                <p>មិនទាន់មានសំឡេងស្មារណការទេ។</p>
                <a href="{{ route('staff.leave-requests.create') }}" class="btn btn-sm btn-primary">
                    ស្នើសំឡេងដំបូងម្តង
                </a>
            </div>
        @else

            <div class="table-responsive">
                <table class="table attendance-table">

                    <thead>
                        <tr>
                            <th>ល.អត្តសញ្ញា</th>
                            <th>ប្រភេទ</th>
                            <th>ចាប់ពី</th>
                            <th>ដល់</th>
                            <th>ចំនួនថ្ងៃ</th>
                            <th>ស្ថារាង</th>
                            <th>មូលដ្ឋាន</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($leaveRequests as $request)
                            @php
                                $labels = [
                                    'pending' => 'កំពុងរង់ចាំ',
                                    'approved' => 'បានអនុម័ត',
                                    'rejected' => 'បានបដិសេធ',
                                    'cancelled' => 'បានលុបចោល',
                                ];
                                $badge = [
                                    'pending' => 'late',
                                    'approved' => 'present',
                                    'rejected' => 'absent',
                                    'cancelled' => 'leave',
                                ][$request->status] ?? 'absent';
                            @endphp

                            <tr>
                                <td class="font-monospace">#{{ $request->id }}</td>
                                <td>{{ $request->leaveType?->name ?? '—' }}</td>
                                <td class="font-monospace">{{ $request->start_date->format('d/m/Y') }}</td>
                                <td class="font-monospace">{{ $request->end_date->format('d/m/Y') }}</td>
                                <td class="font-monospace">{{ $request->total_days }}</td>
                                <td>
                                    <span class="badge-status {{ $badge }}">
                                        {{ $labels[$request->status] ?? $request->status }}
                                    </span>
                                </td>
                                <td class="small">{{ \Illuminate\Support\Str::limit($request->reason, 60) }}</td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="module-pagination">{{ $leaveRequests->links() }}</div>

        @endif

    </div>

</div>

@endsection
