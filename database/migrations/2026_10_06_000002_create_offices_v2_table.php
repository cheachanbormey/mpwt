<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices_v2', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            // A department owns its offices, so removing one removes the rest.
            $table->foreignId('dep_id')
                ->constrained('departments_v2')
                ->cascadeOnDelete();

            $table->string('office_code', 50)->unique();
            $table->string('office_name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('office_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices_v2');
    }
};
