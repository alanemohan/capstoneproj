<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\ProgressReport;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // period = number of months to show in trend charts (3 / 6 / 12)
        $period = in_array((int) $request->period, [3, 6, 12])
            ? (int) $request->period
            : 6;

        // Platform-wide counts — cached for 60 seconds
        $stats = Cache::remember('admin_stats', 60, function () {
            return [
                'total_students'    => User::where('role', 'student')->count(),
                'pending_students'  => User::where('role', 'student')->where('status', 'pending')->count(),
                'total_teachers'    => User::where('role', 'teacher')->count(),
                'pending_teachers'  => User::where('role', 'teacher')->where('status', 'pending')->count(),
                'total_lessons'     => Lesson::count(),
                'pending_lessons'   => Lesson::where('status', 'pending')->count(),
                'total_quizzes'     => Quiz::count(),
                'pending_quizzes'   => Quiz::where('status', 'pending')->count(),
                'total_attempts'    => QuizAttempt::where('status', 'completed')->count(),
                'chatbot_queries'   => ChatbotLog::count(),
                'active_users'      => User::where('is_active', true)->count(),
                'active_students'   => User::where('role', 'student')->where('is_active', true)->count(),
                'inactive_students' => User::where('role', 'student')->where('is_active', false)->count(),
                'active_teachers'   => User::where('role', 'teacher')->where('is_active', true)->count(),
                'inactive_teachers' => User::where('role', 'teacher')->where('is_active', false)->count(),
                'total_courses'     => Course::count(),
                'published_courses' => Course::where('status', 'published')->count(),
                'pending_courses'   => Course::where('status', 'pending')->count(),
                'total_enrollments' => Enrollment::count(),
                'total_revenue'     => (float) \App\Models\Payment::sum('amount'),
                'refunds_requested' => Enrollment::where('refund_status', 'requested')->count(),
                'refunds_completed' => Enrollment::whereIn('refund_status', ['partial', 'full'])->count(),
            ];
        });

        $recentUsers   = User::latest()->take(5)->get();
        $recentLessons = Lesson::with('teacher')->latest()->take(5)->get();
        $recentCourses = Course::with('teacher')->withCount('enrollments')->latest()->take(5)->get();

        // Trend data — cached for 60 seconds
        $trends = Cache::remember("admin_trends_{$period}", 60, function () use ($period, $stats) {
            $monthlyRegistrations = [];
            $monthlyRevenue       = [];
            $roleDistribution     = [
                'Students' => $stats['total_students'],
                'Teachers' => $stats['total_teachers'],
                'Admins'   => User::where('role', 'admin')->count(),
            ];

            for ($i = $period - 1; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $label = $month->format('M y');

                $monthlyRegistrations[$label] = User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();

                $monthlyRevenue[$label] = (float) \App\Models\Payment::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');
            }

            return compact('monthlyRegistrations', 'monthlyRevenue', 'roleDistribution');
        });

        $monthlyRegistrations = $trends['monthlyRegistrations'];
        $monthlyRevenue       = $trends['monthlyRevenue'];
        $roleDistribution     = $trends['roleDistribution'];

        // Weekly Activity (Last 7 days) — cached for 60 seconds
        $weeklyActivity = Cache::remember('admin_weekly_activity', 60, function () {
            $activity = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayLabel = $date->format('D');
                $activity[$dayLabel] = [
                    'logins'      => \Illuminate\Support\Facades\DB::table('sessions')->count(), // Simplified proxy for active sessions
                    'enrollments' => Enrollment::whereDate('enrolled_at', $date->toDateString())->count(),
                    'attempts'    => QuizAttempt::whereDate('created_at', $date->toDateString())->count(),
                    'completions' => ProgressReport::whereDate('updated_at', $date->toDateString())->where('is_completed', true)->count(),
                ];
            }
            return $activity;
        });

        // Subject Performance (Platform-wide averages) — cached for 60 seconds
        $subjectStats = Cache::remember('admin_subject_stats', 60, function () {
            $subjects = ['Mathematics', 'Science', 'English', 'Hindi', 'Social Studies'];
            $stats = [];
            foreach ($subjects as $subject) {
                $avg = QuizAttempt::join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                    ->where('quiz_attempts.status', 'completed')
                    ->where('quizzes.subject', $subject)
                    ->avg('quiz_attempts.percentage');
                $stats[$subject] = round($avg ?? 0, 1);
            }
            return $stats;
        });

        return view('admin.dashboard', compact(
            'stats', 'recentUsers', 'recentLessons', 'recentCourses',
            'monthlyRegistrations', 'monthlyRevenue', 'roleDistribution', 
            'period', 'weeklyActivity', 'subjectStats'
        ));
    }
    public function auditLogs(Request $request)
    {
        $query = \App\Models\AuditLog::with('user')->latest();

        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $logs = $query->paginate(30);

        return view('admin.audit-logs', compact('logs'));
    }
}
