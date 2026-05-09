<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MentorManagementController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')
            ->with('assignedMentor:id,name,email,phone,subject_specialization')
            ->orderBy('name')
            ->paginate(20);

        $mentors = User::where('role', 'teacher')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'approved');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'subject_specialization']);

        return view('admin.mentor-management.index', compact('students', 'mentors'));
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'mentor_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $student = User::where('role', 'student')->findOrFail($validated['student_id']);
        $mentor = User::where('role', 'teacher')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'approved');
            })
            ->findOrFail($validated['mentor_id']);

        $oldMentorId = $student->mentor_id;
        $student->update(['mentor_id' => $mentor->id]);

        \App\Services\AuditLogger::log('assign_mentor', $student, ['mentor_id' => $oldMentorId], ['mentor_id' => $mentor->id]);

        // Invalidate caches that may hold stale mentor/student data
        \Illuminate\Support\Facades\Cache::forget("student.profile.{$student->id}");
        \Illuminate\Support\Facades\Cache::forget("mentor.students.{$mentor->id}");
        \Illuminate\Support\Facades\Cache::forget('admin.dashboard.stats');

        // Optionally: notify mentor via email (if configured) — keep notifications succinct
        \Illuminate\Support\Facades\Mail::raw(
            "You have been assigned a new mentee: {$student->name} ({$student->email})",
            function ($message) use ($mentor) {
                $message->to($mentor->email)->subject('New mentee assigned');
            }
        );

        $student->notify(new \App\Notifications\MentorAssignedNotification($mentor));

        StudentNotification::create([
            'student_id' => $student->id,
            'title' => 'Mentor assigned',
            'message' => "{$mentor->name} has been assigned as your mentor. You can now contact your mentor from your profile/dashboard.",
        ]);

        if (!empty(config('mail.to.address'))) {
            Mail::raw(
                "Mentor allocation update:\nStudent: {$student->name} ({$student->email})\nMentor: {$mentor->name} ({$mentor->email})",
                function ($message) {
                    $message->subject('Mentor allocation update');
                }
            );
        }

        return back()->with('success', 'Mentor assigned successfully. Student notification has been generated.');
    }
}
