<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $batchIds = auth()->user()->batches()->pluck('batches.id');
        $assignments = Assignment::whereIn('batch_id', $batchIds)
            ->with(['batch', 'submissions' => function($query) {
                $query->where('student_id', auth()->id());
            }])
            ->latest()
            ->paginate(15);

        return view('student.assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment)
    {
        if (!$this->isStudentInAssignmentBatch($assignment)) {
            abort(403);
        }

        $submission = $assignment->submissions()->where('student_id', auth()->id())->first();

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        if (!$this->isStudentInAssignmentBatch($assignment)) {
            abort(403);
        }

        if ($assignment->due_date->isPast()) {
            return back()->with('error', 'The due date for this assignment has passed.');
        }

        $request->validate([
            'submission_file' => 'required|file|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $path = $request->file('submission_file')->store('assignments/submissions', 'public');

        AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'student_id' => auth()->id()],
            [
                'file_path' => $path,
                'status' => 'submitted'
            ]
        );

        // Update student streak for engagement
        auth()->user()->updateStreak();

        return back()->with('success', 'Assignment submitted successfully.');
    }

    private function isStudentInAssignmentBatch(Assignment $assignment): bool
    {
        return auth()->user()->batches()->where('batches.id', $assignment->batch_id)->exists();
    }
}
