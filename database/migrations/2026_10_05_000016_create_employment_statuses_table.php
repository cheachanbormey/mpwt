<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Phase 03 - employment states become a lookup table so administrators
     * can manage them like every other reference data module.
     *
     * Note: `staff.employment_status` was an ENUM in Phase 02. MySQL cannot
     * reference an ENUM column from a foreign key (error 3780), so the
     * column is widened to VARCHAR(30) first. Existing values are carried
     * over unchanged, and the states used by Phase 02 are copied in.
     */
    public function up(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->string('employment_status', 30)->default('active')->change();
        });

        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        $now = now();

        $states = [
            ['code' => 'active', 'name' => 'ការងារ', 'sort_order' => 1],
            ['code' => 'probation', 'name' => 'ក្នុងកំឡត់សាកល្បង', 'sort_order' => 2],
            ['code' => 'suspended', 'name' => 'ផ្អាកភារការងារ', 'sort_order' => 3],
            ['code' => 'on_leave', 'name' => 'កំពុងស្មារណការ', 'sort_order' => 4],
            ['code' => 'retired', 'name' => 'ចូលនៃមុខវិជ្ជា', 'sort_order' => 5],
            ['code' => 'terminated', 'name' => 'ចប់ផ្ទាល់ការងារ', 'sort_order' => 6],
        ];

        foreach ($states as $state) {
            DB::table('employment_statuses')->insert($state + [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('employment_status')
                ->references('code')
                ->on('employment_statuses')
                ->restrictOnDelete()
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * The foreign key has to go first, because MySQL cannot hold a foreign key
     * on a column that is being converted back to an ENUM.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['employment_status']);
        });

        // Any state added since this migration ran has no ENUM equivalent, so
        // such rows cannot be represented by the old column type.
        $unknown = DB::table('staff')
            ->whereNotIn('employment_status', ['active', 'probation', 'suspended', 'retired', 'terminated'])
            ->update(['employment_status' => 'active']);

        if ($unknown > 0) {
            logger()->warning('employment_statuses rollback: '.$unknown.' staff row(s) used a state the ENUM does not define and were set back to "active".');
        }

        Schema::table('staff', function (Blueprint $table) {
            $table->enum('employment_status', ['active', 'probation', 'suspended', 'retired', 'terminated'])
                ->default('active')
                ->change();
        });

        Schema::dropIfExists('employment_statuses');
    }
};
