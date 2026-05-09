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
        Schema::table('government_schemes', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->string('title_pa')->nullable()->after('title_hi');
            $table->text('description_hi')->nullable()->after('description');
            $table->text('description_pa')->nullable()->after('description_hi');
            $table->string('target_audience_hi')->nullable()->after('target_audience');
            $table->string('target_audience_pa')->nullable()->after('target_audience_hi');
            $table->text('benefits_hi')->nullable()->after('benefits');
            $table->text('benefits_pa')->nullable()->after('benefits_hi');
        });

        Schema::table('scholarships', function (Blueprint $table) {
            $table->string('title_hi')->nullable()->after('title');
            $table->string('title_pa')->nullable()->after('title_hi');
            $table->text('description_hi')->nullable()->after('description');
            $table->text('description_pa')->nullable()->after('description_hi');
            $table->text('eligibility_criteria_hi')->nullable()->after('eligibility_criteria');
            $table->text('eligibility_criteria_pa')->nullable()->after('eligibility_criteria_hi');
            $table->string('amount_hi')->nullable()->after('amount');
            $table->string('amount_pa')->nullable()->after('amount_hi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('government_schemes', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'title_pa', 'description_hi', 'description_pa', 'target_audience_hi', 'target_audience_pa', 'benefits_hi', 'benefits_pa']);
        });

        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn(['title_hi', 'title_pa', 'description_hi', 'description_pa', 'eligibility_criteria_hi', 'eligibility_criteria_pa', 'amount_hi', 'amount_pa']);
        });
    }
};
