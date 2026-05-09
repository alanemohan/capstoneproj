<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\ContentReview;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class AdminCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('teacher')->withCount(['lessons', 'enrollments']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(20);

        return view('admin.courses.index', compact('courses'));
    }

    public function preview(Course $course)
    {
        $course->load(['teacher', 'approvedBy', 'lessons.contents', 'enrollments']);

        return view('admin.courses.preview', compact('course'));
    }

    public function approve(Request $request, Course $course)
    {
        $oldStatus = $course->status;

        try {
            $course->transitionToStatus(CourseStatus::Published);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $course->update([
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $course->lessons()->update(['status' => 'published']);

        $this->logReview($course, 'approved', $request->notes);
        \App\Services\AuditLogger::log('approve_course', $course, ['status' => $oldStatus], ['status' => 'published']);

        $course->teacher->notify(new \App\Notifications\ApprovalNotification('Course', 'published', $course->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Course \"{$course->title}\" approved and published.");
    }

    public function reject(Request $request, Course $course)
    {
        $oldStatus = $course->status;

        try {
            $course->transitionToStatus(CourseStatus::Rejected);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $this->logReview($course, 'rejected', $request->notes);
        \App\Services\AuditLogger::log('reject_course', $course, ['status' => $oldStatus], ['status' => 'rejected']);

        $course->teacher->notify(new \App\Notifications\ApprovalNotification('Course', 'rejected', $course->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Course \"{$course->title}\" rejected.");
    }

    public function hold(Request $request, Course $course)
    {
        $oldStatus = $course->status;

        try {
            $course->transitionToStatus(CourseStatus::OnHold);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $this->logReview($course, 'on_hold', $request->notes);
        \App\Services\AuditLogger::log('hold_course', $course, ['status' => $oldStatus], ['status' => 'on_hold']);
        $course->teacher->notify(new \App\Notifications\ApprovalNotification('Course', 'on_hold', $course->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Course \"{$course->title}\" has been put on hold.");
    }

    public function flag(Request $request, Course $course)
    {
        $oldStatus = $course->status;

        try {
            $course->transitionToStatus(CourseStatus::Flagged);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $this->logReview($course, 'flagged', $request->notes);
        \App\Services\AuditLogger::log('flag_course', $course, ['status' => $oldStatus], ['status' => 'flagged']);
        $course->teacher->notify(new \App\Notifications\ApprovalNotification('Course', 'flagged', $course->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Course \"{$course->title}\" has been flagged for changes.");
    }

    public function archive(Request $request, Course $course)
    {
        $oldStatus = $course->status;

        try {
            $course->transitionToStatus(CourseStatus::Archived);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        $this->logReview($course, 'archived', $request->notes);
        \App\Services\AuditLogger::log('archive_course', $course, ['status' => $oldStatus], ['status' => 'archived']);
        $course->teacher->notify(new \App\Notifications\ApprovalNotification('Course', 'archived', $course->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Course \"{$course->title}\" has been archived.");
    }

    protected function logReview(Course $course, string $action, ?string $notes)
    {
        ContentReview::create([
            'admin_id' => Auth::id(),
            'content_type' => Course::class,
            'content_id' => $course->id,
            'action' => $action,
            'notes' => $notes,
        ]);
    }

    public function destroy(Course $course)
    {
        \App\Services\AuditLogger::log('delete_course', $course, $course->toArray(), null);
        $course->delete();

        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }
}
