<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_participants', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('meeting_id')
                ->constrained('meetings')
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->enum('attendance_status', [
                'invited',
                'accepted',
                'declined',
                'attended',
                'absent',
            ])->default('invited');
            $table->dateTime('attended_at')->nullable();
            $table->string('notes')->nullable();

            $table->timestamps();

            // A member of staff can only be registered once per meeting
            $table->unique(['meeting_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
    }
};
