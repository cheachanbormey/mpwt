<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            // One login account per member of staff (nullable until Phase 03 auth)
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('office_code')
                ->nullable();
                // ->constrained('offices');
                // ->nullOnDelete();

            $table->foreignId('position_id')
                ->nullable()
                ->constrained('positions')
                ->nullOnDelete();

            $table->string('staff_id', 50)->unique();
            $table->string('name_km');
            $table->string('name_en')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('telegram', 30)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('system_role', ['admin', 'staff', 'user'])->default('staff');

            $table->timestamps();
            $table->softDeletes();
            
            $table->index('gender');
            $table->index('status');
            $table->index('system_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
