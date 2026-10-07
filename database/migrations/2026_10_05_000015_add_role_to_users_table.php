<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Phase 03 - authentication and system roles.
     *
     * Adds the role that drives every protected route in the HRMS.
     * Two roles exist:
     *   admin - full access to every management module
     *   staff - self-service only (own profile, attendance, leave, meetings)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)
                ->default('staff')
                ->after('password')
                ->comment('admin | staff');

            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
