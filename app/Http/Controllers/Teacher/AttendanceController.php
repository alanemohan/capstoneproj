<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, Batch $batch)
    {
        if ($batch->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access to batch.');
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,late',
        ]);

        foreach ($validated['attendance'] as $studentId => $status) {
            Attendance::updateOrCreate(
                ['batch_id' => $batch->id, 'student_id' => $studentId, 'date' => $validated['date']],
                ['status' => $status]
            );
        }

        return back()->with('success', 'Attendance marked successfully for ' . $validated['date']);
    }
}
