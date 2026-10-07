<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->foreignId('leave_type_id')
                ->constrained('leave_types')
                ->restrictOnDelete();

            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 5, 2)->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->foreignId('approved_by_staff_id')
                ->nullable()
                ->constrained('staff')
                ->nullOnDelete();

            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index(['staff_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
