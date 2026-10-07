@extends('layouts.error')

@section('code', '404')

@section('content')

    <div class="error-box">

        <i class="bi bi-compass error-icon"></i>

        <p class="error-code">404</p>

        <h1 class="error-title">រកមិនឃើញទំព័រ</h1>

        <p class="error-message">
            ទំព័រដែលអ្នកស្វែងរកមិនត្រឹមតែង់ឡើយ ឬតែង់បានលុបចេញពីប្រព័ន្ធ។
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-house-door"></i>
            ត្រឡប់ទៅទំព័រដើម
        </a>

    </div>

@endsection