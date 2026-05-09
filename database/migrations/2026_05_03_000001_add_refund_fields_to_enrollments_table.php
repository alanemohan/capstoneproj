<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('refund_status', ['none', 'requested', 'partial', 'full', 'rejected'])
                ->default('none')
                ->after('transaction_id');
            $table->decimal('refund_amount', 8, 2)->default(0)->after('refund_status');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_requested_at');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'refund_amount',
                'refund_requested_at',
                'refunded_at',
                'refund_reason',
            ]);
        });
    }
};