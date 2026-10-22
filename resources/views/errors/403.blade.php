@extends('layouts.error')

@section('code', '403')
@section('title', 'មិនមានសិទ្ធិចូលដំណើរការ')
@section('themeClass', 'theme-amber')

@section('content')

    <div class="error-icon-circle">
        <i class="bi bi-shield-lock-fill"></i>
    </div>

    <div>
        <span class="error-code-badge">
            <i class="bi bi-lock-fill"></i> កូដ 403 &bull; ការហាមឃាត់
        </span>
    </div>

    <h2 class="error-code-number error-highlight">403</h2>

    <h1 class="error-title">មិនមានសិទ្ធិចូលដំណើរការ</h1>

    <p class="error-description">
        លោកអ្នកមិនមានសិទ្ធិអនុញ្ញាតដើម្បីចូលទៅកាន់ទំព័រ ឬធនធាននេះឡើយ។ ប្រសិនបើលោកអ្នកគិតថានេះជាកំហុស សូមទាក់ទងអ្នកគ្រប់គ្រងប្រព័ន្ធរបស់អ្នក។
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