<!DOCTYPE html>
<html lang="km">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        <title>
            Login - MPWT HRMS
        </title>
        <!-- &middot; {{ config('app.name') }} -->
    </title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family= fire-animation">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- {{-- Vite: Bootstrap 5 + Bootstrap Icons + HRMS css --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #073b9f;
            font-family: Arial, "Khmer OS", sans-serif;
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 25px 15px;
        }
        body {
        margin: 0;
        min-height: 100vh;
        background: #073b9f;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        /* Logo */

        .logo {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo img {
            width: 95px;
            height: 95px;
            object-fit: contain;
        }

        /* Ministry title */

        .ministry-title {
            text-align: center;
            color: white;
            margin-bottom: 25px;
        }

        .ministry-title h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .ministry-title h2 {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: .5px;
            margin: 0;
        }

        /* Login Card */

        .login-card {
            background: #fff;
            border-radius: 10px;
            padding: 28px 25px 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .15);
        }

        .login-title {
            text-align: center;
            font-size: 23px;
            font-weight: 600;
            color: #222;
            margin-bottom: 35px;
        }

        /* Labels */

        .form-label {
            font-size: 14px;
            color: #555;
            margin-bottom: 7px;
        }

        /* Input */

        .input-group {
            margin-bottom: 15px;
        }

        .input-group-text {
            background: #fff;
            border-right: 0;
            color: #777;
        }

        .form-control {
            height: 40px;
            border-left: 0;
            font-size: 14px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 2px rgba(7, 59, 159, .08);
            border-radius: 5px;
        }

        .password-toggle {
            background: white;
            border-left: 0;
            cursor: pointer;
            color: #777;
        }

        /* Remember + Forgot */

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0 25px;
            font-size: 13px;
        }

        .remember {
            color: #555;
        }

        .forgot-password {
            color: #777;
            text-decoration: none;
        }

        .forgot-password:hover {
            color: #073b9f;
        }

        /* Login Button */

        .btn-login {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 6px;
            background: #073b9f;
            color: white;
            font-size: 18px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #052f80;
            color: white;
        }
        
        /* Register */

        .register-text {
            text-align: center;
            margin-top: 18px;
            margin-bottom: 0;
            color: #555;
            font-size: 13px;
        }

        .register-text a {
            color: #073b9f;
            text-decoration: none;
            font-weight: 500;
        }

        /* Mobile */

        @media (max-width: 480px) {

            .login-wrapper {
                padding: 20px 15px;
            }

            .logo img {
                width: 80px;
                height: 80px;
            }

            .ministry-title h1 {
                font-size: 20px;
            }

            .ministry-title h2 {
                font-size: 12px;
            }

            .login-card {
                padding: 25px 20px;
            }

            .login-title {
                font-size: 20px;
            }
        }

    </style>
</head>

<body>

<div class="login-wrapper">

    <div class="login-container">

        {{-- Logo --}}
        <div class="logo">
            <img
                src="{{ asset('images/mpwt-logo.png') }}"
                alt="MPWT Logo"
            >
        </div>

        {{-- Ministry Name --}}
        <div class="ministry-title">

            <h1>
                ក្រសួងសាធារណការ និងដឹកជញ្ជូន
            </h1>

            <h2>
                MINISTRY OF PUBLIC WORKS AND TRANSPORT
            </h2>

        </div>


        {{-- Login Card --}}
        <div class="login-card">

            <h3 class="login-title">
                ចូលប្រព័ន្ធ
                <small class="d-block fw-normal">ចូលប្រើប្រាស់គណនីរបស់អ្នក</small>
            </h3>


            {{-- Login Form --}}
            <form method="POST" action="{{ route('login') }}">

                @csrf


                {{-- Username --}}
                <div class="mb-3">

                    <label class="form-label">
                        អ្នកប្រើ
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>

                        <input
                            type="text"
                            name="email"
                            class="form-control"
                            placeholder=""
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >

                    </div>

                    @error('email')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Password --}}
                <div class="mb-3">

                    <label class="form-label">
                        កូដសម្ងាត់
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control"
                            required
                        >

                        <button
                            type="button"
                            class="input-group-text password-toggle"
                            onclick="togglePassword()"
                        >
                            <i
                                class="bi bi-eye-slash"
                                id="passwordIcon"
                            ></i>
                        </button>

                    </div>

                    @error('password')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- Remember / Forgot --}}
                <div class="login-options">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                        >

                        <label
                            class="form-check-label remember"
                            for="remember"
                        >
                            ចងចាំ
                        </label>

                    </div>


                    <a
                        href="#"
                        class="forgot-password"
                    >
                        ភ្លេចលេខសម្ងាត់?
                    </a>

                </div>


                {{-- Login Button --}}
                <button
                    type="submit"
                    class="btn btn-login"
                >
                    ចូលប្រើប្រាស់
                </button>


                {{-- Register --}}
                <p class="register-text">

                    មិនទាន់មានគណនី?
                    
                    <a href="#">
                        ចុះឈ្មោះ
                    </a>

                </p>

            </form>

        </div>

    </div>

</div>


<script>

function togglePassword() {

    const password = document.getElementById('password');
    const icon = document.getElementById('passwordIcon');

    if (password.type === 'password') {

        password.type = 'text';

        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');

    } else {

        password.type = 'password';

        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');

    }

}

</script>

</body>
</html>