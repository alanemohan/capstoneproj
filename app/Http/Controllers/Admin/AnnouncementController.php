<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(15);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_class' => 'nullable|array',
            'target_class.*' => 'string'
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_class' => $request->target_class ? json_encode($request->target_class) : null,
            'teacher_id' => auth()->id(),
        ]);

        return back()->with('success', __('messages.announcement_added_success'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_class' => 'nullable|array',
            'target_class.*' => 'string'
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'target_class' => $request->target_class ? json_encode($request->target_class) : null,
        ]);

        return back()->with('success', __('messages.scholarship_updated_success')); // Reusing updated_success key
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', __('messages.scholarship_deleted_success')); // Reusing deleted_success key
    }
}
