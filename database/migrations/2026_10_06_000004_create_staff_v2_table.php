<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_v2', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            // The office reference points at the canonical `offices` table, which
            // the Office module already manages. Positions point at positions_v2,
            // the rebuilt Position module.
            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions_v2')
                ->nullOnDelete();

            $table->string('staff_id', 50)->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('telegram', 30)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('system_role', ['admin', 'staff', 'user'])->default('staff');
            $table->timestamps();

            $table->index('full_name');
            $table->index('gender');
            $table->index('status');
            $table->index('system_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_v2');
    }
};
