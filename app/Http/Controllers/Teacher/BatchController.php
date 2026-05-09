<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function index()
    {
        $batches = auth()->user()->taughtBatches()->withCount('students')->latest()->paginate(15);
        return view('teacher.batches.index', compact('batches'));
    }

    public function create()
    {
        return view('teacher.batches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_level' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->taughtBatches()->create($validated);

        return redirect()->route('teacher.batches.index')->with('success', 'Batch created successfully.');
    }

    public function show(Batch $batch)
    {
        $this->authorizeBatch($batch);
        $batch->load(['students', 'assignments', 'attendances' => function($query) {
            $query->where('date', now()->toDateString());
        }]);

        // Get students not in batch to add them
        $availableStudents = User::where('role', 'student')
                                 ->whereNotIn('id', $batch->students->pluck('id'))
                                 ->get();

        return view('teacher.batches.show', compact('batch', 'availableStudents'));
    }

    public function edit(Batch $batch)
    {
        $this->authorizeBatch($batch);
        return view('teacher.batches.edit', compact('batch'));
    }

    public function update(Request $request, Batch $batch)
    {
        $this->authorizeBatch($batch);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'class_level' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->route('teacher.batches.show', $batch)->with('success', 'Batch updated successfully.');
    }

    public function destroy(Batch $batch)
    {
        $this->authorizeBatch($batch);
        $batch->delete();
        return redirect()->route('teacher.batches.index')->with('success', 'Batch deleted successfully.');
    }

    public function addStudent(Request $request, Batch $batch)
    {
        $this->authorizeBatch($batch);
        
        $request->validate(['student_id' => 'required|exists:users,id']);
        
        if (!$batch->students()->where('student_id', $request->student_id)->exists()) {
            $batch->students()->attach($request->student_id);
            return back()->with('success', 'Student added to batch.');
        }

        return back()->with('error', 'Student is already in the batch.');
    }

    public function removeStudent(Batch $batch, User $student)
    {
        $this->authorizeBatch($batch);
        $batch->students()->detach($student->id);
        return back()->with('success', 'Student removed from batch.');
    }

    private function authorizeBatch(Batch $batch)
    {
        if ($batch->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access to batch.');
        }
    }
}
