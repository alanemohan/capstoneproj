<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('language', 10)->default('en')->after('class_level');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('language', 10)->default('en')->after('class_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('language');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
