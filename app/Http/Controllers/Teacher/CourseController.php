<?php

namespace App\Http\Controllers\Teacher;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::where('teacher_id', auth()->id())
            ->withCount(['lessons', 'enrollments'])
            ->latest()
            ->paginate(15);

        return view('teacher.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'subject'     => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'language'    => ['required', 'string', 'in:en,hi,pa'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        Course::create([
            'teacher_id'  => auth()->id(),
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'subject'     => $validated['subject'],
            'class_level' => $validated['class_level'],
            'language'    => $validated['language'],
            'thumbnail'   => $thumbnailPath,
            'status'      => 'draft',
        ]);

        return redirect()->route('teacher.courses')->with('success', 'Course created! Add lessons and submit for review.');
    }

    public function show(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $course->load(['lessons.contents']);
        return view('teacher.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        return view('teacher.courses.create', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'subject'     => ['required', 'string'],
            'class_level' => ['required', 'string'],
            'language'    => ['required', 'string', 'in:en,hi,pa'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $nextStatus = $course->status === CourseStatus::Published->value
            ? CourseStatus::Pending->value
            : $course->status;

        if ($nextStatus === CourseStatus::Pending->value && $course->status === CourseStatus::Published->value) {
            $course->approved_by = null;
            $course->approved_at = null;
        }

        $course->update(array_merge($validated, ['status' => $nextStatus]));

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Course updated.');
    }

    public function destroy(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();
        return redirect()->route('teacher.courses')->with('success', 'Course deleted.');
    }

    public function submit(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($course->lessons()->count() === 0, 422, 'Add at least one lesson before submitting.');

        try {
            $course->transitionToStatus(CourseStatus::Pending);
        } catch (InvalidArgumentException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Course submitted for admin review!');
    }

    public function addLesson(Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        return view('teacher.courses.add-lesson', compact('course'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);

        $request->validate([
            'title'                        => ['required', 'string', 'max:200'],
            'description'                  => ['required', 'string'],
            'contents'                     => ['required', 'array', 'min:1'],
            'contents.*.type'              => ['required', 'in:video,pdf,image,text'],
            'contents.*.title'             => ['nullable', 'string', 'max:200'],
            'contents.*.content_text'      => ['nullable', 'string'],
            'contents.*.file'              => ['nullable', 'file', 'max:51200', 'mimes:pdf,mp4,webm,jpg,jpeg,png'],
        ]);

        $lesson = Lesson::create([
            'teacher_id'  => auth()->id(),
            'course_id'   => $course->id,
            'title'       => $request->title,
            'description' => $request->description,
            'subject'     => $course->subject ?? 'General',
            'class_level' => $course->class_level ?? 'All',
            'language'    => $course->language ?? 'en',
            'file_type'   => $request->contents[0]['type'] ?? 'text',
            'status'      => 'pending',
            'order'       => $course->lessons()->max('order') + 1,
        ]);

        foreach ($request->contents as $i => $block) {
            $filePath = null;
            if (isset($block['file']) && $request->hasFile("contents.{$i}.file")) {
                $filePath = $request->file("contents.{$i}.file")->store('lesson-contents', 'public');
            }

            LessonContent::create([
                'lesson_id'    => $lesson->id,
                'title'        => $block['title'] ?? null,
                'type'         => $block['type'],
                'file_path'    => $filePath,
                'content_text' => $block['content_text'] ?? null,
                'order'        => $i,
            ]);
        }

        return redirect()->route('teacher.courses.show', $course)->with('success', 'Lesson added to course!');
    }

    public function destroyLesson(Course $course, Lesson $lesson)
    {
        abort_if($course->teacher_id !== auth()->id(), 403);
        abort_if($lesson->course_id !== $course->id, 403);

        foreach ($lesson->contents as $content) {
            if ($content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }
        }

        $lesson->delete();
        return back()->with('success', 'Lesson removed from course.');
    }
}
