@extends('layouts.error')

@section('code', '401')
@section('title', 'ទាមទារការផ្ទៀងផ្ទាត់អត្តសញ្ញាណ')
@section('themeClass', 'theme-amber')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-person-x-fill"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-person-lock"></i> កូដ 401 &bull; មិនទាន់ផ្ទៀងផ្ទាត់
        </span>
    </div>

    <h2 class="error-code-number error-highlight">401</h2>

    <h1 class="error-title">ទាមទារការចូលគណនី</h1>

    <p class="error-description">
        លោកអ្នកត្រូវតែចូលទៅក្នុងគណនីរបស់អ្នកជាមុនសិន ដើម្បីទទួលបានសិទ្ធិចូលដំណើរការទំព័រនេះ។
    </p>

    <div class="error-actions">
        <a href="{{ route('login') }}" class="error-btn-primary">
            <i class="bi bi-box-arrow-in-right"></i>
            ចូលគណនី
        </a>
        <a href="{{ url('/') }}" class="error-btn-secondary">
            <i class="bi bi-house-door-fill"></i>
            ទំព័រដើម
        </a>
    </div>

@endsection
