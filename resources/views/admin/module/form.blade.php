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
    <div class="page-header">

        <h1>
            <i class="bi {{ $definition['icon'] }}"></i>
            {{ $editing ? 'កែសម្រួល' : 'បង្កើត' }}{{ $definition['singular'] }}
        </h1>

        <p>{{ $definition['description'] }}</p>

    </div>


    {{-- Notice for read-only fields (the manual leave workflow) --}}
    @if ($readonlyFields->isNotEmpty())
        <div class="alert alert-warning d-flex gap-2" role="alert">
            <i class="bi bi-printer"></i>
            <div>
                <strong>សំគាល់៖</strong>
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

        <div class="dashboard-card">

            <div class="row g-3">

                @foreach ($definition['fields'] as $name => $field)

                    @php
                        $label    = $field['label'] ?? $name;
                        $required = $field['required'] ?? false;
                        $readonly = $field['readonly'] ?? false;
                        $type     = $field['type'] ?? 'text';
                        $old      = old($name, $editing ? $record->getAttribute($name) : ($field['default'] ?? null));
                        $wide     = in_array($type, ['textarea'], true);
                        $inputId  = 'field-'.$name;
                        $errId    = 'error-'.$name;
                    @endphp

                    <div class="{{ $wide ? 'col-12' : 'col-md-6' }}">

                        <label for="{{ $inputId }}" class="form-label">
                            {{ $label }}
                            @if ($required && ! $readonly)
                                <span class="text-danger">*</span>
                            @endif
                        </label>

                        {{-- Checkbox --}}
                        @if ($type === 'boolean')
                            <div class="form-check mt-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="{{ $name }}"
                                       id="{{ $inputId }}"
                                       value="1"
                                       @checked((bool) $old)>
                                <label class="form-check-label" for="{{ $inputId }}">
                                    សកម្ម
                                </label>
                            </div>

                        {{-- Select --}}
                        @elseif ($type === 'select')
                            <select name="{{ $name }}"
                                    id="{{ $inputId }}"
                                    class="form-select @error($name) is-invalid @enderror"
                                    @if ($readonly) disabled @endif
                                    @if ($required) required @endif>

                                <option value="">
                                    — ជ្រើសរើស —
                                </option>

                                @foreach ($options[$name] ?? [] as $value => $text)
                                    <option value="{{ $value }}" {{ (string) $old === (string) $value ? 'selected' : '' }}>
                                        {{ $text }}
                                    </option>
                                @endforeach

                            </select>

                            @if ($readonly)
                                {{-- Keep the stored value so the field is not cleared --}}
                                <input type="hidden" name="{{ $name }}" value="{{ $old }}">
                            @endif

                        {{-- Textarea --}}
                        @elseif ($type === 'textarea')
                            <textarea name="{{ $name }}"
                                      id="{{ $inputId }}"
                                      rows="4"
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
                                   class="form-control @error($name) is-invalid @enderror"
                                   @if ($required) required @endif
                                   @if ($readonly) readonly @endif>

                        {{-- Plain text --}}
                        @else
                            <input type="text"
                                   name="{{ $name }}"
                                   id="{{ $inputId }}"
                                   value="{{ $old }}"
                                   maxlength="{{ $field['max'] ?? 191 }}"
                                   class="form-control @error($name) is-invalid @enderror"
                                   @if ($required) required @endif
                                   @if ($readonly) readonly @endif>

                        @endif

                        @error($name)
                            <span class="invalid-feedback d-block" id="{{ $errId }}">{{ $message }}</span>
                        @enderror

                    </div>

                @endforeach

            </div>

        </div>


        {{-- Actions --}}
        <div class="module-actions">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i>
                {{ $editing ? 'រក្សាទុក' : 'បង្កើត' }}
            </button>

            <a href="{{ route("admin.{$module}.index") }}" class="btn btn-outline-secondary">
                ត្រឡប់ក្រោយ
            </a>

        </div>

    </form>

</div>

@endsection
