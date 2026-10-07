<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('meeting_type_id')
                ->constrained('meeting_types')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->foreignId('created_by_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->string('title');
            $table->text('agenda_summary')->nullable();
            $table->date('meeting_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', [
                'scheduled',
                'ongoing',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->timestamps();

            $table->index('meeting_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
