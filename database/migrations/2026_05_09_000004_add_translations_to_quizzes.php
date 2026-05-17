<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->string('title_pa')->nullable()->after('title_hi');
            $table->text('description_hi')->nullable()->after('description');
            $table->text('description_pa')->nullable()->after('description_hi');
            $table->boolean('translation_pending')->default(false)->after('description_pa');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'title_pa', 'description_hi', 'description_pa', 'translation_pending']);
        });
    }
};
