{{-- =========================================================
     resources/views/partials/flash.blade.php
     Shows session flash messages sent from controllers:
         return back()->with('success', 'បានរក្សាទុកដោយជោគជ័យ');
         return back()->with('error',   'មិនអាចធ្វើបាន');
     ========================================================= --}}

<div class="flash-container">

    @foreach ([
        'success' => ['icon' => 'bi-check-circle-fill',   'class' => 'alert-success'],
        'error'   => ['icon' => 'bi-x-circle-fill',       'class' => 'alert-danger'],
        'warning' => ['icon' => 'bi-exclamation-triangle-fill', 'class' => 'alert-warning'],
        'info'    => ['icon' => 'bi-info-circle-fill',    'class' => 'alert-info'],
    ] as $key => $style)

        @if (session($key))
            <div class="alert {{ $style['class'] }} alert-dismissible flash-alert"
                 role="alert"
                 data-auto-dismiss="5000">

                <i class="bi {{ $style['icon'] }} me-1"></i>
                <span>{{ session($key) }}</span>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>
        @endif

    @endforeach

    {{-- Any custom message passed as session('message') --}}
    @if (session('message'))
        <div class="alert alert-info alert-dismissible flash-alert" role="alert" data-auto-dismiss="5000">
            <i class="bi bi-info-circle-fill me-1"></i>
            <span>{{ session('message') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation errors shown once as a summary block --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible flash-alert" role="alert">
            <i class="bi bi-exclamation-octagon-fill me-1"></i>
            <strong>សូមកែកំហុសផងដូចខាងក្រោម៖</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

</div>