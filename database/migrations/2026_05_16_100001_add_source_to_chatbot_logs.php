<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->string('source')->nullable()->after('confidence')->comment('AI provider that answered');
            $table->string('conversation_id')->nullable()->after('session_id')->comment('Groups messages into conversations');
            $table->index('conversation_id');
        });
    }

    public function down(): void
    {
        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->dropIndex(['conversation_id']);
            $table->dropColumn(['source', 'conversation_id']);
        });
    }
};
