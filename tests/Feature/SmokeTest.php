<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_redirects_to_login_for_guests()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_student_can_reach_dashboard()
    {
        $student = \App\Models\User::factory()->create(['role' => 'student']);
        $response = $this->actingAs($student)->get('/student/dashboard');
        $response->assertStatus(200);
    }

    public function test_teacher_can_reach_dashboard()
    {
        $teacher = \App\Models\User::factory()->create(['role' => 'teacher', 'status' => 'approved']);
        $response = $this->actingAs($teacher)->get('/teacher/dashboard');
        $response->assertStatus(200);
    }

    public function test_admin_can_reach_dashboard()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
