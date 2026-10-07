<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_agendas', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('meeting_id')
                ->constrained('meetings')
                ->cascadeOnDelete();

            $table->foreignId('presenter_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->unsignedSmallInteger('agenda_no');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            // Agenda numbers are unique inside one meeting
            $table->unique(['meeting_id', 'agenda_no']);
            $table->index(['meeting_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_agendas');
    }
};
