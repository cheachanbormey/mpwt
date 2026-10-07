<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffV2Seeder extends Seeder
{
    /**
     * Seed the staff_v2 table from the legacy staff table.
     *
     * A fresh database runs the migrations against empty tables, so nothing is
     * carried across for these. Copying here keeps the same staff_id codes the
     * rest of the module already uses, and leaves a database that was migrated
     * from real data alone.
     */
    public function run(): void
    {
        $positionMap = $this->mapPositions();

        foreach (DB::table('staff')->orderBy('id')->get() as $person) {
            // staff_code is unique in the old table, so the copy is one to one.
            $exists = DB::table('staff_v2')
                ->where('staff_id', $person->staff_code)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('staff_v2')->insert([
                'office_id' => $person->office_id,
                'position_id' => $positionMap[$person->position_id] ?? null,
                'staff_id' => $person->staff_code,
                'full_name' => $person->name_km,
                'gender' => $person->gender,
                'email' => $person->email,
                'phone' => $person->phone,
                'telegram' => null,
                // employment_status held six states; the rebuilt module only
                // distinguishes working from not working.
                'status' => $person->employment_status === 'active' ? 'active' : 'inactive',
                'system_role' => 'staff',
                'created_at' => $person->created_at,
                'updated_at' => $person->updated_at,
            ]);
        }
    }

    /**
     * Legacy position id => position_v2 id, matched on the Khmer name.
     *
     * @return array<int, int>
     */
    private function mapPositions(): array
    {
        $map = [];

        foreach (DB::table('positions')->orderBy('id')->get() as $position) {
            $match = DB::table('positions_v2')
                ->where('position_name_kh', $position->name)
                ->value('id');

            if ($match !== null) {
                $map[$position->id] = $match;
            }
        }

        return $map;
    }
}
