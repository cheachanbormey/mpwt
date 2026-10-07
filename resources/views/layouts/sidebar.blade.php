@php
    /**
     * Role-based navigation.
     *
     * Administrators see every management module listed in config/hrms.php.
     * Staff see the dashboard and their own self-service pages. A person
     * never sees a menu entry they are not allowed to open.
     */
    $adminModules = auth()->user()?->isAdmin()
        ? \App\Http\Controllers\Admin\ModuleController::registry()
        : [];
@endphp

<aside class="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-circle">
            <img src="{{ asset('images/mpwt-logo.png') }}" alt="MPWT Logo" class="logo-circle" > 
        </div>

        <div class="logo-text">
            <div class="logo-kh">
                ក្រសួងសាធារណការ
            </div>

            <div>
                
            </div>

            <div class="logo-kh">
                និងដឹកជញ្ជូន
            </div>

            <small>
                MINISTRY OF PUBLIC
                <br>
                WORKS AND TRANSPORT
            </small>
        </div>

    </div>


    {{-- Menu --}}
    <nav class="sidebar-menu">

        {{-- Dashboard: everyone --}}
        <a href="{{ route('dashboard') }}" class="sidebar-item">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>ផ្ទាំងគ្រប់គ្រង</span>
        </a>

<!-- 
        {{-- Self-service: everyone with a staff record --}}
        <a href="{{ route('staff.profile') }}" class="sidebar-item">
            <i class="bi bi-person-circle"></i>
            <span>ប្រវត្តិរូបរបស់ខ្ញុំ</span>
        </a>

        <a href="{{ route('staff.attendance') }}" class="sidebar-item">
            <i class="bi bi-clock-history"></i>
            <span>វត្តមានរបស់ខ្ញុំ</span>
        </a>

        <a href="{{ route('staff.leave-requests.index') }}" class="sidebar-item">
            <i class="bi bi-envelope-paper"></i>
            <span>សុំច្បាប់</span>
        </a>

        <a href="{{ route('staff.meetings.index') }}" class="sidebar-item">
            <i class="bi bi-calendar-event"></i>
            <span>កិច្ចប្រជុំ</span>
        </a>


        {{-- Administration: administrators only --}}
        @if ($adminModules !== [])

            <div class="sidebar-section-label">
                ការគ្រប់គ្រង
            </div>

            @foreach ($adminModules as $key => $adminModule)
                <a href="{{ route("admin.{$key}.index") }}" class="sidebar-item">
                    <i class="bi {{ $adminModule['icon'] }}"></i>
                    <span>{{ $adminModule['label'] }}</span>
                </a>
            @endforeach

        @endif

    </nav>

    {{-- Signed-in identity at the foot of the sidebar --}}
    <div class="sidebar-footer">

        <div class="user-avatar">
            {{ auth()->user()?->initials }}
        </div>

        <div class="sidebar-footer-text">
            <strong>{{ auth()->user()?->display_name }}</strong>
            <small>{{ auth()->user()?->role_label }}</small>
        </div>

    </div> -->

</aside>
