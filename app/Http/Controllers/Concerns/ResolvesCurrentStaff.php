<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Staff;
use App\Models\StaffV2;
use Illuminate\Http\Request;

/**
 * Resolves the staff record behind the signed-in login account.
 *
 * Self-service pages must never accept a staff id from the request, or a
 * staff member could read someone else's attendance by changing the URL.
 * The record is always taken from the session, so the only way to see
 * your own data is to be signed in as yourself.
 */
trait ResolvesCurrentStaff
{
    /**
     * The staff record linked to the signed-in account, if any.
     */
    protected function currentStaff(Request $request): ?Staff
    {
        return $request->user()?->staff;
    }

    /**
     * The staff record, or a 403 when the account has none linked.
     *
     * An administrator account without a staff record is also rejected:
     * self-service pages describe one person's data.
     */
    protected function currentStaffOrFail(Request $request): Staff
    {
        $staff = $this->currentStaff($request);

        if ($staff === null) {
            abort(403, 'គណនីនេះមិនទាន់ត្រាភ្ជាប់មកនឹងកំណត់ត្រាបុគ្គលិកទេ។');
        }

        return $staff;
    }

    /**
     * The rebuilt staff record linked to the signed-in account, if any.
     */
    protected function currentStaffV2(Request $request): ?StaffV2
    {
        return $request->user()?->staffV2;
    }

    /**
     * The rebuilt staff record, or a 403 when the account has none linked.
     */
    protected function currentStaffV2OrFail(Request $request): StaffV2
    {
        $staff = $this->currentStaffV2($request);

        if ($staff === null) {
            abort(403, 'គណនីនេះមិនទាន់ត្រាភ្ជាប់មកនឹងកំណត់ត្រាបុគ្គលិកទេ។');
        }

        return $staff;
    }
}
