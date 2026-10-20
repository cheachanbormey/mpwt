@extends('layouts.app')

@section('title', $definition['label'])
@section('titleSuffix', 'ការគ្រប់គ្រង')

@section('content')

<div class="module-page">

    {{-- Page header --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">

        <div>

            <h1>
                <i class="bi {{ $definition['icon'] }}"></i>
                {{ $definition['label'] }}
            </h1>

            <p>{{ $definition['description'] }}</p>

        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">

            {{-- Extra screens the registry offers alongside this list --}}
            @foreach (($definition['reports'] ?? []) as $report)
                <a href="{{ route($report['route']) }}" class="btn btn-outline-primary">
                    <i class="bi {{ $report['icon'] }}"></i>
                    {{ $report['label'] }}
                </a>
            @endforeach

            <a href="{{ route("admin.{$module}.create") }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                បង្កើត{{ $definition['singular'] }}
            </a>

        </div>

    </div>


    {{-- Search and filters --}}
    <form method="GET" action="{{ route("admin.{$module}.index") }}" class="module-filters">

        {{-- Search --}}
        @if (($definition['search'] ?? []) !== [])
            <div class="module-search">

                <i class="bi bi-search"></i>

                <input type="search"
                       name="q"
                       value="{{ $search }}"
                       class="form-control"
                       placeholder="ស្វែងរក…">

            </div>
        @endif

        {{-- Dropdown filters --}}
        @foreach ($filterOptions as $name => $choices)
            <select name="{{ $name }}" class="form-select module-filter">

                <option value="">
                    {{ $definition['filters'][$name]['label'] }}: ទាំងអស់
                </option>

                @foreach ($choices as $value => $label)
                    <option value="{{ $value }}" {{ ($filters[$name] ?? '') === (string) $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach

            </select>
        @endforeach

        {{-- Date filters --}}
        @foreach ($definition['filters'] ?? [] as $name => $filter)
            @if (($filter['type'] ?? null) === 'date')
                <input type="date"
                       name="{{ $name }}"
                       value="{{ $filters[$name] ?? '' }}"
                       class="form-control module-filter"
                       title="{{ $filter['label'] }}">
            @endif
        @endforeach

        <button type="submit" class="btn btn-secondary">
            <i class="bi bi-funnel"></i>
            ស្វែងរក
        </button>

        @if ($search !== '' || $filters !== [])
            <a href="{{ route("admin.{$module}.index") }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i>
                សម្អាត
            </a>
        @endif

    </form>


    {{-- Table --}}
    <div class="dashboard-card">

        @php
    $columns = \App\Support\ModulePresenter::columns($definition);
    $showRoute = ($definition['show'] ?? false) ? "admin.{$module}.show" : null;
    $toggleRoute = ($definition['status_field'] ?? null) !== null ? "admin.{$module}.toggle" : null;
    $statusColumn = $definition['status_field'] ?? null;
    $actionWidth = 140 + ($showRoute ? 34 : 0) + ($toggleRoute ? 34 : 0);
@endphp

        @if ($rows->isEmpty())

            <div class="module-empty">

                <i class="bi bi-inbox"></i>

                <p>
                    @if ($search !== '' || $filters !== [])
                        រកមិនឃើញទិន្នន័យដែលស្វែងរក។
                    @else
                        មិនទាន់មាន{{ $definition['label'] }}នៅឡើយ។
                    @endif
                </p>

                <a href="{{ route("admin.{$module}.create") }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg"></i>
                    បង្កើត{{ $definition['singular'] }}ដំបូងម្តង
                </a>

            </div>

        @else

            <div class="table-responsive">

                <table class="table attendance-table module-table">

                    <thead>
                        <tr>
                            <th style="width: 60px">#</th>

                            @foreach ($columns as $column)
                                <th>{{ $column['label'] }}</th>
                            @endforeach

                            <th class="text-end" style="width: {{ $actionWidth }}px">សកម្មភាព</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $row)
                            @php
                                $cells = [];
                                foreach ($columns as $column) {
                                    $cell = \App\Support\ModulePresenter::cell($row, $column['key'], $definition);
                                    $cells[$column['key']] = [
                                        'text'  => $cell['text'],
                                        'class' => \App\Support\ModulePresenter::cellClass($column['key'], $definition, $cell['kind']),
                                        'kind'  => $cell['kind'],
                                        'badge' => \App\Support\ModulePresenter::badgeClass($column['key'], $definition, $cell['kind']),
                                    ];
                                }
                            @endphp

                            <tr>
                                <td class="text-body-tertiary font-monospace">{{ $row->getKey() }}</td>

                                @foreach ($columns as $column)
                                    <td class="{{ $cells[$column['key']]['class'] }}">

                                        @if (in_array($cells[$column['key']]['kind'], ['yes', 'no'], true))
                                            <span class="badge-status {{ $cells[$column['key']]['kind'] === 'yes' ? 'present' : 'absent' }}">
                                                {{ $cells[$column['key']]['text'] }}
                                            </span>
                                        @elseif ($cells[$column['key']]['badge'])
                                            <span class="badge {{ $cells[$column['key']]['badge'] }}">
                                                {{ $cells[$column['key']]['text'] }}
                                            </span>
                                        @else
                                            {{ $cells[$column['key']]['text'] }}
                                        @endif

                                    </td>
                                @endforeach

                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">

                                        @if ($showRoute)
                                            <a href="{{ route($showRoute, $row->getKey()) }}"
                                               class="btn btn-outline-secondary"
                                               title="មើលព័ត៌មាន">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif

                                        @if ($toggleRoute)
                                            @php $isActive = $row->getAttribute($statusColumn) === 'active'; @endphp

                                            <form method="POST"
                                                  action="{{ route($toggleRoute, $row->getKey()) }}"
                                                  onsubmit="return confirm('{{ $isActive ? 'តើត្រូវធ្វើឱ្យអសកម្មម៉ូឌុលនេះទេ?' : 'តើត្រូវធ្វើឱ្យសកម្មម៉ូឌុលនេះទេ?' }}');">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="btn {{ $isActive ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                        title="{{ $isActive ? 'ធ្វើឱ្យអសកម្ម' : 'ធ្វើឱ្យសកម្ម' }}">
                                                    <i class="bi {{ $isActive ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                                </button>

                                            </form>
                                        @endif

                                        <a href="{{ route("admin.{$module}.edit", $row->getKey()) }}"
                                           class="btn btn-outline-primary"
                                           title="កែសម្រួល">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form method="POST"
                                              action="{{ route("admin.{$module}.destroy", $row->getKey()) }}"
                                              onsubmit="return confirm('តើត្រូវលុបម្តងទេ? សកម្មភាពនេះមិនអាចត្រឡប់វិញបានទេ។');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-outline-danger" title="លុប">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

            <div class="module-pagination">
                {{ $rows->links() }}
            </div>

        @endif

    </div>

</div>

@endsection
