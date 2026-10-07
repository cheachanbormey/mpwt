<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffV2UserLinkSeeder extends Seeder
{
    /**
     * Point each login account at its record in the rebuilt staff module.
     *
     * RoleSeeder works out which person owns which account against the legacy
     * table. The clock needs the same answer on users.staff_v2_id, so the link
     * is mirrored across using the staff code they both carry.
     */
    public function run(): void
    {
        $v2ByCode = DB::table('staff_v2')
            ->pluck('id', 'staff_id')
            ->all();

        foreach (DB::table('staff')->whereNotNull('user_id')->get() as $person) {
            $v2Id = $v2ByCode[$person->staff_code] ?? null;

            if ($v2Id === null) {
                continue;
            }

            DB::table('users')
                ->where('id', $person->user_id)
                ->whereNull('staff_v2_id')
                ->update(['staff_v2_id' => $v2Id]);
        }
    }
}
