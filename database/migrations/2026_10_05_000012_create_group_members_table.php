<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_members', function (Blueprint $table) {
            // MyISAM (the WAMP server default) silently drops foreign keys
            $table->engine = 'InnoDB';

            $table->id();

            $table->foreignId('group_id')
                ->constrained('groups')
                ->cascadeOnDelete();

            $table->foreignId('staff_id')
                ->constrained('staff')
                ->cascadeOnDelete();

            $table->string('role_in_group', 50)->nullable();
            $table->boolean('is_leader')->default(false);
            $table->date('joined_at')->nullable();

            $table->timestamps();

            // A member cannot be added to the same group twice
            $table->unique(['group_id', 'staff_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
