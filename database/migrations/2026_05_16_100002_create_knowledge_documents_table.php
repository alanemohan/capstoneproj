<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('knowledge_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('general')->comment('math, science, english, lms, general');
            $table->text('content')->comment('The actual knowledge text');
            $table->text('keywords')->nullable()->comment('Comma-separated keywords for search');
            $table->json('tfidf_vector')->nullable()->comment('Pre-computed TF-IDF vector for fast similarity search');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_documents');
    }
};
