@extends('layouts.error')

@section('code', '500')
@section('title', 'កំហុសបច្ចេកទេសក្នុងប្រព័ន្ធ')
@section('themeClass', 'theme-danger')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-exclamation-triangle-fill"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-bug-fill"></i> កូដ 500 &bull; កំហុសម៉ាស៊ីនបម្រើ
        </span>
    </div>

    <h2 class="error-code-number error-highlight">500</h2>

    <h1 class="error-title">មានកំហុសបច្ចេកទេសក្នុងប្រព័ន្ធ</h1>

    <p class="error-description">
        ប្រព័ន្ធបានជួបប្រទះបញ្ហាបច្ចេកទេសដែលមិនរំពឹងទុក។ ក្រុមការងារបច្ចេកទេសត្រូវបានជូនដំណឹងរួចហើយ។ សូមព្យាយាមម្តងទៀតនៅពេលបន្តិចក្រោយ។
    </p>

    <div class="error-actions">
        <button type="button" onclick="window.location.reload()" class="error-btn-primary">
            <i class="bi bi-arrow-clockwise"></i>
            ផ្ទុកទំព័រឡើងវិញ
        </button>
        <a href="{{ url('/') }}" class="error-btn-secondary">
            <i class="bi bi-house-door-fill"></i>
            ត្រឡប់ទៅទំព័រដើម
        </a>
    </div>

    @if (config('app.debug') && isset($exception))
        <details class="error-debug-details">
            <summary class="error-debug-summary">
                <i class="bi bi-code-slash me-1"></i>
                ព័ត៌មានបច្ចេកទេស (របៀប Debug)
            </summary>
            <pre class="error-debug-pre">{{ $exception->getMessage() }}

{{ $exception->getFile() }}:{{ $exception->getLine() }}</pre>
        </details>
    @endif

@endsection