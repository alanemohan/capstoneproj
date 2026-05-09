<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Notifications\RefundStatusNotification;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['user', 'course'])
            ->where('payment_status', '!=', 'free');

        if ($status = $request->status) {
            $query->where('payment_status', $status);
        }

        if ($refundStatus = $request->refund_status) {
            $query->where('refund_status', $refundStatus);
        }

        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('course', fn($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        $enrollments = $query->latest('enrolled_at')->paginate(20);
        
        $stats = [
            'total_revenue' => Enrollment::where('payment_status', 'paid')->sum('amount_paid'),
            'pending_count' => Enrollment::where('payment_status', 'pending')->count(),
            'paid_count'    => Enrollment::where('payment_status', 'paid')->count(),
            'failed_count'  => Enrollment::where('payment_status', 'failed')->count(),
            'refund_requested_count' => Enrollment::where('refund_status', 'requested')->count(),
            'refund_completed_count'  => Enrollment::whereIn('refund_status', ['partial', 'full'])->count(),
        ];

        return view('admin.reconciliation.index', compact('enrollments', 'stats'));
    }

    public function approveRefund(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->refund_status === 'requested', 404);

        $validated = $request->validate([
            'refund_amount' => ['nullable', 'numeric', 'min:0.01', 'max:' . (float) $enrollment->amount_paid],
        ]);

        $refundAmount = $validated['refund_amount'] ?? (float) $enrollment->refund_amount;
        if ($refundAmount <= 0) {
            $refundAmount = (float) $enrollment->amount_paid;
        }

        $refundAmount = min((float) $enrollment->amount_paid, round($refundAmount, 2));
        $refundStatus = $refundAmount >= (float) $enrollment->amount_paid ? 'full' : 'partial';

        $oldValues = [
            'refund_status' => $enrollment->refund_status,
            'refund_amount' => $enrollment->refund_amount,
            'refunded_at' => $enrollment->refunded_at,
        ];

        $enrollment->update([
            'refund_status' => $refundStatus,
            'refund_amount' => $refundAmount,
            'refunded_at' => now(),
        ]);

        AuditLogger::log(
            'approve_refund',
            $enrollment,
            $oldValues,
            [
                'refund_status' => $refundStatus,
                'refund_amount' => $refundAmount,
                'refunded_at' => now()->toDateTimeString(),
            ]
        );

        $enrollment->user->notify(new RefundStatusNotification(
            $enrollment->course->title,
            $refundStatus,
            $refundAmount,
            $enrollment->refund_reason
        ));

        return back()->with('success', 'Refund approved and marked as ' . $refundStatus . '.');
    }

    public function rejectRefund(Request $request, Enrollment $enrollment)
    {
        abort_unless($enrollment->refund_status === 'requested', 404);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldValues = [
            'refund_status' => $enrollment->refund_status,
            'refund_amount' => $enrollment->refund_amount,
            'refunded_at' => $enrollment->refunded_at,
        ];

        $enrollment->update([
            'refund_status' => 'rejected',
            'refunded_at' => null,
        ]);

        AuditLogger::log(
            'reject_refund',
            $enrollment,
            $oldValues,
            [
                'refund_status' => 'rejected',
                'admin_notes' => $validated['notes'] ?? null,
            ]
        );

        $enrollment->user->notify(new RefundStatusNotification(
            $enrollment->course->title,
            'rejected',
            (float) $enrollment->refund_amount,
            $validated['notes'] ?? $enrollment->refund_reason
        ));

        return back()->with('success', 'Refund request rejected.');
    }
}
