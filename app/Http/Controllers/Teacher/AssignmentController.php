<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Batch;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = auth()->user()->givenAssignments()->with('batch')->latest()->paginate(15);
        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $batches = auth()->user()->taughtBatches;
        return view('teacher.assignments.create', compact('batches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'max_marks' => 'required|numeric|min:1',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->teacher_id !== auth()->id()) abort(403);

        $validated['teacher_id'] = auth()->id();
        Assignment::create($validated);

        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $assignment->load(['batch.students', 'submissions.student']);
        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $batches = auth()->user()->taughtBatches;
        return view('teacher.assignments.edit', compact('assignment', 'batches'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date',
            'max_marks' => 'required|numeric|min:1',
        ]);

        $batch = Batch::findOrFail($validated['batch_id']);
        if ($batch->teacher_id !== auth()->id()) abort(403);

        $assignment->update($validated);

        return redirect()->route('teacher.assignments.show', $assignment)->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        $assignment->delete();
        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment deleted successfully.');
    }

    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeAssignment($assignment);

        if ($submission->assignment_id !== $assignment->id) abort(404);

        $validated = $request->validate([
            'marks' => 'required|numeric|min:0|max:' . $assignment->max_marks,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'marks' => $validated['marks'],
            'feedback' => $validated['feedback'],
            'status' => 'graded'
        ]);

        return back()->with('success', 'Assignment graded successfully.');
    }

    private function authorizeAssignment(Assignment $assignment)
    {
        if ($assignment->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access to assignment.');
        }
    }
}
