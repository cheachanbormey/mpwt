<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ResolvesCurrentStaff;

    //  public function index()
    // {
    //     // Sample data for the profile page
    //     $staff = [
    //         'staff_id' => 'staff_id',
    //         'name_km' => 'name_km',
    //         'name_en' => 'name_en',
    //         'email' => 'email',
    //         'phone' => 'phone',
    //         'telegram' => 'telegram',
    //         'department' => 'department',
    //         'office' => 'office',
    //         'position' => 'position',
    //         'status' => 'status',
    //         // 'system_year' => '2026',
    //     ];

    //     return view('staff.profile', compact('staff'));
    // }

    // public function index()
    // {
    //     $staff = auth()->user()->staff()
    //         ->with('office')
    //         ->firstOrFail();

    //     return view('staff.profile', compact('staff'));
    // }

    public function index()
    {
        $staff = auth()->user()->staff()
            ->with('office.department')
            ->firstOrFail();

        return view('staff.profile', compact('staff'));
    }
    /**
     * Show the signed-in person's own profile.
     */
    public function show(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        // $staff->load(['office.department', 'position', 'groups']);

        return view('staff.profile', [
            'staff' => $staff,
        ]);
    }
}
