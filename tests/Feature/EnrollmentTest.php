<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_enroll_in_free_course()
    {
        $student = \App\Models\User::factory()->create(['role' => 'student']);
        $course = \App\Models\Course::factory()->create(['price' => 0, 'status' => 'published']);
        
        $response = $this->actingAs($student)->post(route('student.courses.enroll', $course));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'payment_status' => 'free'
        ]);
    }

    public function test_student_enrollment_starts_as_pending_for_paid_course()
    {
        $student = \App\Models\User::factory()->create(['role' => 'student']);
        $course = \App\Models\Course::factory()->create(['price' => 100, 'status' => 'published']);
        
        $response = $this->actingAs($student)->post(route('student.courses.purchase', $course), [
            'payment_method' => 'upi',
            'upi_id' => 'test@upi',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'payment_status' => 'pending'
        ]);
    }

    public function test_payment_callback_confirms_enrollment()
    {
        $student = \App\Models\User::factory()->create(['role' => 'student']);
        $course = \App\Models\Course::factory()->create(['price' => 100]);
        $enrollment = \App\Models\Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'payment_status' => 'pending',
            'amount_paid' => 100,
            'transaction_id' => 'PEND-123'
        ]);

        $response = $this->actingAs($student)->post(route('student.payment.callback'), [
            'enrollments' => [$enrollment->id],
            'status' => 'success',
            'transaction_id' => 'TXN-999'
        ]);

        $response->assertRedirect(route('student.my-courses'));
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'payment_status' => 'paid',
        ]);
    }
}
