@extends('layouts.app')

@section('title', 'ស្នើសំឡេង')
@section('titleSuffix', $staff->staff_code)

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-envelope-plus"></i>
            ស្នើសុំច្បាប់ថ្មី
        </h1>

        <p>ប៉ុណ្ណោះបញ្ចូលព័ត៌មាន។ ច្បាប់នឹងត្រូវបានបោះពុម្ពទៅចុចហត្ថលេខាដោយដៃ។</p>

    </div>


    <form method="POST"
          action="{{ route('staff.staff.leave-requests.store') }}"
          class="module-form"
          novalidate>

        @csrf

        <div class="dashboard-card">

            <div class="row g-3">

                {{-- Leave type --}}
                <div class="col-md-6">

                    <label for="leave_type_id" class="form-label">
                        ប្រភេទច្បាប់ <span class="text-danger">*</span>
                    </label>

                    <select name="leave_type_id"
                            id="leave_type_id"
                            class="form-select @error('leave_type_id') is-invalid @enderror"
                            required>

                        <option value="">— ជ្រើសរើស —</option>

                        @foreach ($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->id }}"
                                    {{ (string) old('leave_type_id') === (string) $leaveType->id ? 'selected' : '' }}
                                    @if (! $leaveType->is_active) disabled @endif>
                                {{ $leaveType->name }}
                                @if (! $leaveType->is_active) (អសកម្ម) @endif
                            </option>
                        @endforeach

                    </select>

                    @error('leave_type_id')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                </div>


                {{-- Start date --}}
                <div class="col-md-3">

                    <label for="start_date" class="form-label">
                        ចាប់ពីកាលបរិច្ឆេទ <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                           name="start_date"
                           id="start_date"
                           value="{{ old('start_date') }}"
                           class="form-control @error('start_date') is-invalid @enderror"
                           required>

                    @error('start_date')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                </div>


                {{-- End date --}}
                <div class="col-md-3">

                    <label for="end_date" class="form-label">
                        ដល់កាលបរិច្ឆេទ <span class="text-danger">*</span>
                    </label>

                    <input type="date"
                           name="end_date"
                           id="end_date"
                           value="{{ old('end_date') }}"
                           class="form-control @error('end_date') is-invalid @enderror"
                           required>

                    @error('end_date')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                </div>


                {{-- Reason --}}
                <div class="col-12">

                    <label for="reason" class="form-label">
                        មូលដ្ឋាន <span class="text-danger">*</span>
                    </label>

                    <textarea name="reason"
                              id="reason"
                              rows="5"
                              class="form-control @error('reason') is-invalid @enderror"
                              required
                              placeholder="សូមពន្យល់មូលហេតុច្បាប់របស់អ្នក…">{{ old('reason') }}</textarea>

                    @error('reason')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                    @enderror

                </div>

            </div>

        </div>


        {{-- Notice --}}
        <div class="alert alert-warning mt-3" role="alert">
            <i class="bi bi-info-circle"></i>
            ចំនួនថ្ងៃច្បាប់ត្រូវបានគណនាដោយប្រព័ន្ធ
            (មិនរាប់ថ្ងៃសៅរ៍ និងថ្ងៃអាទិត្យ)។
            ច្បាប់នេះនឹងមានស្ថាភាព <strong>កំពុងរង់ចាំ</strong> ហើយមិនអាចប្ដូរក្នុងប្រព័ន្ធបានទេ។
        </div>


        <div class="module-actions">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i>
                បញ្ជូនពាក្យសុំច្បាប់
            </button>

            <a href="{{ route('staff.staff.leave-requests.index') }}" class="btn btn-outline-secondary">
                ត្រឡប់ក្រោយ
            </a>

        </div>

    </form>

</div>

@endsection
