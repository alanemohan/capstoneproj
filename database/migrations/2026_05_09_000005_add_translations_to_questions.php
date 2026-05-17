<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->text('question_text_hi')->nullable()->after('question_text');
            $table->text('question_text_pa')->nullable()->after('question_text_hi');
            $table->text('explanation_hi')->nullable()->after('explanation');
            $table->text('explanation_pa')->nullable()->after('explanation_hi');
            $table->json('options_hi')->nullable()->after('options');
            $table->json('options_pa')->nullable()->after('options_hi');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['question_text_hi', 'question_text_pa', 'explanation_hi', 'explanation_pa', 'options_hi', 'options_pa']);
        });
    }
};
