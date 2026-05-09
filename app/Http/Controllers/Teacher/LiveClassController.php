<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LiveClass;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class LiveClassController extends Controller
{
    public function index()
    {
        $classes = LiveClass::where('teacher_id', Auth::id())->orderBy('scheduled_at', 'desc')->get();
        return view('teacher.live-classes.index', compact('classes'));
    }

    public function create()
    {
        $courses = Course::where('teacher_id', Auth::id())->where('status', 'published')->get();
        return view('teacher.live-classes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'meeting_link' => 'required|url|max:500',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'description' => 'nullable|string',
        ]);

        LiveClass::create([
            'teacher_id' => Auth::id(),
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'meeting_link' => $request->meeting_link,
            'scheduled_at' => $request->scheduled_at,
            'duration_minutes' => $request->duration_minutes,
            'status' => 'scheduled'
        ]);

        return redirect()->route('teacher.live-classes.index')->with('success', 'Live Class scheduled successfully.');
    }

    public function edit(LiveClass $liveClass)
    {
        if ($liveClass->teacher_id !== Auth::id()) abort(403);
        $courses = Course::where('teacher_id', Auth::id())->where('status', 'published')->get();
        return view('teacher.live-classes.edit', compact('liveClass', 'courses'));
    }

    public function update(Request $request, LiveClass $liveClass)
    {
        if ($liveClass->teacher_id !== Auth::id()) abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'course_id' => 'nullable|exists:courses,id',
            'meeting_link' => 'required|url|max:500',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'status' => 'required|in:scheduled,live,completed,cancelled',
            'description' => 'nullable|string',
        ]);

        $liveClass->update($request->all());

        return redirect()->route('teacher.live-classes.index')->with('success', 'Live Class updated successfully.');
    }

    public function destroy(LiveClass $liveClass)
    {
        if ($liveClass->teacher_id !== Auth::id()) abort(403);
        $liveClass->delete();
        return back()->with('success', 'Live Class deleted.');
    }
}
