@php
    /**
     * Role-based navigation.
     *
     * Administrators see every management module listed in config/hrms.php,
     * grouped into clean collapsible nested menus.
     * Staff see the dashboard and their own self-service pages.
     */
    $adminModules = auth()->user()?->isAdmin()
        ? \App\Http\Controllers\Admin\ModuleController::registry()
        : [];

    $groups = [
        'organization' => [
            'label' => 'រចនាសម្ព័ន្ធអង្គភាព',
            'icon'  => 'bi-diagram-3',
            'keys'  => ['departments', 'offices', 'positions', 'locations'],
        ],
        'staff_mgmt' => [
            'label' => 'គ្រប់គ្រងបុគ្គលិក',
            'icon'  => 'bi-people',
            'keys'  => ['staff', 'groups'],
        ],
        'attendance_leaves' => [
            'label' => 'វត្តមាន និងច្បាប់',
            'icon'  => 'bi-calendar2-check',
            'keys'  => ['attendances', 'leave_requests', 'leave_types'],
        ],
        'meetings_mgmt' => [
            'label' => 'គ្រប់គ្រងកិច្ចប្រជុំ',
            'icon'  => 'bi-calendar-event',
            'keys'  => ['meetings', 'meeting_types', 'meeting_participants', 'meeting_agendas'],
        ],
    ];

    $groupedKeys = collect($groups)->pluck('keys')->flatten()->all();
    $ungroupedModules = collect($adminModules)->except($groupedKeys);
@endphp

<aside class="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="logo-circle">
            <img src="{{ asset('images/mpwt-logo.png') }}" alt="MPWT Logo">
        </div>

        <div class="logo-text">
            <strong>HRM Portal</strong>
        </div>
    </div>


    {{-- Menu --}}
    <nav class="sidebar-menu">

        {{-- Dashboard: everyone --}}
        <a href="{{ route('dashboard') }}" class="sidebar-item" title="ផ្ទាំងគ្រប់គ្រង">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>ផ្ទាំងគ្រប់គ្រង</span>
        </a>

        <a href="{{ route('staff.staff.attendance') }}" class="sidebar-item" title="វត្តមាន CheckinMe">
            <i class="bi bi-clock-history"></i>
            <span>វត្តមាន CheckinMe</span>
        </a>

        <a href="{{ route('staff.staff.leave-requests.index') }}" class="sidebar-item" title="ស្នើសុំច្បាប់ឈប់សម្រាក">
            <i class="bi bi-envelope-paper"></i>
            <span>ស្នើសុំច្បាប់ឈប់សម្រាក</span>
        </a>

        <a href="{{ route('staff.staff.meetings.index') }}" class="sidebar-item" title="កិច្ចប្រជុំ និង ស្កេនវត្តមាន">
            <i class="bi bi-calendar-event"></i>
            <span>កិច្ចប្រជុំ និង ស្កេនវត្តមាន</span>
        </a>

        {{-- Self-service: everyone with a staff record --}}
        <a href="{{ route('staff.profile') }}" class="sidebar-item" title="ព័ត៌មានផ្ទាល់ខ្លួន">
            <i class="bi bi-person-circle"></i>
            <span>ព័ត៌មានផ្ទាល់ខ្លួន</span>
        </a>


        {{-- Administration: grouped into nested menus --}}
        @if ($adminModules !== [])

            <div class="sidebar-section-label">
                ការគ្រប់គ្រង
            </div>

            @foreach ($groups as $groupId => $group)
                @php
                    $groupModules = collect($adminModules)->only($group['keys']);
                    if ($groupModules->isEmpty()) {
                        continue;
                    }
                    $isGroupActive = $groupModules->keys()->contains(function ($k) {
                        return request()->is("admin/{$k}*");
                    });
                @endphp

                <div class="sidebar-dropdown">
                    <button type="button"
                            class="sidebar-item sidebar-dropdown-toggle {{ $isGroupActive ? 'has-active' : '' }}"
                            data-bs-toggle="collapse"
                            data-bs-target="#group-{{ $groupId }}"
                            aria-expanded="{{ $isGroupActive ? 'true' : 'false' }}"
                            title="{{ $group['label'] }}">
                        <i class="bi {{ $group['icon'] }}"></i>
                        <span>{{ $group['label'] }}</span>
                        <i class="bi bi-chevron-down sidebar-chevron ms-auto"></i>
                    </button>

                    <div class="collapse sidebar-submenu {{ $isGroupActive ? 'show' : '' }}" id="group-{{ $groupId }}">
                        @foreach ($group['keys'] as $key)
                            @if (isset($adminModules[$key]))
                                @php
                                    $mod = $adminModules[$key];
                                    $isActive = request()->is("admin/{$key}*");
                                @endphp
                                <a href="{{ route("admin.{$key}.index") }}"
                                   class="sidebar-subitem {{ $isActive ? 'active' : '' }}"
                                   title="{{ $mod['label'] }}">
                                    <i class="bi {{ $mod['icon'] }}"></i>
                                    <span>{{ $mod['label'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Fallback for any modules not explicitly assigned to a group --}}
            @foreach ($ungroupedModules as $key => $adminModule)
                <a href="{{ route("admin.{$key}.index") }}"
                   class="sidebar-item {{ request()->is("admin/{$key}*") ? 'active' : '' }}"
                   title="{{ $adminModule['label'] }}">
                    <i class="bi {{ $adminModule['icon'] }}"></i>
                    <span>{{ $adminModule['label'] }}</span>
                </a>
            @endforeach

        @endif

    </nav>

</aside>