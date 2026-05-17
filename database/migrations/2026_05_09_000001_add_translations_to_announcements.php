<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->string('title_pa')->nullable()->after('title_hi');
            $table->text('content_hi')->nullable()->after('content');
            $table->text('content_pa')->nullable()->after('content_hi');
            $table->boolean('translation_pending')->default(false)->after('content_pa');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'title_pa', 'content_hi', 'content_pa', 'translation_pending']);
        });
    }
};
