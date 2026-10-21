@extends('layouts.app')

@php
    $editing = $record !== null;
    $action = $editing
        ? route("admin.{$module}.update", $record->getKey())
        : route("admin.{$module}.store");
    $readonlyFields = collect($definition['fields'])
        ->filter(fn ($field) => $field['readonly'] ?? false)
        ->keys();
@endphp

@section('title', ($editing ? 'កែសម្រួល' : 'បង្កើត').$definition['singular'])
@section('titleSuffix', $definition['label'])

@section('content')

<div class="module-page">

    {{-- Page header --}}
    <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1>
                <i class="bi {{ $definition['icon'] }}"></i>
                {{ $editing ? 'កែសម្រួល' : 'បង្កើត' }}{{ $definition['singular'] }}
            </h1>
            <p>{{ $definition['description'] }}</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route("admin.{$module}.index") }}" class="btn btn-outline-secondary btn-back-header">
                <i class="bi bi-arrow-left"></i>
                ត្រឡប់ទៅបញ្ជី{{ $definition['label'] }}
            </a>
        </div>
    </div>

    <div class="module-form-container">

        {{-- Notice for read-only fields (the manual leave workflow) --}}
        @if ($readonlyFields->isNotEmpty())
            <div class="alert alert-warning border-0 shadow-sm rounded-3 d-flex align-items-start gap-3 p-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0 mt-1"></i>
                <div class="small">
                    <strong class="d-block mb-1 text-dark">សំគាល់សំខាន់៖</strong>
                    ស្ថារាងមួយៗ ({{ implode(', ', $readonlyFields->map(fn ($f) => $definition['fields'][$f]['label'])->all()) }})
                    មិនអាចកែប្រែនៅក្នុងប្រព័ន្ធនេះទេ។
                    សំឡេងស្មារណការត្រូវបានបោះពុម្ពដើម្បីយកទៅចុចហត្ថលេខាដោយអ្នកគ្រប់គ្រងជំនួសវិញ។
                </div>
            </div>
        @endif

        <form method="POST" action="{{ $action }}" class="module-form" novalidate>
            @csrf

            @if ($editing)
                @method('PUT')
            @endif

            <div class="dashboard-card module-form-card">

                <div class="card-header-custom d-flex align-items-center justify-content-between">
                    <h3 class="d-flex align-items-center gap-2 mb-0">
                        <i class="bi {{ $definition['icon'] ?? 'bi-file-earmark-text' }}"></i>
                        <span>ព័ត៌មាន{{ $definition['singular'] }}</span>
                    </h3>
                    <span class="form-required-badge">
                        <span class="text-danger fw-bold">*</span> ចាំបាច់ត្រូវបំពេញ
                    </span>
                </div>

                <div class="module-form-body">
                    <div class="row g-4">

                        @foreach ($definition['fields'] as $name => $field)

                            @php
                                $label       = $field['label'] ?? $name;
                                $required    = $field['required'] ?? false;
                                $readonly    = $field['readonly'] ?? false;
                                $type        = $field['type'] ?? 'text';
                                $placeholder = $field['placeholder'] ?? null;
                                $hint        = $field['hint'] ?? null;
                                $old         = old($name, $editing ? $record->getAttribute($name) : ($field['default'] ?? null));
                                $wide        = ($field['wide'] ?? false) || in_array($type, ['textarea'], true);
                                $inputId     = 'field-'.$name;
                                $errId       = 'error-'.$name;
                            @endphp

                            <div class="{{ $wide ? 'col-12' : ($type === 'boolean' ? 'col-12' : 'col-md-6') }}">

                                {{-- Checkbox / Boolean Switch Card --}}
                                @if ($type === 'boolean')
                                    <div class="module-switch-card @error($name) is-invalid @enderror">
                                        <div class="form-check form-switch m-0 d-flex align-items-center justify-content-between">
                                            <div>
                                                <label class="form-check-label fw-semibold text-dark d-block mb-1" for="{{ $inputId }}">
                                                    {{ $label }}
                                                    @if ($required && ! $readonly)
                                                        <span class="text-danger fw-bold ms-1" title="ចាំបាច់">*</span>
                                                    @endif
                                                </label>
                                                <span class="module-switch-hint text-muted small">
                                                    {{ $hint ?? 'បើកដំណើរការ ឬកំណត់ឱ្យស្ថិតក្នុងស្ថានភាពសកម្ម' }}
                                                </span>
                                            </div>
                                            <input class="form-check-input ms-3"
                                                   type="checkbox"
                                                   role="switch"
                                                   name="{{ $name }}"
                                                   id="{{ $inputId }}"
                                                   value="1"
                                                   @checked((bool) $old)
                                                   @if ($readonly) disabled @endif>
                                        </div>
                                        @if ($readonly)
                                            <input type="hidden" name="{{ $name }}" value="{{ $old ? '1' : '0' }}">
                                        @endif
                                    </div>

                                @else
                                    <label for="{{ $inputId }}" class="form-label">
                                        <span>{{ $label }}</span>
                                        @if ($required && ! $readonly)
                                            <span class="text-danger fw-bold ms-1" title="ចាំបាច់">*</span>
                                        @endif
                                    </label>

                                    {{-- Select --}}
                                    @if ($type === 'select')
                                        <select name="{{ $name }}"
                                                id="{{ $inputId }}"
                                                class="form-select @error($name) is-invalid @enderror"
                                                @if ($readonly) disabled @endif
                                                @if ($required) required @endif>

                                            <option value="">
                                                — ជ្រើសរើស{{ $label }} —
                                            </option>

                                            @foreach ($options[$name] ?? [] as $value => $text)
                                                <option value="{{ $value }}" {{ (string) $old === (string) $value ? 'selected' : '' }}>
                                                    {{ $text }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @if ($readonly)
                                            <input type="hidden" name="{{ $name }}" value="{{ $old }}">
                                        @endif

                                    {{-- Textarea --}}
                                    @elseif ($type === 'textarea')
                                        <textarea name="{{ $name }}"
                                                  id="{{ $inputId }}"
                                                  rows="4"
                                                  placeholder="{{ $placeholder ?? 'បញ្ចូល'.$label.'...' }}"
                                                  class="form-control @error($name) is-invalid @enderror"
                                                  @if ($required) required @endif
                                                  @if ($readonly) readonly @endif>{{ $old }}</textarea>

                                    {{-- Date --}}
                                    @elseif ($type === 'date')
                                        <input type="date"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old ? \Illuminate\Support\Carbon::parse($old)->format('Y-m-d') : '' }}"
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($required) required @endif
                                               @if ($readonly) readonly @endif>

                                    {{-- Time --}}
                                    @elseif ($type === 'time')
                                        <input type="time"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old ? \Illuminate\Support\Carbon::parse($old)->format('H:i') : '' }}"
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($required) required @endif
                                               @if ($readonly) readonly @endif>

                                    {{-- Date and time --}}
                                    @elseif ($type === 'datetime')
                                        <input type="datetime-local"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old ? \Illuminate\Support\Carbon::parse($old)->format('Y-m-d\TH:i') : '' }}"
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($readonly) readonly @endif>

                                    {{-- Number / decimal --}}
                                    @elseif (in_array($type, ['number', 'decimal'], true))
                                        <input type="number"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old }}"
                                               step="{{ $type === 'decimal' ? '0.01' : '1' }}"
                                               placeholder="{{ $placeholder ?? 'បញ្ចូល'.$label.'...' }}"
                                               @if (isset($field['min'])) min="{{ $field['min'] }}" @endif
                                               @if (isset($field['max'])) max="{{ $field['max'] }}" @endif
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($required) required @endif
                                               @if ($readonly) readonly @endif>

                                    {{-- Email --}}
                                    @elseif ($type === 'email')
                                        <input type="email"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old }}"
                                               placeholder="{{ $placeholder ?? 'name@example.com' }}"
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($required) required @endif
                                               @if ($readonly) readonly @endif>

                                    {{-- Plain text --}}
                                    @else
                                        <input type="text"
                                               name="{{ $name }}"
                                               id="{{ $inputId }}"
                                               value="{{ $old }}"
                                               placeholder="{{ $placeholder ?? 'បញ្ចូល'.$label.'...' }}"
                                               maxlength="{{ $field['max'] ?? 191 }}"
                                               class="form-control @error($name) is-invalid @enderror"
                                               @if ($required) required @endif
                                               @if ($readonly) readonly @endif>

                                    @endif

                                    @if ($hint)
                                        <div class="form-hint">
                                            <i class="bi bi-info-circle me-1"></i>{{ $hint }}
                                        </div>
                                    @endif

                                @endif

                                @error($name)
                                    <span class="invalid-feedback d-block" id="{{ $errId }}">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </span>
                                @enderror

                            </div>

                        @endforeach

                    </div>
                </div>

                {{-- Card Actions Footer --}}
                <div class="module-form-footer">
                    <a href="{{ route("admin.{$module}.index") }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        ត្រឡប់ក្រោយ
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            {{ $editing ? 'រក្សាទុកការកែប្រែ' : 'បង្កើត'.$definition['singular'] }}
                        </button>
                    </div>
                </div>

            </div>

        </form>

    </div>

</div>

@endsection
