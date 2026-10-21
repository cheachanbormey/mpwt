<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
public function up(): void
    {
          if (Schema::hasTable('offices')) {
        return;
        }
        Schema::create('offices', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('dep_code')->nullable();
                // ->constrained('departments');
                // ->cascadeOnDelete();

            $table->string('office_code', 50)->unique();
            $table->string('name_kh');
            $table->string('name_en')->nullable();
            $table->foreignId('location_id')
                ->constrained('locations')
                ->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
