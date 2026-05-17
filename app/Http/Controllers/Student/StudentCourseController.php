<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponses;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudentCourseController extends Controller
{
    use ApiResponses;
    public function __construct(private EnrollmentService $enrollment) {}

    // ─── Course Catalog ───────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Course::published()->with('teacher')->withCount(['lessons', 'enrollments']);

        if ($s = $request->subject) {
            $query->where('subject', $s);
        }
        if ($cl = $request->class_level) {
            $query->where('class_level', $cl);
        }
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $courses  = $query->latest()->paginate(12);
        $enrolled = auth()->user()->enrolledCourses()->pluck('courses.id')->toArray();

        // Cart IDs so we can show "In Cart" badge on listing cards
        $inCart = CartItem::where('user_id', auth()->id())->pluck('course_id')->toArray();

        $subjects    = Cache::remember('catalog.subjects', 300, fn () =>
            Course::published()->distinct()->pluck('subject')->filter()->sort()->values()
        );
        $classLevels = Cache::remember('catalog.class_levels', 300, fn () =>
            Course::published()->distinct()->pluck('class_level')->filter()->sort()->values()
        );

        return view('student.courses.index', compact('courses', 'enrolled', 'inCart', 'subjects', 'classLevels'));
    }

    // ─── Course Detail ────────────────────────────────────────────────────────

    public function show(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        $course->load([
            'teacher',
            'enrollments',
            'lessons' => fn ($q) => $q->orderBy('order'),
        ]);

        $isEnrolled   = $this->enrollment->isEnrolled(auth()->user(), $course);
        $resumeLesson = $isEnrolled ? $course->lastAccessedLesson(auth()->id()) : null;
        $inCart       = CartItem::where('user_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->exists();

        return view('student.courses.show', compact('course', 'isEnrolled', 'resumeLesson', 'inCart'));
    }

    // ─── Enrollment (free) ────────────────────────────────────────────────────

    public function enroll(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        if (!$course->isFree()) {
            return redirect()->route('student.courses.show', $course)
                ->with('error', 'Please complete the payment to enroll in this course.');
        }

        $this->enrollment->enrollFree(auth()->user(), $course);

        return redirect()->route('student.courses.show', $course)
            ->with('success', "You are now enrolled in \"{$course->title}\"!");
    }

    // ─── Direct Purchase (Buy Now, single course) ─────────────────────────────

    public function purchase(Request $request, Course $course)
    {
        abort_if(!$course->isPublished(), 404);
        abort_if($course->isFree(), 400);

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:card,upi'],
            // Card fields — required only when paying by card
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits:16'],
            'card_expiry' => ['required_if:payment_method,card', 'nullable', 'string', 'regex:/^\d{2}\/\d{2}$/'],
            'card_cvv'    => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
            'card_name'   => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            // UPI field — required only when paying by UPI
            'upi_id'      => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100',
                              'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/'],
        ]);

        $enrollment = $this->enrollment->enrollPending(auth()->user(), $course);

        // Remove from cart if it was there
        CartItem::where('user_id', auth()->id())->where('course_id', $course->id)->delete();

        return redirect()->route('student.payment.gateway', ['enrollments' => [$enrollment->id]]);
    }

    // ─── Cart ─────────────────────────────────────────────────────────────────

    public function addToCart(Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        if ($course->isFree()) {
            return back()->with('error', 'Free courses can be enrolled directly — no cart needed.');
        }

        if ($this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        if (CartItem::where('user_id', auth()->id())->where('course_id', $course->id)->exists()) {
            return back()->with('info', 'This course is already in your cart.');
        }

        CartItem::firstOrCreate([
            'user_id'   => auth()->id(),
            'course_id' => $course->id,
        ]);

        return back()->with('success', "\"{$course->title}\" added to your cart!");
    }

    public function removeFromCart(Course $course)
    {
        CartItem::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->delete();

        return back()->with('success', 'Course removed from cart.');
    }

    public function viewCart()
    {
        // Load cart items, filter out any that are no longer published
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with(['course' => fn ($q) => $q->with('teacher')->withCount('lessons')])
            ->latest()
            ->get()
            ->filter(fn ($item) => $item->course && $item->course->isPublished() && !$this->enrollment->isEnrolled(auth()->user(), $item->course))
            ->values();

        // Remove stale items (unpublished/deleted/already-enrolled courses)
        $validIds = $cartItems->pluck('course_id');
        CartItem::where('user_id', auth()->id())
            ->whereNotIn('course_id', $validIds)
            ->delete();

        $total = $cartItems->sum(fn ($item) => (float) $item->course->price);

        return view('student.cart', compact('cartItems', 'total'));
    }

    public function checkout(Request $request)
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('course')
            ->get()
            ->filter(fn ($item) => $item->course && $item->course->isPublished() && !$this->enrollment->isEnrolled(auth()->user(), $item->course))
            ->values();

        if ($cartItems->isEmpty()) {
            return redirect()->route('student.cart')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'payment_method' => ['required', 'in:card,upi'],
            'card_number' => ['required_if:payment_method,card', 'nullable', 'digits:16'],
            'card_expiry' => ['required_if:payment_method,card', 'nullable', 'string', 'regex:/^\d{2}\/\d{2}$/'],
            'card_cvv'    => ['required_if:payment_method,card', 'nullable', 'digits_between:3,4'],
            'card_name'   => ['required_if:payment_method,card', 'nullable', 'string', 'max:100'],
            'upi_id'      => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100',
                              'regex:/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/'],
        ]);

        $enrollmentIds = [];
        foreach ($cartItems as $item) {
            if (!$this->enrollment->isEnrolled(auth()->user(), $item->course)) {
                $enrollment = $this->enrollment->enrollPending(auth()->user(), $item->course);
                $enrollmentIds[] = $enrollment->id;
            }
        }

        // Clear the entire cart
        CartItem::where('user_id', auth()->id())->delete();

        if (empty($enrollmentIds)) {
             return redirect()->route('student.my-courses')->with('info', 'You are already enrolled in these courses.');
        }

        return redirect()->route('student.payment.gateway', ['enrollments' => $enrollmentIds]);
    }

    public function mockGateway(Request $request)
    {
        $ids = $request->enrollments;
        if (!$ids || !is_array($ids)) {
            return redirect()->route('student.dashboard')->with('error', 'Invalid payment request.');
        }

        $enrollments = \App\Models\Enrollment::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->with('course')
            ->get();

        if ($enrollments->isEmpty()) {
            return redirect()->route('student.dashboard')->with('error', 'No pending enrollments found.');
        }

        $total = $enrollments->sum('amount_paid');

        return view('student.mock-gateway', compact('enrollments', 'total'));
    }

    public function paymentCallback(Request $request)
    {
        $ids    = $request->input('enrollments', []);
        $status = $request->input('status'); // 'success' or 'fail'

        if (empty($ids)) {
            return redirect()->route('student.cart')->with('error', 'No enrollments found to process.');
        }

        $enrollments = \App\Models\Enrollment::whereIn('id', $ids)
            ->where('user_id', auth()->id())
            ->get();

        if ($status === 'success') {
            foreach ($enrollments as $enrollment) {
                $this->enrollment->confirmPayment($enrollment, 'GATEWAY-' . strtoupper(\Illuminate\Support\Str::random(12)));
            }

            // Invalidate Admin Dashboard Cache so revenue, KPI metrics and activity charts update instantly
            \Illuminate\Support\Facades\Cache::forget('admin_stats');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_3');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_6');
            \Illuminate\Support\Facades\Cache::forget('admin_trends_12');
            \Illuminate\Support\Facades\Cache::forget('admin_weekly_activity');

            return redirect()->route('student.my-courses')->with('success', 'Payment successful! Welcome to your new courses.');
        } else {
            foreach ($enrollments as $enrollment) {
                $this->enrollment->failPayment($enrollment, 'User cancelled or gateway error');
            }
            return redirect()->route('student.cart')->with('error', 'Payment failed or was cancelled.');
        }
    }

    // ─── My Courses ───────────────────────────────────────────────────────────

    public function myCourses()
    {
        $enrollments = auth()->user()
            ->enrollments()
            ->with(['course' => fn ($q) => $q->withCount('lessons')->with('teacher')])
            ->latest()
            ->get();

        return view('student.courses.my-courses', compact('enrollments'));
    }

    public function requestRefund(Request $request, Course $course)
    {
        abort_if(!$course->isPublished(), 404);

        $enrollment = auth()->user()
            ->enrollments()
            ->where('course_id', $course->id)
            ->where('payment_status', 'paid')
            ->first();

        if (! $enrollment) {
            return back()->with('error', 'No paid enrollment found for this course.');
        }

        if ($enrollment->refund_status !== 'none') {
            return back()->with('error', 'A refund request already exists for this course.');
        }

        if (! $this->enrollment->canRequestRefund($enrollment)) {
            return back()->with('error', 'This course is outside the refund window.');
        }

        $validated = $request->validate([
            'refund_reason' => ['required', 'string', 'max:1000'],
            'refund_amount' => ['nullable', 'numeric', 'min:0.01', 'max:' . (float) $enrollment->amount_paid],
        ]);

        $this->enrollment->requestRefund(
            $enrollment,
            $validated['refund_reason'],
            $validated['refund_amount'] ?? null
        );

        return back()->with('success', 'Your refund request has been submitted for review.');
    }

    // ─── Lesson Player ────────────────────────────────────────────────────────

    public function lesson(Course $course, Lesson $lesson)
    {
        abort_if(!$course->isPublished(), 404);
        abort_if($lesson->course_id !== $course->id, 404);
        abort_if(!$lesson->isPublished(), 404);

        if (!$this->enrollment->isEnrolled(auth()->user(), $course)) {
            return redirect()->route('student.courses.show', $course)
                ->with('error', 'You must enroll in this course to access lessons.');
        }

        $lesson->load('contents');
        $lesson->incrementViews();

        $allLessons = $course->lessons()->orderBy('order')->get();
        $currentIdx = $allLessons->search(fn ($l) => $l->id === $lesson->id);
        $prev       = $currentIdx > 0 ? $allLessons[$currentIdx - 1] : null;
        $next       = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;

        $isCompleted = ProgressReport::where('lesson_id', $lesson->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->exists();

        $completedIds = ProgressReport::whereIn('lesson_id', $allLessons->pluck('id'))
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        return view('student.courses.lesson', compact(
            'course', 'lesson', 'allLessons', 'prev', 'next',
            'currentIdx', 'isCompleted', 'completedIds'
        ));
    }

    public function completeLesson(Course $course, Lesson $lesson)
    {
        try {
            abort_if($lesson->course_id !== $course->id, 404);
            abort_if(!$lesson->isPublished(), 404);

            if (!$this->enrollment->isEnrolled(auth()->user(), $course)) {
                return $this->errorJson('You must enroll in this course before marking lessons complete.', 403);
            }

            $report = ProgressReport::firstOrCreate(
                ['lesson_id' => $lesson->id, 'student_id' => auth()->id()],
                ['completion_percentage' => 0, 'views' => 0]
            );

            if (!$report->is_completed) {
                $report->markCompleted();
            }

            // Invalidate student caches so the dashboard updates immediately
            \Illuminate\Support\Facades\Cache::forget("student_stats_" . auth()->id());
            \Illuminate\Support\Facades\Cache::forget("student_subject_scores_" . auth()->id());
            \Illuminate\Support\Facades\Cache::forget("student_weekly_progress_" . auth()->id());

            $allLessons = $course->lessons()->orderBy('order')->get();
            $currentIdx = $allLessons->search(fn ($l) => $l->id === $lesson->id);
            $next       = $currentIdx < $allLessons->count() - 1 ? $allLessons[$currentIdx + 1] : null;

            // Check if entire course is completed
            $completedCount = ProgressReport::where('student_id', auth()->id())
                ->whereIn('lesson_id', $allLessons->pluck('id'))
                ->where('is_completed', true)
                ->count();
            
            $isCourseComplete = $completedCount === $allLessons->count();

            return $this->successJson([
                'completed' => true,
                'is_course_complete' => $isCourseComplete,
                'next_url'  => $next ? route('student.courses.lesson', [$course, $next]) : null,
            ], 'Lesson marked complete');
        } catch (\Throwable $e) {
            \App\Services\AuditLogger::log('error_complete_lesson', null, ['exception' => $e->getMessage()]);
            return $this->errorJson('Failed to complete lesson. Please try again later.', 500);
        }
    }

    public function resetProgress(Course $course)
    {
        if (!$this->enrollment->isEnrolled(auth()->user(), $course)) {
            return back()->with('error', 'You must be enrolled to reset progress.');
        }

        $lessonIds = $course->lessons()->pluck('id');
        ProgressReport::where('student_id', auth()->id())
            ->whereIn('lesson_id', $lessonIds)
            ->delete();

        return redirect()->route('student.courses.show', $course)
            ->with('success', 'Course progress has been reset. You can start again!');
    }
}
