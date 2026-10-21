<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->foreign('dep_code')->references('id')->on('departments')->onDelete('cascade');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->foreign('office_code')->references('id')->on('offices')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['dep_code']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['staff_id']);
        });

        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['office_code']);
        });
    }
};