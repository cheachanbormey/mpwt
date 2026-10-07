@extends('layouts.app')

@section('title', 'ប្រវត្តិរូបរបស់ខ្ញុំ')
@section('titleSuffix', $staff->staff_code)

@section('content')

<div class="module-page">

    <div class="page-header">

        <h1>
            <i class="bi bi-person-circle"></i>
            ប្រវត្តិរូបរបស់ខ្ញុំ
        </h1>

        <p>ព័ត៌មានផ្ទាល់ខ្លួន អាចមើលបានតែខ្ញុំ និងថ្ងៃត្រូវបានកែសម្រួលដោយអ្នកគ្រប់គ្រង</p>

    </div>


    {{-- Identity card --}}
    <div class="dashboard-card mb-4">

        <div class="profile-header">

            <div class="user-avatar profile-avatar">
                {{ $staff->initials }}
            </div>

            <div>

                <h2 class="mb-1">{{ $staff->name_km }}</h2>

                @if ($staff->name_en)
                    <p class="text-body-secondary mb-2">{{ $staff->name_en }}</p>
                @endif

                <div class="d-flex flex-wrap gap-2">

                    <span class="badge role-badge role-{{ auth()->user()->role }}">
                        <i class="bi bi-{{ auth()->user()->isAdmin() ? 'shield-lock' : 'person' }}"></i>
                        {{ auth()->user()->role_label }}
                    </span>

                    <span class="badge-status {{ $staff->isActive() ? 'present' : 'absent' }}">
                        {{ \App\Models\EmploymentStatus::labels()[$staff->employment_status] ?? $staff->employment_status }}
                    </span>

                    <span class="badge text-bg-light border">{{ $staff->staff_code }}</span>

                </div>

            </div>

        </div>

    </div>


    <div class="row g-3">

        {{-- Personal details --}}
        <div class="col-lg-6">

            <div class="dashboard-card h-100">

                <div class="card-header-custom">
                    <div>
                        <h3><i class="bi bi-person-vcard"></i> ព័ត៌មានផ្ទាល់ខ្លួន</h3>
                    </div>
                </div>

                <table class="table attendance-table mb-0">

                    <tbody>
                        <tr>
                            <th>លេខបុគ្គលិក</th>
                            <td class="text-end font-monospace">{{ $staff->staff_code }}</td>
                        </tr>
                        <tr>
                            <th>យេស្តិ</th>
                            <td class="text-end">
                                @php
                                    $genders = ['male' => 'ប្រុស', 'female' => 'ស្រី', 'other' => 'ផ្សេងៗ'];
                                @endphp
                                {{ $genders[$staff->gender] ?? $staff->gender ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <th>ថ្ងៃខែឆ្នាំកើត</th>
                            <td class="text-end font-monospace">
                                {{ $staff->date_of_birth?->format('d/m/Y') ?? '—' }}
                            </td>
                        </tr>
                        <tr>
                            <th>លេខអត្តសញ្ញាណ</th>
                            <td class="text-end font-monospace">{{ $staff->national_id ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>លេខទូរស័ព្ទ</th>
                            <td class="text-end font-monospace">{{ $staff->phone ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>អ៊ីមែល</th>
                            <td class="text-end">{{ $staff->email ?? auth()->user()->email }}</td>
                        </tr>
                        <tr>
                            <th>អាសយដ្ឋាន</th>
                            <td class="text-end">{{ $staff->address ?? '—' }}</td>
                        </tr>
                    </tbody>

                </table>

            </div>

        </div>


        {{-- Work details --}}
        <div class="col-lg-6">

            <div class="dashboard-card h-100 mb-3">

                <div class="card-header-custom">
                    <div>
                        <h3><i class="bi bi-briefcase"></i> ព័ត៌មានការងារ</h3>
                    </div>
                </div>

                <table class="table attendance-table mb-0">

                    <tbody>
                        <tr>
                            <th>ផ្ទាំង</th>
                            <td class="text-end">{{ $staff->office?->department?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ការងារ</th>
                            <td class="text-end">{{ $staff->office?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>តួនាទី</th>
                            <td class="text-end">{{ $staff->position?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>កាលបរិច្ឆេទចូលរួមការងារ</th>
                            <td class="text-end font-monospace">{{ $staff->hire_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>ប្រាក់ឈ្នួលមូលដ្ឋាន</th>
                            <td class="text-end font-monospace">{{ $staff->base_salary ?? '—' }}</td>
                        </tr>
                    </tbody>

                </table>

            </div>


            {{-- Groups --}}
            <div class="dashboard-card">

                <div class="card-header-custom">
                    <div>
                        <h3><i class="bi bi-people-fill"></i> ក្រុមការងារ</h3>
                    </div>
                </div>

                @if ($staff->groups->isEmpty())
                    <p class="text-body-secondary mb-0 p-3">មិនទាន់មានក្រុមការងារណាៗ។</p>
                @else
                    <div class="p-3 d-flex flex-wrap gap-2">
                        @foreach ($staff->groups as $group)
                            <span class="badge text-bg-light border">
                                {{ $group->name }}
                                @if ($group->pivot->is_leader)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </div>

</div>

@endsection
