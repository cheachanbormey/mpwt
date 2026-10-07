<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Concerns\ResolvesCurrentStaff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    use ResolvesCurrentStaff;

    /**
     * Show the signed-in person's own profile.
     */
    public function show(Request $request): View
    {
        $staff = $this->currentStaffOrFail($request);

        $staff->load(['office.department', 'position', 'ledGroups', 'groups']);

        return view('staff.profile', [
            'staff' => $staff,
        ]);
    }
}
