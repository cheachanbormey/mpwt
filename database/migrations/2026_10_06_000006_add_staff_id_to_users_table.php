<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Links a login account to its row in the rebuilt staff module.
 *
 * The original module linked accounts through `staff.user_id`. Attendance now
 * hangs off `staff_v2`, so a signed-in person needs their own link there too.
 * Existing accounts are matched on email, which is the one field the two
 * tables share.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('staff_id')
                ->nullable()
                ->unique()
                ->constrained('staff')
                ->nullOnDelete();
        });

        foreach (DB::table('users')->whereNotNull('email')->get() as $user) {
            $staffId = DB::table('staff')
                ->where('email', $user->email)
                ->value('id');

            if ($staffId === null) {
                continue;
            }

            DB::table('users')->where('id', $user->id)->update([
                'staff_id' => $staffId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropUnique(['staff_id']);
            $table->dropColumn('staff_id');
        });
    }
};
