@extends('layouts.error')

@section('code', '503')
@section('title', 'ប្រព័ន្ធកំពុងដំណើរការថែទាំ')
@section('themeClass', 'theme-blue')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-cone-striped"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-wrench-adjustable-circle"></i> កូដ 503 &bull; កំពុងថែទាំប្រព័ន្ធ
        </span>
    </div>

    <h2 class="error-code-number error-highlight">503</h2>

    <h1 class="error-title">ប្រព័ន្ធកំពុងដំណើរការថែទាំជាបណ្តោះអាសន្ន</h1>

    <p class="error-description">
        យើងខ្ញុំកំពុងធ្វើការកែលម្អ និងថែទាំប្រព័ន្ធដើម្បីបង្កើនប្រសិទ្ធភាពការងារ។ ប្រព័ន្ធនឹងវិលត្រឡប់មកដំណើរការធម្មតាវិញក្នុងពេលឆាប់ៗនេះ។
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

@endsection
