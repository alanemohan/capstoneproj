@extends('layouts.student')

@section('title', __('messages.dashboard') . ' - Nabha Learning')

@section('student-content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-primary-600 to-indigo-600 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">{{ __('messages.welcome') }}, {{ auth()->user()->name }}!</h1>
                <p class="text-primary-200 mt-1">{{ auth()->user()->class_level }} | {{ auth()->user()->school }}</p>
                <p class="text-sm mt-3 text-primary-100">{{ __('messages.keep_learning') }}</p>
            </div>
            @if(auth()->user()->streak_count > 0)
                <div class="flex-shrink-0 bg-white bg-opacity-20 rounded-xl px-4 py-3 text-center">
                    <div class="text-2xl font-bold">🔥 {{ auth()->user()->streak_count }}</div>
                    <div class="text-xs text-primary-100 mt-0.5">{{ __('messages.day_streak') }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $lessonsCompleted }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('messages.lessons_completed') }}</div>
            <div class="text-xs text-primary-600 mt-1">{{ __('messages.of') }} {{ $totalLessons }} {{ __('messages.available') }}</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $quizAttempts->count() }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('messages.quizzes_attempted') }}</div>
            <div class="text-xs text-emerald-600 mt-1">{{ $quizAttempts->where('passed', true)->count() }} {{ __('messages.passed') }}</div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ round($avgScore, 1) }}%</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('messages.avg_score') }}</div>
            <div class="text-xs {{ $avgScore >= 60 ? 'text-emerald-600' : 'text-orange-500' }} mt-1">
                {{ $avgScore >= 60 ? __('messages.good_performance') : __('messages.keep_practicing') }}
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-800">{{ $totalQuizzes }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ __('messages.quizzes_available') }}</div>
            <div class="text-xs text-primary-600 mt-1">{{ __('messages.take_quiz_now') }}</div>
        </div>
    </div>

    <!-- Notice Board & Live Classes Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Announcements -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span>📢</span> {{ __('messages.recent_announcements') }}
            </h3>
            @if($announcements->isEmpty())
                <p class="text-sm text-gray-500">{{ __('messages.no_announcements') }}</p>
            @else
                <div class="space-y-4">
                    @foreach($announcements as $announcement)
                        <div class="border-l-4 border-emerald-500 pl-3">
                            <h4 class="text-sm font-semibold text-gray-800">{{ $announcement->title }}</h4>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $announcement->content }}</p>
                            <span class="text-[10px] text-gray-400 mt-1 block">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Live Classes -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="animate-pulse">🔴</span> {{ __('messages.upcoming_live_classes') }}
            </h3>
            @if($upcomingClasses->isEmpty())
                <p class="text-sm text-gray-500">{{ __('messages.no_live_classes') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($upcomingClasses as $class)
                        <div class="bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">{{ $class->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $class->scheduled_at->format('D, M d \a\t h:i A') }}</p>
                            </div>
                            <a href="{{ $class->meeting_link }}" target="_blank" class="text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded transition">
                                {{ __('messages.join') }}
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.assigned_mentor') }}</h3>
            @if($student->assignedMentor)
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-gray-800">{{ $student->assignedMentor->name }}</p>
                    <p class="text-xs text-gray-500">{{ $student->assignedMentor->subject_specialization ?? __('messages.general_mentor') }}</p>
                    <p class="text-xs text-gray-500">{{ $student->assignedMentor->email }}</p>
                    <p class="text-xs text-gray-500">{{ $student->assignedMentor->phone ?? __('messages.no_phone') }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500">{{ __('messages.no_mentor_assigned') }}</p>
            @endif
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.notifications') }}</h3>
            @if($student->portalNotifications->isEmpty())
                <p class="text-sm text-gray-500">{{ __('messages.no_notifications') }}</p>
            @else
                <div class="space-y-3">
                    @foreach($student->portalNotifications as $notice)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-sm font-semibold text-gray-800">{{ $notice->title }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ $notice->message }}</p>
                            <p class="text-[11px] text-gray-400 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.weekly_activity') }}</h3>
            <canvas id="weeklyChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.subject_performance') }}</h3>
            <canvas id="subjectChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Lessons -->
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-800">{{ __('messages.recently_accessed') }}</h3>
            <a href="{{ route('student.lessons') }}" class="text-sm text-primary-600 hover:underline">{{ __('messages.view_all') }} →</a>
        </div>
        @if($recentLessons->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500 text-sm">{{ __('messages.no_lessons_accessed') }}</p>
                <a href="{{ route('student.lessons') }}" class="mt-3 inline-block bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700 transition">
                    {{ __('messages.browse_lessons') }}
                </a>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentLessons as $report)
                    @if($report->lesson)
                    <a href="{{ route('student.lesson.show', $report->lesson_id) }}"
                       class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-lg transition group">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm text-gray-800 truncate group-hover:text-primary-600">{{ $report->lesson->title }}</p>
                            <p class="text-xs text-gray-500">{{ $report->lesson->subject }} | {{ $report->last_accessed?->diffForHumans() }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            @if($report->is_completed)
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">{{ __('messages.done') }}</span>
                            @else
                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">{{ __('messages.in_progress') }}</span>
                            @endif
                        </div>
                    </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="{{ route('student.lessons') }}" class="bg-primary-600 text-white rounded-xl p-4 text-center hover:bg-primary-700 transition flex flex-col justify-center">
            <div class="text-sm font-semibold">{{ __('messages.browse_lessons') }}</div>
        </a>
        <a href="{{ route('student.quizzes') }}" class="bg-emerald-600 text-white rounded-xl p-4 text-center hover:bg-emerald-700 transition flex flex-col justify-center">
            <div class="text-sm font-semibold">{{ __('messages.take_quiz') }}</div>
        </a>
        <a href="{{ route('student.assignments.index') }}" class="bg-blue-600 text-white rounded-xl p-4 text-center hover:bg-blue-700 transition flex flex-col justify-center">
            <div class="text-sm font-semibold">{{ __('messages.my_assignments') }}</div>
        </a>
        <a href="{{ route('student.chatbot') }}" class="bg-purple-600 text-white rounded-xl p-4 text-center hover:bg-purple-700 transition flex flex-col justify-center">
            <div class="text-sm font-semibold">{{ __('messages.ask_ai') }}</div>
        </a>
        <a href="{{ route('student.careers') }}" class="bg-orange-500 text-white rounded-xl p-4 text-center hover:bg-orange-600 transition flex flex-col justify-center">
            <div class="text-sm font-semibold">{{ __('messages.roadmaps') }}</div>
        </a>
    </div>
</div>

@push('scripts')
<script>
const weeklyData = @json($progressByWeek);
const subjectData = @json($subjectScores);

new Chart(document.getElementById('weeklyChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(weeklyData),
        datasets: [{
            label: @json(__('messages.chart_lessons')),
            data: Object.values(weeklyData),
            backgroundColor: 'rgba(79, 70, 229, 0.8)',
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('subjectChart'), {
    type: 'radar',
    data: {
        labels: Object.keys(subjectData),
        datasets: [{
            label: @json(__('messages.score_percent')),
            data: Object.values(subjectData),
            backgroundColor: 'rgba(79, 70, 229, 0.2)',
            borderColor: 'rgba(79, 70, 229, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(79, 70, 229, 1)',
        }]
    },
    options: {
        responsive: true,
        scales: { r: { beginAtZero: true, max: 100, ticks: { stepSize: 20 } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection
