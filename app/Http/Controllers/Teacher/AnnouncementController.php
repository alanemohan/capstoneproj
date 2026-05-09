<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('teacher_id', Auth::id())->latest()->get();
        return view('teacher.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $courses = Course::where('teacher_id', Auth::id())->where('status', 'published')->get();
        return view('teacher.announcements.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        Announcement::create([
            'teacher_id' => Auth::id(),
            'course_id' => $request->course_id,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('teacher.announcements.index')->with('success', 'Announcement posted successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        if ($announcement->teacher_id !== Auth::id()) abort(403);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
