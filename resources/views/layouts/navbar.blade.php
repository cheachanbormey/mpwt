<header class="top-navbar">

    {{-- Left --}}
    <div class="navbar-left">

        <button type="button" class="sidebar-toggle" id="sidebarToggle" title="បិទ/បើកមើលសារ">
            <i class="bi bi-list"></i>
        </button>

        <div class="navbar-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                placeholder="ស្វែងរកតាមឈ្មោះ តួនាទី..."
            >

        </div>

    </div>


    {{-- Right --}}
    <div class="navbar-right">

        {{--
            User dropdown. The name and role always come from the signed-in
            account, and signing out is a POST form so it cannot be triggered
            by a link.
        --}}
        <div class="dropdown">

            <button class="user-profile"
                    type="button"
                    data-bs-toggle="dropdown"
                    data-bs-display="static"
                    aria-expanded="false"
                    aria-label="អ្នកប្រើប្រាស់">

                <div class="user-avatar">
                    {{ auth()->user()->initials }}
                </div>

                <div class="user-info d-none d-md-block">

                    <strong>
                        {{ auth()->user()->display_name }}
                    </strong>

                    <small>
                        {{ auth()->user()->role_label }}
                    </small>

                </div>

                <i class="bi bi-chevron-down user-chevron"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end user-dropdown">

                {{-- Identity --}}
                <li class="user-dropdown-header">
                    <div class="user-dropdown-identity">

                        <div class="user-avatar user-avatar-lg">
                            {{ auth()->user()->initials }}
                        </div>

                        <div>

                            <strong>{{ auth()->user()->display_name }}</strong>

                            <small>{{ auth()->user()->email }}</small>

                            @if (auth()->user()->staff)
                                <small>
                                    {{ auth()->user()->staff->staff_code }}
                                    &middot;
                                    {{ auth()->user()->staff->office?->name }}
                                </small>
                            @endif

                        </div>

                    </div>

                    <span class="badge role-badge role-{{ auth()->user()->role }}">
                        <i class="bi bi-{{ auth()->user()->isAdmin() ? 'shield-lock' : 'person' }}"></i>
                        {{ auth()->user()->role_label }}
                    </span>

                </li>

                <li><hr class="dropdown-divider"></li>

                {{-- Self-service --}}
                <li>
                    <a class="dropdown-item" href="{{ route('staff.profile') }}">
                        <i class="bi bi-person-circle"></i>
                        ព័ត៌មានផ្ទាល់ខ្លួន
                    </a>
                </li>

                <li>
                    <a class="dropdown-item">
                        <i class="bi bi-gear"></i>
                         ការកំណត់ប្រព័ន្ធ
                    </a>
                </li>


                <li><hr class="dropdown-divider"></li>

                {{--
                    POST only. Laravel's CSRF token plus @method('DELETE') is
                    not needed here because the route itself is POST.
                --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i>
                            ចាកចេញពីប្រព័ន្ធ
                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>

</header>
