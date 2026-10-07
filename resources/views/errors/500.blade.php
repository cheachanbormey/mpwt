@extends('layouts.error')

@section('code', '500')

@section('content')

    <div class="error-box">

        <i class="bi bi-tools error-icon"></i>

        <p class="error-code">500</p>

        <h1 class="error-title">កំហុសម្នាក់មិនអាចបាន</h1>

        <p class="error-message">
            មានកំហុសមួយបានកើតឡើង។ ក្រុមការងារបានទទួលស្គាល់ហើយ សូមព្យាយាមនៅពេលក្រោយ។
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-house-door"></i>
            ត្រឡប់ទៅទំព័រដើម
        </a>

        @if (config('app.debug'))
            <details class="mt-3 text-start">
                <summary class="error-message" style="cursor:pointer">
                    ព័ត៌មានបច្ចេកទេស (តែមើលនៅក្នុងរបៀប Debug)
                </summary>
                <pre class="bg-light p-2 mt-2 small text-break">{{ $exception->message }}</pre>
            </details>
        @endif

    </div>

@endsection