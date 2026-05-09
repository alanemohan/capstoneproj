<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_access_admin_dashboard()
    {
        $student = \App\Models\User::factory()->create(['role' => 'student']);
        
        $response = $this->actingAs($student)->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_admin_dashboard()
    {
        $teacher = \App\Models\User::factory()->create(['role' => 'teacher']);
        
        $response = $this->actingAs($teacher)->get('/admin/dashboard');
        
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_student_dashboard()
    {
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }
}
