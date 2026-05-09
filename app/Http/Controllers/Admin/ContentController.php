<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\ContentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with('teacher')->withCount(['progressReports', 'quizzes']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $lessons = $query->latest()->paginate(20);
        return view('admin.content.index', compact('lessons'));
    }

    public function approve(Request $request, Lesson $lesson)
    {
        $oldStatus = $lesson->status;
        $lesson->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->logReview($lesson, 'approved', $request->notes);
        \App\Services\AuditLogger::log('approve_lesson', $lesson, ['status' => $oldStatus], ['status' => 'published']);
        
        $lesson->teacher->notify(new \App\Notifications\ApprovalNotification('Lesson', 'published', $lesson->title));
        
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Lesson \"{$lesson->title}\" has been approved and published.");
    }

    public function reject(Request $request, Lesson $lesson)
    {
        $oldStatus = $lesson->status;
        $lesson->update(['status' => 'rejected']);
        $this->logReview($lesson, 'rejected', $request->notes);
        \App\Services\AuditLogger::log('reject_lesson', $lesson, ['status' => $oldStatus], ['status' => 'rejected']);
        
        $lesson->teacher->notify(new \App\Notifications\ApprovalNotification('Lesson', 'rejected', $lesson->title));
        
        Cache::forget('admin.dashboard.stats');
        
        return back()->with('success', "Lesson \"{$lesson->title}\" has been rejected.");
    }

    public function hold(Request $request, Lesson $lesson)
    {
        $oldStatus = $lesson->status;
        $lesson->update(['status' => 'on_hold']);
        $this->logReview($lesson, 'on_hold', $request->notes);
        \App\Services\AuditLogger::log('hold_lesson', $lesson, ['status' => $oldStatus], ['status' => 'on_hold']);
        $lesson->teacher->notify(new \App\Notifications\ApprovalNotification('Lesson', 'on_hold', $lesson->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Lesson \"{$lesson->title}\" has been put on hold.");
    }

    public function flag(Request $request, Lesson $lesson)
    {
        $oldStatus = $lesson->status;
        $lesson->update(['status' => 'flagged']);
        $this->logReview($lesson, 'flagged', $request->notes);
        \App\Services\AuditLogger::log('flag_lesson', $lesson, ['status' => $oldStatus], ['status' => 'flagged']);
        $lesson->teacher->notify(new \App\Notifications\ApprovalNotification('Lesson', 'flagged', $lesson->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Lesson \"{$lesson->title}\" has been flagged for changes.");
    }

    public function archive(Request $request, Lesson $lesson)
    {
        $oldStatus = $lesson->status;
        $lesson->update(['status' => 'archived']);
        $this->logReview($lesson, 'archived', $request->notes);
        \App\Services\AuditLogger::log('archive_lesson', $lesson, ['status' => $oldStatus], ['status' => 'archived']);
        $lesson->teacher->notify(new \App\Notifications\ApprovalNotification('Lesson', 'archived', $lesson->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Lesson \"{$lesson->title}\" has been archived.");
    }

    protected function logReview(Lesson $lesson, string $action, ?string $notes)
    {
        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Lesson::class,
            'content_id' => $lesson->id,
            'action' => $action,
            'notes' => $notes,
        ]);
    }

    public function preview(Lesson $lesson)
    {
        return view('admin.content.preview', compact('lesson'));
    }

    public function destroy(Lesson $lesson)
    {
        \App\Services\AuditLogger::log('delete_lesson', $lesson, $lesson->toArray(), null);
        $lesson->delete();
        return redirect()->route('admin.content')->with('success', 'Lesson deleted.');
    }
}
