<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'ចូលប្រព័ន្ធ')
        &middot; {{ config('app.name') }}
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,700&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Vite: Bootstrap 5 + Bootstrap Icons + HRMS css --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="auth-body">

    <div class="auth-wrapper">

        {{-- Brand panel (left on desktop) --}}
        <div class="auth-brand">

            <div class="auth-brand-inner">

                <div class="auth-logo">
                    <i class="bi bi-building"></i>
                </div>

                <h1 class="auth-brand-title">
                    ក្រសួងសាធារណការ<br>
                    និងដឹកជញ្ជូន
                </h1>

                <p class="auth-brand-sub">
                    ប្រព័ន្ធគ្រប់គ្រងធនធានមនុស្ស (HRMS)
                </p>

                <ul class="auth-brand-points">
                    <li><i class="bi bi-check-circle-fill"></i> គ្រប់គ្រងបុគ្គលិក</li>
                    <li><i class="bi bi-check-circle-fill"></i> វត្តមានប្រចាំថ្ងៃ</li>
                    <li><i class="bi bi-check-circle-fill"></i> ស្នើសុំច្បាប់ឈប់សម្រាក</li>
                    <li><i class="bi bi-check-circle-fill"></i> កិច្ចប្រជុំ</li>
                </ul>

            </div>

        </div>

        {{-- Form panel --}}
        <div class="auth-panel">

            @yield('content')

        </div>

    </div>

    @stack('scripts')

</body>

</html>