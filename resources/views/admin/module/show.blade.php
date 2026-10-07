@extends('layouts.app')

@php
    $statusColumn = $definition['status_field'] ?? null;
    $isActive = $statusColumn !== null
        && $record->getAttribute($statusColumn) === $statusValues['active'];
@endphp

@section('title', $definition['singular'].' '.$record->getKey())
@section('titleSuffix', $definition['label'])

@section('content')

<div class="module-page">

    {{-- Page header --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div>

            <h1>
                <i class="bi {{ $definition['icon'] }}"></i>
                {{ \App\Support\ModulePresenter::rowLabel($record, $definition) }}
            </h1>

            <p>{{ $definition['description'] }}</p>

        </div>

        <div class="module-actions mt-0">

            @if ($definition['show'] ?? false)
                <a href="{{ route("admin.{$module}.index") }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                    ត្រឡប់ក្រោយ
                </a>
            @endif

            @if (($definition['status_field'] ?? null) !== null)
                <form method="POST" action="{{ route("admin.{$module}.toggle", $record->getKey()) }}">

                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn {{ $isActive ? 'btn-warning' : 'btn-success' }}">
                        <i class="bi {{ $isActive ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                        {{ $isActive ? 'ធ្វើឱ្យអសកម្ម' : 'ធ្វើឱ្យសកម្ម' }}
                    </button>

                </form>
            @endif

            <a href="{{ route("admin.{$module}.edit", $record->getKey()) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i>
                កែសម្រួល
            </a>

        </div>

    </div>


    {{-- Field values --}}
    <div class="dashboard-card mb-3">

        <div class="card-header-custom">
            <h3><i class="bi bi-card-text"></i> ព័ត៌មានលម្អិត</h3>
            <p>ព័ត៌មានទាំងអស់នៃ{{ $definition['singular'] }} #{{ $record->getKey() }}</p>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <tbody>

                    @foreach ($definition['fields'] as $name => $field)
                        @php
                            $label = $field['label'] ?? $name;
                            $raw = $record->getAttribute($name);
                            $cell = \App\Support\ModulePresenter::cell($record, $name, $definition);
                            $badge = \App\Support\ModulePresenter::badgeClass($name, $definition, $cell['kind']);

                            // A select backed by another module shows that module's label.
                            $text = $cell['text'];

                            if (($field['type'] ?? 'text') === 'select' && isset($options[$name])) {
                                $text = $options[$name][$raw] ?? $text;
                            }
                        @endphp

                        <tr>
                            <th scope="row" style="width: 260px">{{ $label }}</th>
                            <td>
                                @if ($badge)
                                    <span class="badge {{ $badge }}">{{ $cell['text'] }}</span>
                                @else
                                    {{ $text }}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <th scope="row">បង្កើតនៅ</th>
                        <td class="font-monospace">{{ $record->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>

                    <tr>
                        <th scope="row">កែសម្រួលចុងក្រោយ</th>
                        <td class="font-monospace">{{ $record->updated_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>


    {{-- Related rows declared by the registry (subordinate offices) --}}
    @foreach ($definition['detail_relations'] ?? [] as $relation => $config)
        @php $related = $record->getAttribute($relation) ?? collect(); @endphp

        <div class="dashboard-card mb-3">

            <div class="card-header-custom">
                <h3><i class="bi bi-diagram-3"></i> {{ $config['label'] ?? $relation }}</h3>
                <p>{{ count($related) }} កំណត់ត្រា</p>
            </div>

            @if ($related->isEmpty())

                <div class="module-empty">
                    <i class="bi bi-inbox"></i>
                    <p>មិនមាន{{ $config['label'] ?? $relation }}នៅឡើយ។</p>
                </div>

            @else

                <div class="table-responsive">
                    <table class="table attendance-table module-table mb-0">

                        <thead>
                            <tr>
                                <th style="width: 60px">#</th>
                                @foreach ($config['columns'] ?? [] as $column => $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($related as $row)
                                <tr>
                                    <td class="text-body-tertiary font-monospace">{{ $row->getKey() }}</td>

                                    @foreach ($config['columns'] ?? [] as $column => $label)
                                        @php
                                            $value = $row->getAttribute($column);
                                            $options = $config['labels'][$column] ?? [];
                                            $text = $options[$value] ?? $value;
                                        @endphp

                                        <td>
                                            @if ($value === null || $value === '')
                                                <span class="text-body-tertiary">—</span>
                                            @elseif ($column === ($config['status_column'] ?? null))
                                                <span class="badge text-bg-{{ $value === $statusValues['active'] ? 'success' : 'secondary' }}">
                                                    {{ $text }}
                                                </span>
                                            @else
                                                {{ $text }}
                                            @endif
                                        </td>
                                    @endforeach

                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @endif

        </div>

    @endforeach

</div>

@endsection
