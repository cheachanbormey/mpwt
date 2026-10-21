@extends('layouts.error')

@section('code', '404')
@section('title', 'រកមិនឃើញទំព័រ')
@section('themeClass', 'theme-blue')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-compass-fill"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-search"></i> កូដ 404 &bull; រកមិនឃើញ
        </span>
    </div>

    <h2 class="error-code-number error-highlight">404</h2>

    <h1 class="error-title">រកមិនឃើញទំព័រដែលអ្នកស្នើសុំ</h1>

    <p class="error-description">
        ទំព័រដែលលោកអ្នកកំពុងស្វែងរកប្រហែលជាត្រូវបានផ្លាស់ប្តូរឈ្មោះ លុបចេញពីប្រព័ន្ធ ឬតំណភ្ជាប់មិនត្រឹមត្រូវឡើយ។
    </p>

    <div class="error-actions">
        <a href="{{ url('/') }}" class="error-btn-primary">
            <i class="bi bi-house-door-fill"></i>
            ត្រឡប់ទៅទំព័រដើម
        </a>
        <button type="button" onclick="window.history.back()" class="error-btn-secondary">
            <i class="bi bi-arrow-left"></i>
            ត្រឡប់ក្រោយ
        </button>
    </div>

@endsection