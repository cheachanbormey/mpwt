@extends('layouts.app')

@section('title', 'ព័ត៌មានផ្ទាល់ខ្លួន')

@section('content')
<div class="profile-page">

    {{-- Profile Header --}}
    <div class="profile-card">

        <div class="profile-banner">
            <div class="ministry-title">
                <div class="ministry-logo">
                    <div class="logo-circle">
                        <img src="{{ asset('images/mpwt-logo.png') }}" alt="MPWT Logo" class="logo-circle" > 
                    </div>
                </div>

                <div>
                    <h5>ក្រសួងសាធារណការ និងដឹកជញ្ជូន</h5>
                    <small>MINISTRY OF PUBLIC WORKS AND TRANSPORT</small>
                    <div class="small mt-1">
                        HUMAN RESOURCES PORTAL
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-intro">
            <div class="avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <h5 class="mt-3 mb-1">
                {{ $staff['name_km'] }}
            </h5>

            <div class="text-muted small mb-3">
                {{ $staff['name_en'] }}
                <i class="bi bi-patch-check-fill text-primary"></i>
            </div>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <span class="profile-badge badge-position">
                    {{ $staff->position?->name_en }}
                </span>

                <span class="profile-badge badge-office">
                    {{ $staff->office?->department?->name_en}}
                </span>

                <span class="profile-badge badge-status">
                    <!-- {{ $staff['status'] }} -->
                    {{ $staff->position?->name_kh }}
                </span>
            </div>

            <div class="profile-contact">
                <span>
                    <i class="bi bi-envelope"></i>
                    {{ $staff['email'] }}
                </span>

                <span>
                    <i class="bi bi-telephone"></i>
                    {{ $staff['phone'] }}
                </span>

                <span>
                    <i class="bi bi-telegram"></i>
                    {{ $staff['telegram'] }}
                </span>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mt-1 mb-3">

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <div class="summary-icon blue">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <small>ការិយាល័យ</small>
                    <strong>{{ $staff->office?->name_en ?? ' ' }}</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <div class="summary-icon green">
                    <i class="bi bi-person-check"></i>
                </div>
                <div>
                    <small>តួនាទីបុគ្គលិក</small>
                    <strong>{{ $staff->position?->name_kh }}</strong>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="summary-card">
                <div class="summary-icon purple">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <small>System Year</small>
                    <strong>{{ $staff['system_year'] }} Active</strong>
                </div>
            </div>
        </div>

    </div>

    {{-- Personal Information --}}
    <div class="details-card">

        <div class="details-header">
            <div>
                <h5>ព័ត៌មានផ្ទាល់ខ្លួន</h5>
                <p>ព័ត៌មានបុគ្គលិក និងព័ត៌មានទំនាក់ទំនង</p>
            </div>

            <!-- <button type="button"
                    class="btn btn-primary btn-sm"
                    onclick="window.print()">
                <i class="bi bi-printer"></i>
                បោះពុម្ព
            </button> -->

            <button type="button" class="btn btn-primary btn-sm">
                កែប្រែព័ត៍មាន
            </button>
            
        </div>

        <div class="section-title">
            ១. ព័ត៌មានផ្ទាល់ខ្លួន និងទំនាក់ទំនង
        </div>

        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <small>ឈ្មោះបុគ្គលិក</small>
                        <strong>{{ $staff['name_en'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <small>អ៊ីមែល</small>
                        <strong>{{ $staff['email'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>
                    <div>
                        <small>លេខទូរស័ព្ទ</small>
                        <strong>{{ $staff['phone'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-telegram"></i>
                    </div>
                    <div>
                        <small>គណនី Telegram</small>
                        <strong>{{ $staff['telegram'] }}</strong>
                    </div>
                </div>
            </div>

        </div>

        <div class="section-title">
            ២. ការចាត់តាំងការងារផ្លូវការ
        </div>

        <div class="row g-3">

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <small>អគ្គភាព / នាយកដ្ឋាន</small>
                        <strong>{{ $staff->office?->department?->name_en}}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <small>ការិយាល័យ</small>
                        <strong>{{ $staff->office?->name_en }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div>
                        <small>តួនាទី</small>
                        <strong>{{ $staff->position?->name_en }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="info-box">
                    <div class="info-icon">
                        <i class="bi bi-card-text"></i>
                    </div>
                    <div>
                        <small>លេខសម្គាល់បុគ្គលិក</small>
                        <strong>{{ $staff['staff_id'] }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    .profile-page {
        color: #202938;
        padding-bottom: 25px;
    }

    .profile-card,
    .details-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(20, 45, 80, .04);
    }

    .profile-banner {
        height: 125px;
        padding: 20px;
        background: #19477f;
        border-bottom: 3px solid #f2b84b;
        color: #fff;
    }

    .ministry-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .ministry-title h5 {
        font-size: 15px;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .ministry-title small,
    .ministry-title .small {
        font-size: 9px;
    }

    .ministry-logo {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #19477f;
        background: #fff;
        font-size: 22px;
        flex-shrink: 0;
    }

    .profile-intro {
        padding: 0 20px 25px;
        text-align: center;
    }

    .avatar {
        margin: -36px auto 0;
        width: 76px;
        height: 76px;
        border-radius: 50%;
        border: 3px solid white;
        background: #dceaff;
        color: #19477f;
        display: grid;
        place-items: center;
        font-size: 43px;
        box-shadow: 0 1px 5px #0002;
        position: relative;
    }

    .profile-intro h5 {
        font-size: 15px;
        font-weight: 700;
    }

    .profile-badge {
        border-radius: 20px;
        padding: 6px 13px;
        font-size: 10px;
        font-weight: 600;
    }

    .badge-position {
        background: #0645a3;
        color: white;
    }

    .badge-office {
        background: #eaf0f7;
        color: #28384d;
    }

    .badge-status {
        background: #FFF8E1;
        color: #FF6F1E;
    }

    .profile-contact {
        margin-top: 14px;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px 18px;
        font-size: 11px;
    }

    .profile-contact i {
        margin-right: 4px;
        color: #19477f;
    }

    .summary-card {
        background: white;
        border-radius: 12px;
        min-height: 75px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
    }

    .summary-icon,
    .info-icon {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        border-radius: 6px;
    }

    .summary-icon {
        width: 38px;
        height: 38px;
        font-size: 19px;
    }

    .summary-icon.blue {
        color: #2465ad;
        background: #e8f2ff;
    }

    .summary-icon.green {
        color: #0bba88;
        background: #e6fff5;
    }

    .summary-icon.purple {
        color: #b44bd1;
        background: #f8e9ff;
    }

    .summary-card small,
    .info-box small {
        display: block;
        color: #626d7b;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .summary-card strong,
    .info-box strong {
        display: block;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .details-card {
        padding: 24px;
        margin-top: 14px;
    }

    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e3e8ef;
        margin-bottom: 20px;
    }

    .details-header h5 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .details-header p {
        color: #7b8491;
        font-size: 12px;
        margin-bottom: 0;
    }

    .section-title {
        color: #174a91;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .info-box {
        border: 1px solid #c9ddff;
        border-radius: 12px;
        min-height: 65px;
        padding: 13px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        transition: background .2s, border-color .2s;
    }

    .info-box:hover {
        background: #f7faff;
        border-color: #7daeff;
    }

    .info-icon {
        width: 36px;
        height: 36px;
        background: #e8f2ff;
        color: #19477f;
        font-size: 18px;
    }

    @media (max-width: 576px) {
        .profile-banner {
            padding: 16px;
            height: 115px;
        }

        .ministry-title h5 {
            font-size: 12px;
        }

        .details-card {
            padding: 16px;
        }

        .profile-contact {
            flex-direction: column;
            align-items: center;
        }
    }

    @media print {
        .sidebar, .navbar, .details-header button {
            display: none !important;
        }

        .profile-page {
            background: white;
        }

        .profile-card, .details-card, .summary-card {
            box-shadow: none;
            break-inside: avoid;
        }
    }
</style>
@endsection
