<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();

            $table->foreignId('leader_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
