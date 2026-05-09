<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }
};
