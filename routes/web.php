<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\TeacherApprovalController;
use App\Http\Controllers\Admin\MentorManagementController;
use App\Http\Controllers\Admin\TeacherManagerController;
use App\Http\Controllers\Admin\StudentManagerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ChatbotQaController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\QuizAttemptController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentCourseController;
use App\Http\Controllers\Student\AssignmentController as StudentAssignmentController;
use App\Http\Controllers\Teacher\AnalyticsController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfileController;
use App\Http\Controllers\Teacher\QuizController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Teacher\ReportController as TeacherReportController;
use App\Http\Controllers\Teacher\BatchController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\AssignmentController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            default   => redirect()->route('student.dashboard'),
        };
    }
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/notifications/mark-all-read', function() {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.mark-all-read');


Route::get('/pending-approval', function () {
    return view('auth.pending-approval');
})->name('pending-approval')->middleware('auth');

// Student Routes
Route::middleware(['auth', 'role:student', 'approved'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [StudentProfileController::class, 'updatePassword'])->name('profile.password');

    // Courses
    Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses');
    Route::get('/courses/my-courses', [StudentCourseController::class, 'myCourses'])->name('my-courses');
    Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{course}/enroll', [StudentCourseController::class, 'enroll'])->name('courses.enroll');
    Route::post('/courses/{course}/purchase', [StudentCourseController::class, 'purchase'])->name('courses.purchase')->middleware('throttle:checkout');
    Route::get('/courses/{course}/lessons/{lesson}', [StudentCourseController::class, 'lesson'])->name('courses.lesson');
    Route::post('/courses/{course}/lessons/{lesson}/complete', [StudentCourseController::class, 'completeLesson'])->name('courses.lesson.complete');
    Route::post('/courses/{course}/reset-progress', [StudentCourseController::class, 'resetProgress'])->name('courses.reset');
    Route::post('/courses/{course}/refund', [StudentCourseController::class, 'requestRefund'])->name('courses.refund');

    // Cart
    Route::get('/cart', [StudentCourseController::class, 'viewCart'])->name('cart');
    Route::post('/cart/{course}/add', [StudentCourseController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{course}/remove', [StudentCourseController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/cart/checkout', [StudentCourseController::class, 'checkout'])->name('cart.checkout')->middleware('throttle:checkout');

    // Payments
    Route::get('/payment/gateway', [StudentCourseController::class, 'mockGateway'])->name('payment.gateway');
    Route::post('/payment/callback', [StudentCourseController::class, 'paymentCallback'])->name('payment.callback');

    // Lessons (standalone)
    Route::get('/lessons', [StudentController::class, 'lessons'])->name('lessons');
    Route::get('/lessons/{lesson}', [StudentController::class, 'showLesson'])->name('lesson.show');
    Route::post('/lessons/{lesson}/complete', [StudentController::class, 'completeLesson'])->name('lesson.complete');
    Route::get('/lessons/{lesson}/download', [StudentController::class, 'downloadLesson'])->name('lesson.download');

    // Quizzes
    Route::get('/quizzes', [QuizAttemptController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/{quiz}/start', [QuizAttemptController::class, 'startQuiz'])->name('quiz.start');
    Route::post('/quizzes/{quiz}/submit', [QuizAttemptController::class, 'submitQuiz'])->name('quiz.submit');
    Route::get('/quiz-result/{attempt}', [QuizAttemptController::class, 'result'])->name('quiz.result');

    // Assignments
    Route::get('/assignments', [StudentAssignmentController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [StudentAssignmentController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/submit', [StudentAssignmentController::class, 'submit'])->name('assignments.submit');

    // Chatbot
    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot');
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat')->middleware('throttle:chatbot');
    Route::post('/chatbot/new', [ChatbotController::class, 'newChat'])->name('chatbot.new');
    Route::get('/chatbot/history', [ChatbotController::class, 'history'])->name('chatbot.history');
    Route::get('/chatbot/conversations', [ChatbotController::class, 'conversations'])->name('chatbot.conversations');
    Route::post('/chatbot/feedback/{log}', [ChatbotController::class, 'feedback'])->name('chatbot.feedback');
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

    // New features
    Route::get('/scholarships', [\App\Http\Controllers\Student\ScholarshipController::class, 'index'])->name('scholarships');
    Route::get('/schemes', [\App\Http\Controllers\Student\SchemeController::class, 'index'])->name('schemes');
    Route::get('/mentors', [\App\Http\Controllers\Student\MentorController::class, 'index'])->name('mentors');
    Route::post('/mentors/email', [\App\Http\Controllers\Student\MentorController::class, 'sendEmail'])->name('mentors.email');
    Route::get('/complaints', [\App\Http\Controllers\Student\ComplaintController::class, 'index'])->name('complaints');
    Route::post('/complaints', [\App\Http\Controllers\Student\ComplaintController::class, 'store'])->name('complaints.store');
});

// Teacher Routes
Route::middleware(['auth', 'role:teacher', 'approved'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', function () {
        $teacher = auth()->user();
        $teacherId = $teacher->id;

        $stats = \Illuminate\Support\Facades\Cache::remember("teacher_stats_{$teacherId}", 60, function () use ($teacherId) {
            $lessonsCount   = \App\Models\Lesson::where('teacher_id', $teacherId)->count();
            $quizzesCount   = \App\Models\Quiz::where('teacher_id', $teacherId)->count();
            $studentsReached = \App\Models\ProgressReport::whereIn('lesson_id',
                \App\Models\Lesson::where('teacher_id', $teacherId)->pluck('id')
            )->distinct('student_id')->count();

            return compact('lessonsCount', 'quizzesCount', 'studentsReached');
        });

        $lessonsCount   = $stats['lessonsCount'];
        $quizzesCount   = $stats['quizzesCount'];
        $studentsReached = $stats['studentsReached'];

        return view('teacher.dashboard', compact('teacher', 'lessonsCount', 'quizzesCount', 'studentsReached'));
    })->name('dashboard');

    // Profile
    Route::get('/profile', [TeacherProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [TeacherProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [TeacherProfileController::class, 'updatePassword'])->name('profile.password');

    // Reports
    Route::get('/reports', [TeacherReportController::class, 'index'])->name('reports');

    // Chatbot Q&A training
    Route::get('/chatbot-qa', [ChatbotQaController::class, 'index'])->name('chatbot-qa');
    Route::post('/chatbot-qa', [ChatbotQaController::class, 'store'])->name('chatbot-qa.store');
    Route::put('/chatbot-qa/{qa}', [ChatbotQaController::class, 'update'])->name('chatbot-qa.update');
    Route::delete('/chatbot-qa/{qa}', [ChatbotQaController::class, 'destroy'])->name('chatbot-qa.destroy');

    // Lessons
    Route::get('/lessons', [LessonController::class, 'index'])->name('lessons');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    // Quizzes
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/create', [QuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::get('/quizzes/{quiz}/analytics', [QuizController::class, 'analytics'])->name('quizzes.analytics');
    Route::patch('/quizzes/{quiz}/toggle-status', [QuizController::class, 'toggleStatus'])->name('quizzes.toggle');
    Route::delete('/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/student-progress', [AnalyticsController::class, 'studentProgress'])->name('student.progress');
    Route::get('/student-progress/export', [AnalyticsController::class, 'exportCsv'])->name('student.progress.export');

    // Course management
    Route::get('/courses', [CourseController::class, 'index'])->name('courses');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/submit', [CourseController::class, 'submit'])->name('courses.submit');
    Route::get('/courses/{course}/add-lesson', [CourseController::class, 'addLesson'])->name('courses.add-lesson');
    Route::post('/courses/{course}/add-lesson', [CourseController::class, 'storeLesson'])->name('courses.store-lesson');
    Route::delete('/courses/{course}/lessons/{lesson}', [CourseController::class, 'destroyLesson'])->name('courses.destroy-lesson');

    // Live Classes
    Route::get('/live-classes', [\App\Http\Controllers\Teacher\LiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('/live-classes/create', [\App\Http\Controllers\Teacher\LiveClassController::class, 'create'])->name('live-classes.create');
    Route::post('/live-classes', [\App\Http\Controllers\Teacher\LiveClassController::class, 'store'])->name('live-classes.store');
    Route::get('/live-classes/{liveClass}/edit', [\App\Http\Controllers\Teacher\LiveClassController::class, 'edit'])->name('live-classes.edit');
    Route::put('/live-classes/{liveClass}', [\App\Http\Controllers\Teacher\LiveClassController::class, 'update'])->name('live-classes.update');
    Route::delete('/live-classes/{liveClass}', [\App\Http\Controllers\Teacher\LiveClassController::class, 'destroy'])->name('live-classes.destroy');

    // Announcements
    Route::get('/announcements', [\App\Http\Controllers\Teacher\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [\App\Http\Controllers\Teacher\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [\App\Http\Controllers\Teacher\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [\App\Http\Controllers\Teacher\AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    // Batches
    Route::resource('batches', BatchController::class);
    Route::post('/batches/{batch}/students', [BatchController::class, 'addStudent'])->name('batches.students.add');
    Route::delete('/batches/{batch}/students/{student}', [BatchController::class, 'removeStudent'])->name('batches.students.remove');

    // Attendance
    Route::post('/batches/{batch}/attendance', [AttendanceController::class, 'store'])->name('batches.attendance.store');

    // Assignments
    Route::resource('assignments', AssignmentController::class);
    Route::post('/assignments/{assignment}/submissions/{submission}/grade', [AssignmentController::class, 'grade'])->name('assignments.grade');

    // Chatbot widget (teacher)
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat')->middleware('throttle:chatbot');
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit-logs');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');

    // Teacher approval
    Route::get('/teachers', [TeacherApprovalController::class, 'index'])->name('teachers');
    Route::patch('/teachers/{user}/approve', [TeacherApprovalController::class, 'approve'])->name('teachers.approve');
    Route::patch('/teachers/{user}/reject', [TeacherApprovalController::class, 'reject'])->name('teachers.reject');
    Route::patch('/teachers/{user}/suspend', [TeacherApprovalController::class, 'suspend'])->name('teachers.suspend');
    Route::delete('/teachers/{user}', [TeacherApprovalController::class, 'destroy'])->name('teachers.destroy');

    // Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle');
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // Content
    Route::get('/content', [ContentController::class, 'index'])->name('content');
    Route::get('/content/{lesson}/preview', [ContentController::class, 'preview'])->name('content.preview');
    Route::patch('/content/{lesson}/approve', [ContentController::class, 'approve'])->name('content.approve');
    Route::patch('/content/{lesson}/reject', [ContentController::class, 'reject'])->name('content.reject');
    Route::patch('/content/{lesson}/hold', [ContentController::class, 'hold'])->name('content.hold');
    Route::patch('/content/{lesson}/flag', [ContentController::class, 'flag'])->name('content.flag');
    Route::patch('/content/{lesson}/archive', [ContentController::class, 'archive'])->name('content.archive');
    Route::delete('/content/{lesson}', [ContentController::class, 'destroy'])->name('content.destroy');

    // Chatbot Q&A training
    Route::get('/chatbot-qa', [ChatbotQaController::class, 'index'])->name('chatbot-qa');
    Route::post('/chatbot-qa', [ChatbotQaController::class, 'store'])->name('chatbot-qa.store');
    Route::put('/chatbot-qa/{qa}', [ChatbotQaController::class, 'update'])->name('chatbot-qa.update');
    Route::delete('/chatbot-qa/{qa}', [ChatbotQaController::class, 'destroy'])->name('chatbot-qa.destroy');

    // Course approval
    Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses');
    Route::get('/courses/{course}/preview', [AdminCourseController::class, 'preview'])->name('courses.preview');
    Route::patch('/courses/{course}/approve', [AdminCourseController::class, 'approve'])->name('courses.approve');
    Route::patch('/courses/{course}/reject', [AdminCourseController::class, 'reject'])->name('courses.reject');
    Route::patch('/courses/{course}/hold', [AdminCourseController::class, 'hold'])->name('courses.hold');
    Route::patch('/courses/{course}/flag', [AdminCourseController::class, 'flag'])->name('courses.flag');
    Route::patch('/courses/{course}/archive', [AdminCourseController::class, 'archive'])->name('courses.archive');
    Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

    // Quiz approval
    Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/{quiz}/preview', [AdminQuizController::class, 'preview'])->name('quizzes.preview');
    Route::patch('/quizzes/{quiz}/approve', [AdminQuizController::class, 'approve'])->name('quizzes.approve');
    Route::patch('/quizzes/{quiz}/reject', [AdminQuizController::class, 'reject'])->name('quizzes.reject');
    Route::patch('/quizzes/{quiz}/hold', [AdminQuizController::class, 'hold'])->name('quizzes.hold');
    Route::patch('/quizzes/{quiz}/flag', [AdminQuizController::class, 'flag'])->name('quizzes.flag');
    Route::patch('/quizzes/{quiz}/archive', [AdminQuizController::class, 'archive'])->name('quizzes.archive');
    Route::delete('/quizzes/{quiz}', [AdminQuizController::class, 'destroy'])->name('quizzes.destroy');

    // Student & Teacher Management
    Route::get('/students-manager', [StudentManagerController::class, 'index'])->name('students_manager.index');
    Route::get('/students-manager/export', [StudentManagerController::class, 'export'])->name('students_manager.export');
    Route::get('/students-manager/{student}', [StudentManagerController::class, 'show'])->name('students_manager.show');
    Route::patch('/students-manager/{student}/toggle', [StudentManagerController::class, 'toggleActive'])->name('students_manager.toggle');
    Route::patch('/students-manager/{student}/approve', [StudentManagerController::class, 'approve'])->name('students_manager.approve');
    Route::patch('/students-manager/{student}/reject', [StudentManagerController::class, 'reject'])->name('students_manager.reject');
    Route::delete('/students-manager/{student}', [StudentManagerController::class, 'destroy'])->name('students_manager.destroy');

    Route::get('/teachers-manager', [TeacherManagerController::class, 'index'])->name('teachers_manager.index');
    Route::get('/teachers-manager/{teacher}', [TeacherManagerController::class, 'show'])->name('teachers_manager.show');
    Route::patch('/teachers-manager/{teacher}/toggle', [TeacherManagerController::class, 'toggleActive'])->name('teachers_manager.toggle');
    Route::delete('/teachers-manager/{teacher}', [TeacherManagerController::class, 'destroy'])->name('teachers_manager.destroy');

    // Mentor management
    Route::get('/mentor-management', [MentorManagementController::class, 'index'])->name('mentor-management.index');
    Route::post('/mentor-management/assign', [MentorManagementController::class, 'assign'])->name('mentor-management.assign');

    // Scholarships
    Route::resource('scholarships', \App\Http\Controllers\Admin\ScholarshipController::class)->except(['create', 'show', 'edit']);

    // Government Schemes
    Route::resource('schemes', \App\Http\Controllers\Admin\SchemeController::class)->except(['create', 'show', 'edit']);

    // Announcements
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['create', 'show', 'edit']);

    // Translation Management
    Route::get('/translations', [\App\Http\Controllers\Admin\TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations/retranslate', [\App\Http\Controllers\Admin\TranslationController::class, 'retranslate'])->name('translations.retranslate');
    Route::post('/translations/bulk', [\App\Http\Controllers\Admin\TranslationController::class, 'bulkRetranslate'])->name('translations.bulk');
    Route::put('/translations/{type}/{id}', [\App\Http\Controllers\Admin\TranslationController::class, 'update'])->name('translations.update');

    // Complaints
    Route::get('complaints', [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::patch('complaints/{complaint}/status', [\App\Http\Controllers\Admin\ComplaintController::class, 'updateStatus'])->name('complaints.status');

    // Reconciliation
    Route::get('reconciliation', [\App\Http\Controllers\Admin\ReconciliationController::class, 'index'])->name('reconciliation.index');
    Route::patch('reconciliation/{enrollment}/refund/approve', [\App\Http\Controllers\Admin\ReconciliationController::class, 'approveRefund'])->name('reconciliation.refund.approve');
    Route::patch('reconciliation/{enrollment}/refund/reject', [\App\Http\Controllers\Admin\ReconciliationController::class, 'rejectRefund'])->name('reconciliation.refund.reject');

    // Admin Chatbot log access
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat'])->name('chatbot.chat')->middleware('throttle:chatbot');
    Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
});
