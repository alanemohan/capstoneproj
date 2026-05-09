<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'teacher')->withCount(['courses', 'lessons', 'quizzes']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $teachers = $query->latest()->paginate(15);
        return view('admin.teachers_manager.index', compact('teachers'));
    }

    public function show(User $teacher)
    {
        if ($teacher->role !== 'teacher') abort(404);

        $teacher->load(['courses', 'lessons', 'quizzes']);
        
        $stats = [
            'total_courses' => $teacher->courses->count(),
            'total_lessons' => $teacher->lessons->count(),
            'total_quizzes' => $teacher->quizzes->count(),
            'students_reached' => \App\Models\Enrollment::whereIn('course_id', $teacher->courses->pluck('id'))->count(),
        ];

        return view('admin.teachers_manager.show', compact('teacher', 'stats'));
    }

    public function toggleActive(User $teacher)
    {
        if ($teacher->role !== 'teacher') abort(404);
        
        $oldActive = $teacher->is_active;
        $teacher->update(['is_active' => !$teacher->is_active]);
        
        \App\Services\AuditLogger::log('toggle_teacher_active', $teacher, ['is_active' => $oldActive], ['is_active' => $teacher->is_active]);
        
        $status = $teacher->is_active ? 'activated' : 'suspended';
        return back()->with('success', "Teacher account has been {$status}.");
    }

    public function destroy(User $teacher)
    {
        if ($teacher->role !== 'teacher') abort(404);
        
        \App\Services\AuditLogger::log('delete_teacher', $teacher, $teacher->toArray(), null);
        $teacher->delete();
        return redirect()->route('admin.teachers_manager.index')->with('success', 'Teacher deleted successfully.');
    }
}
