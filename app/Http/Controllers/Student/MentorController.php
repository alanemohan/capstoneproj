<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class MentorController extends Controller
{
    public function index()
    {
        $student = auth()->user();
        $mentors = $student->assignedMentor ? collect([$student->assignedMentor]) : collect();
        return view('student.mentors.index', compact('mentors'));
    }

    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $student = auth()->user();
        $recipient = 'alanemohan@gmail.com';

        try {
            \Illuminate\Support\Facades\Mail::raw(
                "You received a message from a student via Nabha Digital Learning LMS.\n\n" .
                "--- STUDENT DETAILS ---\n" .
                "Name: {$student->name}\n" .
                "Email: {$student->email}\n" .
                "Class Level: " . ($student->class_level ?? 'N/A') . "\n" .
                "School: " . ($student->school ?? 'N/A') . "\n\n" .
                "--- MESSAGE SUBJECT ---\n" .
                "{$validated['subject']}\n\n" .
                "--- MESSAGE CONTENT ---\n" .
                "{$validated['message']}",
                function ($message) use ($validated, $recipient, $student) {
                    $message->to($recipient)
                        ->replyTo($student->email)
                        ->subject("[LMS Mentor Message] " . $validated['subject']);
                }
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent to mentor successfully!'
                ]);
            }

            return back()->with('success', 'Your message has been sent to the mentor successfully.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Unable to send email: ' . $e->getMessage());
        }
    }
}
