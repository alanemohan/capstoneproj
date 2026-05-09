<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentManagerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student')->withCount(['enrollments', 'quizAttempts', 'progressReports']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->latest()->paginate(15);
        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        if ($student->role !== 'student') abort(404);

        $student->load(['enrollments.course', 'quizAttempts.quiz', 'progressReports.lesson']);
        
        $stats = [
            'total_enrollments' => $student->enrollments->count(),
            'quizzes_completed' => $student->quizAttempts->where('status', 'completed')->count(),
            'lessons_completed' => $student->progressReports->where('is_completed', true)->count(),
            'average_score' => $student->total_quiz_score,
            'streak' => $student->streak_count,
        ];

        return view('admin.students.show', compact('student', 'stats'));
    }

    public function toggleActive(User $student)
    {
        if ($student->role !== 'student') abort(404);
        
        $oldActive = $student->is_active;
        $student->update(['is_active' => !$student->is_active]);
        
        \App\Services\AuditLogger::log('toggle_student_active', $student, ['is_active' => $oldActive], ['is_active' => $student->is_active]);
        
        $status = $student->is_active ? 'activated' : 'suspended';
        return back()->with('success', "Student account has been {$status}.");
    }

    public function destroy(User $student)
    {
        if ($student->role !== 'student') abort(404);
        
        \App\Services\AuditLogger::log('delete_student', $student, $student->toArray(), null);
        $student->delete();
        return redirect()->route('admin.students_manager.index')->with('success', 'Student deleted successfully.');
    }

    public function export()
    {
        $students = User::where('role', 'student')->withCount('enrollments')->with('quizAttempts')->get();
        
        $csvData = "Name,Email,Class,School,Enrollments,Score,Status\n";
        foreach ($students as $student) {
            $enrollments = $student->enrollments_count;
            $score = $student->total_quiz_score;
            $status = $student->is_active ? 'Active' : 'Suspended';
            
            $csvData .= "{$student->name},{$student->email},{$student->class_level},{$student->school},{$enrollments},{$score},{$status}\n";
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="students_export.csv"');
    }
}
