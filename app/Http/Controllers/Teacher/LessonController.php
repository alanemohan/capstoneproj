<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->withCount(['progressReports', 'quizzes'])
            ->latest()->paginate(15);

        return view('teacher.lessons.index', compact('lessons'));
    }

    public function create()
    {
        $courses = \App\Models\Course::where('teacher_id', auth()->id())->get();
        return view('teacher.lessons.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'language' => ['required', 'string', 'in:en,hi,pa'],
            'file_type' => ['required', 'in:pdf,video,text,image'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:512000', 'mimes:pdf,mp4,mov,avi,mkv,webm,jpg,jpeg,png'],
            'file_path' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        $filePath = null;
        if ($request->filled('file_path')) {
            $filePath = $request->input('file_path');
        } elseif ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lessons', 'public');
        }

        Lesson::create([
            'teacher_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $validated['subject'],
            'class_level' => $validated['class_level'],
            'language' => $validated['language'],
            'file_type' => $validated['file_type'],
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'status' => 'pending',
            'course_id' => $validated['course_id'] ?? null,
        ]);

        $teacherId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("teacher_stats_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("teacher_analytics_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("admin.dashboard.stats");
        \Illuminate\Support\Facades\Cache::forget("catalog.subjects");
        \Illuminate\Support\Facades\Cache::forget("catalog.class_levels");

        return redirect()->route('teacher.lessons')
            ->with('success', 'Lesson submitted for approval! Admin will review it shortly.');
    }

    public function edit(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        $courses = \App\Models\Course::where('teacher_id', auth()->id())->get();
        return view('teacher.lessons.create', compact('lesson', 'courses'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'language' => ['required', 'string', 'in:en,hi,pa'],
            'content' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:512000', 'mimes:pdf,mp4,mov,avi,mkv,webm,jpg,jpeg,png'],
            'file_path' => ['nullable', 'string'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ]);

        if ($request->filled('file_path')) {
            if ($lesson->file_path && $lesson->file_path !== $request->input('file_path')) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $validated['file_path'] = $request->input('file_path');
        } elseif ($request->hasFile('file')) {
            if ($lesson->file_path) {
                Storage::disk('public')->delete($lesson->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('lessons', 'public');
        }

        // Exclude file object if present to avoid update issues
        unset($validated['file']);

        $lesson->update(array_merge($validated, ['status' => 'pending']));

        $teacherId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("teacher_stats_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("teacher_analytics_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("admin.dashboard.stats");
        \Illuminate\Support\Facades\Cache::forget("catalog.subjects");
        \Illuminate\Support\Facades\Cache::forget("catalog.class_levels");

        return redirect()->route('teacher.lessons')->with('success', 'Lesson updated and resubmitted for approval.');
    }

    public function destroy(Lesson $lesson)
    {
        $this->authorize('delete', $lesson);
        if ($lesson->file_path) {
            Storage::disk('public')->delete($lesson->file_path);
        }
        $lesson->delete();

        $teacherId = auth()->id();
        \Illuminate\Support\Facades\Cache::forget("teacher_stats_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("teacher_analytics_{$teacherId}");
        \Illuminate\Support\Facades\Cache::forget("admin.dashboard.stats");
        \Illuminate\Support\Facades\Cache::forget("catalog.subjects");
        \Illuminate\Support\Facades\Cache::forget("catalog.class_levels");

        return redirect()->route('teacher.lessons')->with('success', 'Lesson deleted.');
    }
}
