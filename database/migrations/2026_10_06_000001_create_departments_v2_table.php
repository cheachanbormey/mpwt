<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments_v2', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->string('dep_code', 50)->unique();
            $table->string('dep_kh_name');
            $table->string('dep_en_name')->nullable();

            // The department head. Nullable so a department can exist before
            // anyone is appointed, and nullOnDelete so removing the staff row
            // leaves the department in place rather than deleting it.
            $table->foreignId('director_dep')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index('status');
            $table->index('dep_kh_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments_v2');
    }
};
