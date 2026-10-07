<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Moves attendance onto the rebuilt staff module.
 *
 * The `attendances` table is kept as-is (its columns already match what the
 * Attendance module needs) but `staff_id` now points at `staff_v2` instead
 * of `staff`, so the people created through the rebuilt Staff module can
 * check in.
 *
 * To do that without losing history the existing positions and staff rows are
 * copied into the v2 tables first, then every attendance row is repointed at
 * the copy of its own person.
 */
return new class extends Migration
{
    /**
     * Every column of the attendances table, in the order used when copying.
     *
     * @var list<string>
     */
    private const COLUMNS = [
        'id', 'staff_id', 'work_date', 'check_in', 'check_out',
        'worked_hours', 'status', 'check_in_source', 'notes',
        'created_at', 'updated_at',
    ];

    public function up(): void
    {
        $positionMap = $this->copyPositions();
        $staffMap = $this->copyStaff($positionMap);

        if ($this->isSqlite()) {
            // SQLite cannot take a constraint off a table that already exists,
            // so the table is rebuilt with the new target instead.
            $this->rebuildTable($staffMap);

            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            // Dropped by name because Laravel guessed the constraint name from
            // the column, which no longer matches once the target changes.
            $table->dropForeign('attendances_staff_id_foreign');

            // Temporarily released so repointing cannot trip the one-row-per-day
            // rule half way through.
            $table->dropUnique(['staff_id', 'work_date']);
        });

        $this->repointAttendances($staffMap);

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff_v2')
                ->cascadeOnDelete();

            // A member of staff can only have one record per day
            $table->unique(['staff_id', 'work_date']);
        });
    }

    public function down(): void
    {
        // Attendance rows now hold staff_v2 keys, which have no meaning in the
        // original `staff` table, so the records are dropped rather than mapped
        // onto the wrong person.
        DB::table('attendances')->delete();

        if ($this->isSqlite()) {
            $this->rebuildTable([]);

            return;
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
            $table->dropUnique(['staff_id', 'work_date']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->cascadeOnDelete();

            $table->unique(['staff_id', 'work_date']);
        });
    }

    /**
     * True when running on SQLite, which handles constraints differently.
     */
    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }

    /**
     * Recreate the attendances table so its key points at a different table.
     *
     * This is the only way to change a constraint on SQLite. The rows are read
     * out first and written back with their staff key swapped, so no history is
     * lost.
     *
     * @param  array<int, int>  $staffMap
     */
    private function rebuildTable(array $staffMap): void
    {
        $legacy = 'attendances_before_repoint';

        Schema::rename('attendances', $legacy);

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('staff_id')
                ->constrained('staff_v2')
                ->cascadeOnDelete();

            $table->date('work_date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('worked_hours', 5, 2)->nullable();
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'leave',
                'holiday',
                'weekend',
            ])->default('present');
            $table->enum('check_in_source', ['manual', 'qr', 'system'])->default('manual');
            $table->string('notes')->nullable();

            $table->timestamps();

            /*
             * Renaming a table in SQLite keeps its indexes and their names, so
             * the stock names would already be taken by the table just renamed
             * out of the way. Distinct names are used here only because this
             * branch never runs on the MySQL database.
             */
            $table->unique(['staff_id', 'work_date'], 'att_staff_day_unique');
            $table->index('work_date', 'att_work_date_index');
            $table->index('status', 'att_status_index');
        });

        if ($staffMap !== []) {
            $quoted = implode(', ', array_map(fn ($c) => DB::getQueryGrammar()->wrap($c), self::COLUMNS));

            DB::statement(
                'INSERT INTO attendances ('.$quoted.') SELECT '.$quoted.' FROM '.$legacy
            );

            $this->repointAttendances($staffMap);
        }

        Schema::drop($legacy);
    }

    /**
     * Copy every position into positions_v2.
     *
     * @return array<int, int> old position id => new position id
     */
    private function copyPositions(): array
    {
        $map = [];

        foreach (DB::table('positions')->orderBy('id')->get() as $position) {
            $existing = DB::table('positions_v2')
                ->where('position_name_kh', $position->name)
                ->value('id');

            $map[$position->id] = $existing ?? DB::table('positions_v2')->insertGetId([
                'position_name_kh' => $position->name,
                'position_name_en' => $position->name_en,
                'description' => $position->description,
                'status' => $position->is_active ? 'active' : 'inactive',
                'created_at' => $position->created_at,
                'updated_at' => $position->updated_at,
            ]);
        }

        return $map;
    }

    /**
     * Copy every member of staff into staff_v2.
     *
     * @param  array<int, int>  $positionMap
     * @return array<int, int> old staff id => new staff id
     */
    private function copyStaff(array $positionMap): array
    {
        $map = [];

        foreach (DB::table('staff')->orderBy('id')->get() as $person) {
            // staff_code is unique in the old table, so the map stays one to one.
            $existing = DB::table('staff_v2')
                ->where('staff_id', $person->staff_code)
                ->value('id');

            if ($existing !== null) {
                $map[$person->id] = $existing;

                continue;
            }

            $map[$person->id] = DB::table('staff_v2')->insertGetId([
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

        return $map;
    }

    /**
     * Move every attendance row onto the copy of the same person.
     *
     * @param  array<int, int>  $staffMap
     */
    private function repointAttendances(array $staffMap): void
    {
        foreach ($staffMap as $oldId => $newId) {
            if ($oldId === $newId) {
                continue;
            }

            DB::table('attendances')->where('staff_id', $oldId)->update([
                'staff_id' => $newId,
            ]);
        }

        if ($staffMap === []) {
            // whereNotIn with an empty list matches every row, which would wipe
            // the table instead of leaving it alone.
            return;
        }

        // Anything left behind had no counterpart in staff_v2.
        DB::table('attendances')->whereNotIn('staff_id', array_values($staffMap))->delete();
    }
};
