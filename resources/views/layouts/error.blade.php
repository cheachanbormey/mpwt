<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('code') - @yield('title', 'កំហុស') &middot; {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,700&family=Noto+Sans+Khmer:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="error-body @yield('themeClass')">

    <div class="error-ambient-glow" aria-hidden="true"></div>

    <div class="error-wrapper">
        <div class="error-container">

            {{-- Ministry Branding --}}
            <a href="{{ url('/') }}" class="error-branding">
                <img src="{{ asset('images/mpwt-logo.png') }}" alt="MPWT Logo" class="error-branding-logo">
                <div class="error-branding-text">
                    <h1 class="error-branding-title">ក្រសួងសាធារណការ និងដឹកជញ្ជូន</h1>
                    <p class="error-branding-sub">ប្រព័ន្ធគ្រប់គ្រងធនធានមនុស្ស (HRMS)</p>
                </div>
            </a>

            {{-- Error Card --}}
            <div class="error-card">
                @yield('content')
            </div>

            {{-- Footer Note --}}
            <p class="error-footer-text text-center">
                ប្រសិនបើបញ្ហានេះនៅតែបន្តកើតមាន សូមទាក់ទងមកកាន់អ្នកគ្រប់គ្រងប្រព័ន្ធ HRMS<br>
                &copy; {{ date('Y') }} ក្រសួងសាធារណការ និងដឹកជញ្ជូន
            </p>

        </div>
    </div>

</body>

</html>