@extends('layouts.app')

@section('title', $meeting->title)
@section('titleSuffix', 'កិច្ចប្រជុំ')

@section('content')

<div class="module-page">

    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div>

            <h1>
                <i class="bi bi-calendar-event"></i>
                {{ $meeting->title }}
            </h1>

            <p>
                {{ $meeting->meeting_date->format('d/m/Y') }}
                &middot;
                {{ \Illuminate\Support\Carbon::parse($meeting->start_time)->format('H:i') }}
                –
                {{ \Illuminate\Support\Carbon::parse($meeting->end_time)->format('H:i') }}
            </p>

        </div>

        <a href="{{ route('staff.meetings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            ត្រឡប់ក្រោយ
        </a>

    </div>


    <div class="row g-3">

        {{-- Details --}}
        <div class="col-lg-4">

            <div class="dashboard-card h-100">

                <div class="card-header-custom">
                    <div><h3><i class="bi bi-info-circle"></i> ព័ត៌មាន</h3></div>
                </div>

                <table class="table attendance-table mb-0">

                    <tbody>
                        <tr>
                            <th>ប្រភេទ</th>
                            <td class="text-end">{{ $meeting->meetingType?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ទីកន្លែង</th>
                            <td class="text-end">{{ $meeting->location?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>អ្នកបង្កើត</th>
                            <td class="text-end">{{ $meeting->creator?->name_km ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ស្ថានភាពរបស់ខ្ញុំ</th>
                            <td class="text-end">
                                @php
                                    $participantLabels = [
                                        'invited' => 'បានអញ្ជើញ',
                                        'accepted' => 'បានទទួល',
                                        'declined' => 'បានបដិសេធ',
                                        'attended' => 'បានចូលរួម',
                                        'absent' => 'មិនបានមក',
                                    ];
                                @endphp
                                {{ $participation ? ($participantLabels[$participation->attendance_status] ?? '—') : 'អ្នកបង្កើត' }}
                            </td>
                        </tr>
                    </tbody>

                </table>

                @if ($meeting->agenda_summary)
                    <div class="p-3 border-top">
                        <h4 class="small text-uppercase text-body-secondary">សេចក្តីសង្ខេប</h4>
                        <p class="mb-0 small">{{ $meeting->agenda_summary }}</p>
                    </div>
                @endif

            </div>

        </div>


        {{-- Agenda --}}
        <div class="col-lg-8">

            <div class="dashboard-card mb-3">

                <div class="card-header-custom">
                    <div><h3><i class="bi bi-list-ol"></i> រាប់ការប្រជុំ</h3></div>
                </div>

                @if ($meeting->agendas->isEmpty())
                    <p class="text-body-secondary p-3 mb-0">មិនទាន់មានរាប់ការប្រជុំ។</p>
                @else
                    <div class="table-responsive">
                        <table class="table attendance-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>ប្រធានកិច្ច</th>
                                    <th>អ្នកបង្ហាញ</th>
                                    <th class="text-end">រយៈពេល</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meeting->agendas as $agenda)
                                    <tr>
                                        <td class="font-monospace">{{ $agenda->agenda_no }}</td>
                                        <td>{{ $agenda->title }}</td>
                                        <td>{{ $agenda->presenter?->name_km ?? '—' }}</td>
                                        <td class="text-end font-monospace">
                                            {{ $agenda->duration_minutes ? $agenda->duration_minutes.' នាទី' : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>


            {{-- Participants --}}
            <div class="dashboard-card">

                <div class="card-header-custom">
                    <div>
                        <h3><i class="bi bi-people"></i> អ្នកចូលរួម</h3>
                        <p>{{ $meeting->participants->count() }} នាក់</p>
                    </div>
                </div>

                <div class="p-3 d-flex flex-wrap gap-2">
                    @foreach ($meeting->participants as $participant)
                        <span class="badge text-bg-light border">
                            {{ $participant->staff?->name_km ?? 'មិនទាន់មាន' }}
                        </span>
                    @endforeach
                </div>

            </div>

        </div>

    </div>

</div>

@endsection
