@extends('layouts.app')

@section('title', 'QR Code វត្តមាន')
@section('titleSuffix', $staff->staff_id)

@section('content')
<div class="module-page">
    <div class="page-header">
        <h1>
            <i class="bi bi-qr-code"></i>
            QR Code សម្រាប់ចូលការងារ
        </h1>
        <p>ស្កេន QR Code នេះដើម្បីកត់ត្រាម៉ោងចូលការងាររបស់ {{ $staff->full_name }}</p>
    </div>

    <div class="dashboard-card text-center">
        <div class="card-body py-5">
            <div class="mb-4">
                <img src="{{ $qrUrl }}" alt="QR Code ចូលការងារ" style="max-width: 300px; width: 100%;">
            </div>
            <h4 class="mb-3">ស្កេន QR Code ដើម្បីចូលការងារ</h4>
            <p class="text-body-secondary mb-4">
                QR Code នេះនាំទៅកាន់ {{ $checkInUrl }}<br>
                បុគ្គលិកអាចស្កេនវាដោយប្រើកាមេរ៉ាអំពីឧបករណ៍របស់ខ្លួនដើម្បីចូលការងារភ្លាមៗ។
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('staff.attendance') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i>
                    ត្រឡប់ទៅវត្តមាន
                </a>
                <button onclick="window.print()" class="btn btn-outline-primary">
                    <i class="bi bi-printer"></i>
                    បោះពុម្ព QR Code
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
