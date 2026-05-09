<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\ContentReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with('teacher')->withCount(['questions', 'attempts']);
        if ($request->status) $query->where('status', $request->status);
        $quizzes = $query->latest()->paginate(20);
        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function preview(Quiz $quiz)
    {
        $quiz->load(['teacher', 'questions']);
        return view('admin.quizzes.preview', compact('quiz'));
    }

    public function approve(Request $request, Quiz $quiz)
    {
        $oldStatus = $quiz->status;
        $quiz->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Quiz::class,
            'content_id' => $quiz->id,
            'action' => 'approved',
            'notes' => $request->notes,
        ]);

        \App\Services\AuditLogger::log('approve_quiz', $quiz, ['status' => $oldStatus], ['status' => 'active']);
        $quiz->teacher->notify(new \App\Notifications\ApprovalNotification('Quiz', 'active', $quiz->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Quiz \"{$quiz->title}\" approved and activated.");
    }

    public function reject(Request $request, Quiz $quiz)
    {
        $oldStatus = $quiz->status;
        $quiz->update(['status' => 'rejected']);
        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Quiz::class,
            'content_id' => $quiz->id,
            'action' => 'rejected',
            'notes' => $request->notes,
        ]);

        \App\Services\AuditLogger::log('reject_quiz', $quiz, ['status' => $oldStatus], ['status' => 'rejected']);
        $quiz->teacher->notify(new \App\Notifications\ApprovalNotification('Quiz', 'rejected', $quiz->title));
        Cache::forget('admin.dashboard.stats');

        return back()->with('success', "Quiz \"{$quiz->title}\" rejected.");
    }

    public function hold(Request $request, Quiz $quiz)
    {
        $oldStatus = $quiz->status;
        $quiz->update(['status' => 'on_hold']);
        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Quiz::class,
            'content_id' => $quiz->id,
            'action' => 'on_hold',
            'notes' => $request->notes,
        ]);
        \App\Services\AuditLogger::log('hold_quiz', $quiz, ['status' => $oldStatus], ['status' => 'on_hold']);
        $quiz->teacher->notify(new \App\Notifications\ApprovalNotification('Quiz', 'on_hold', $quiz->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Quiz \"{$quiz->title}\" has been put on hold.");
    }

    public function flag(Request $request, Quiz $quiz)
    {
        $oldStatus = $quiz->status;
        $quiz->update(['status' => 'flagged']);
        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Quiz::class,
            'content_id' => $quiz->id,
            'action' => 'flagged',
            'notes' => $request->notes,
        ]);
        \App\Services\AuditLogger::log('flag_quiz', $quiz, ['status' => $oldStatus], ['status' => 'flagged']);
        $quiz->teacher->notify(new \App\Notifications\ApprovalNotification('Quiz', 'flagged', $quiz->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Quiz \"{$quiz->title}\" has been flagged for changes.");
    }

    public function archive(Request $request, Quiz $quiz)
    {
        $oldStatus = $quiz->status;
        $quiz->update(['status' => 'archived']);
        ContentReview::create([
            'admin_id' => auth()->id(),
            'content_type' => Quiz::class,
            'content_id' => $quiz->id,
            'action' => 'archived',
            'notes' => $request->notes,
        ]);
        \App\Services\AuditLogger::log('archive_quiz', $quiz, ['status' => $oldStatus], ['status' => 'archived']);
        $quiz->teacher->notify(new \App\Notifications\ApprovalNotification('Quiz', 'archived', $quiz->title));
        Cache::forget('admin.dashboard.stats');
        return back()->with('success', "Quiz \"{$quiz->title}\" has been archived.");
    }

    public function destroy(Quiz $quiz)
    {
        \App\Services\AuditLogger::log('delete_quiz', $quiz, $quiz->toArray(), null);
        $quiz->delete();
        return redirect()->route('admin.quizzes')->with('success', 'Quiz deleted.');
    }
}
