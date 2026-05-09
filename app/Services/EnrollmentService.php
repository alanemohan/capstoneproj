<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EnrollmentService
{
    public function isEnrolled(User $user, Course $course): bool
    {
        return $course->enrollments()
            ->where('user_id', $user->id)
            ->whereIn('payment_status', ['free', 'paid'])
            ->exists();
    }

    public function getEnrollment(User $user, Course $course): ?Enrollment
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
    }

    public function enrollFree(User $user, Course $course): Enrollment
    {
        return Enrollment::create([
            'user_id'        => $user->id,
            'course_id'      => $course->id,
            'enrolled_at'    => now(),
            'payment_status' => 'free',
            'amount_paid'    => 0,
        ]);
    }

    /**
     * Start a paid enrollment process (pending).
     */
    public function enrollPending(User $user, Course $course): Enrollment
    {
        return Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            [
                'enrolled_at'    => now(),
                'payment_status' => 'pending',
                'amount_paid'    => $course->price,
                'transaction_id' => 'PEND-' . strtoupper(Str::random(12)),
            ]
        );
    }

    public function confirmPayment(Enrollment $enrollment, string $transactionId): bool
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($enrollment, $transactionId) {
            // Prevent double-processing: if a payment with the same transaction_id exists, skip.
            $exists = \App\Models\Payment::where('transaction_id', $transactionId)->exists();
            if ($exists) {
                return false;
            }

            $updated = $enrollment->update([
                'payment_status' => 'paid',
                'transaction_id' => $transactionId,
                'enrolled_at'    => now(),
            ]);

            if (! $updated) {
                return false;
            }

            // Create a payment ledger record
            \App\Models\Payment::create([
                'enrollment_id' => $enrollment->id,
                'course_id'     => $enrollment->course_id,
                'user_id'       => $enrollment->user_id,
                'amount'        => $enrollment->amount_paid,
                'transaction_id'=> $transactionId,
                'gateway'       => 'mock',
                'meta'          => null,
            ]);

            $enrollment->user->notify(new \App\Notifications\PaymentStatusNotification(
                $enrollment->course->title,
                'paid',
                $enrollment->amount_paid
            ));

            return true;
        });
    }

    public function failPayment(Enrollment $enrollment, string $reason = ''): bool
    {
        $updated = $enrollment->update([
            'payment_status' => 'failed',
            'transaction_id' => $enrollment->transaction_id . '-FAIL',
        ]);

        if ($updated) {
            $enrollment->user->notify(new \App\Notifications\PaymentStatusNotification(
                $enrollment->course->title,
                'failed',
                $enrollment->amount_paid
            ));
        }

        return $updated;
    }

    public function refundWindowDays(): int
    {
        return max(0, (int) config('lms.refund_window_days', 14));
    }

    public function canRequestRefund(Enrollment $enrollment, ?Carbon $now = null): bool
    {
        $now ??= now();

        if ($enrollment->payment_status !== 'paid' || !$enrollment->enrolled_at) {
            return false;
        }

        return $enrollment->enrolled_at->greaterThanOrEqualTo($now->copy()->subDays($this->refundWindowDays()));
    }

    public function requestRefund(Enrollment $enrollment, ?string $reason = null, ?float $amount = null): Enrollment
    {
        $eligible = $this->canRequestRefund($enrollment);
        $refundAmount = $amount ?? (float) $enrollment->amount_paid;
        $refundAmount = max(0, min((float) $enrollment->amount_paid, round($refundAmount, 2)));

        $status = ! $eligible
            ? 'rejected'
            : 'requested';

        $enrollment->update([
            'refund_status' => $status,
            'refund_amount' => $status === 'rejected' ? 0 : $refundAmount,
            'refund_requested_at' => now(),
            'refunded_at' => null,
            'refund_reason' => $reason,
        ]);

        return $enrollment->refresh();
    }
}
