<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('student_id', Auth::id())->latest()->get();
        return view('student.complaints.index', compact('complaints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        $complaint = Complaint::create([
            'student_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        // Notify all admin users of the new complaint
        $student = Auth::user();
        foreach (\App\Models\User::where('role', 'admin')->get() as $admin) {
            $admin->notify(new \App\Notifications\ComplaintNotification($student->name, $complaint->subject));
        }

        return back()->with('success', __('messages.complaint_submitted_success'));
    }
}
