@extends('layouts.error')

@section('code', '403')

@section('content')

    <div class="error-box">

        <i class="bi bi-shield-lock error-icon"></i>

        <p class="error-code">403</p>

        <h1 class="error-title">មិនមានសិទ្ធិចូល</h1>

        <p class="error-message">
            អ្នកមិនមានសិទ្ធិចូលមើលទំព័រនេះ។ សូមសុំទាក់ទងម្ចាស់ការងារ។
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-house-door"></i>
            ត្រឡប់ទៅទំព័រដើម
        </a>

    </div>

@endsection