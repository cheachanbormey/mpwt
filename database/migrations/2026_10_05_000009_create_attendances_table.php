<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->date('work_date');
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('worked_hours', 5, 2)->nullable();
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'leave'
            ])->default('present');
            $table->enum('check_in_source', ['manual', 'qr', 'system'])->default('manual');

            $table->timestamps();

            // A member of staff can only have one record per day
            $table->unique(['staff_id', 'work_date']);
            $table->index('work_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
