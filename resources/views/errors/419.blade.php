@extends('layouts.error')

@section('code', '419')
@section('title', 'សម័យដំណើរការបានផុតកំណត់')
@section('themeClass', 'theme-purple')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-hourglass-split"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-clock-history"></i> កូដ 419 &bull; អស់សុពលភាព
        </span>
    </div>

    <h2 class="error-code-number error-highlight">419</h2>

    <h1 class="error-title">សម័យដំណើរការបានផុតកំណត់</h1>

    <p class="error-description">
        ដោយសារទុកចោលយូរពេក ឬសម័យសុវត្ថិភាព (Session) ត្រូវបានផុតកំណត់។ សូមចុចផ្ទុកទំព័រឡើងវិញ ដើម្បីបន្តការងាររបស់អ្នក។
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