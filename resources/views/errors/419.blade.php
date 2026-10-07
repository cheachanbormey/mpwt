@extends('layouts.error')

@section('code', '419')

@section('content')

    <div class="error-box">

        <i class="bi bi-hourglass-split error-icon"></i>

        <p class="error-code">419</p>

        <h1 class="error-title">សម័យបានផុតកំណត់</h1>

        <p class="error-message">
            ទំព័របានអស់កំណត់។ សូមផ្ទុកទំព័រម្តងទៀត ហើយព្យាយាមម្តងទៀត។
        </p>

        <a href="{{ url('/') }}" class="btn btn-primary btn-sm px-3">
            <i class="bi bi-arrow-clockwise"></i>
            ផ្ទុកម្តងទៀត
        </a>

    </div>

@endsection