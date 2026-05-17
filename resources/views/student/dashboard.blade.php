@extends('layouts.student')

@section('title', __('messages.dashboard') . ' — Nabha Learning')

@section('student-content')
<div class="space-y-6">
    <!-- Welcome Header with Gradient Accent -->
    <div class="glass-card p-6 relative overflow-hidden animate-fade-in">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-violet-500/10 to-transparent rounded-full blur-3xl -mr-20 -mt-20"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-cyan-500/8 to-transparent rounded-full blur-2xl -ml-16 -mb-16"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white/95 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.welcome') }}, {{ auth()->user()->name }}!</h1>
                <p class="text-violet-300/80 font-medium mt-1 text-sm">{{ auth()->user()->class_level }} · {{ auth()->user()->school }}</p>
                <p class="text-xs mt-3 text-white/35 font-medium">{{ __('messages.keep_learning') }}</p>
            </div>
            @if(auth()->user()->streak_count > 0)
                <div class="flex-shrink-0 glass-card px-5 py-3 text-center animate-pulse-glow">
                    <div class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-300">🔥 {{ auth()->user()->streak_count }}</div>
                    <div class="text-[10px] text-white/40 font-bold uppercase tracking-wider mt-0.5">{{ __('messages.day_streak') }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $stats = [
                ['value' => $lessonsCompleted, 'label' => __('messages.lessons_completed'), 'sub' => __('messages.of') . ' ' . $totalLessons . ' ' . __('messages.available'), 'gradient' => 'from-violet-500 to-indigo-500', 'delay' => '1'],
                ['value' => $quizAttempts->count(), 'label' => __('messages.quizzes_attempted'), 'sub' => $quizAttempts->where('passed', true)->count() . ' ' . __('messages.passed'), 'gradient' => 'from-cyan-400 to-blue-500', 'delay' => '2'],
                ['value' => round($avgScore, 1) . '%', 'label' => __('messages.avg_score'), 'sub' => $avgScore >= 60 ? __('messages.good_performance') : __('messages.keep_practicing'), 'gradient' => 'from-emerald-400 to-teal-500', 'delay' => '3'],
                ['value' => $totalQuizzes, 'label' => __('messages.quizzes_available'), 'sub' => __('messages.take_quiz_now'), 'gradient' => 'from-pink-400 to-rose-500', 'delay' => '4'],
            ];
        @endphp
        @foreach($stats as $stat)
            <div class="glass-card p-4 animate-count-up stagger-{{ $stat['delay'] }}">
                <div class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r {{ $stat['gradient'] }}">{{ $stat['value'] }}</div>
                <div class="text-[10px] text-white/40 mt-1.5 font-semibold uppercase tracking-wider">{{ $stat['label'] }}</div>
                <div class="text-[10px] text-white/25 mt-0.5">{{ $stat['sub'] }}</div>
            </div>
        @endforeach
    </div>

    <!-- Notice Board & Live Classes Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Announcements -->
        <div class="glass-card overflow-hidden animate-fade-in stagger-5">
            <div class="px-5 py-3.5 border-b border-white/[0.06] flex items-center gap-2">
                <span class="text-sm">📢</span>
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.recent_announcements') }}</h3>
            </div>
            <div class="p-5">
                @if($announcements->isEmpty())
                    <p class="text-xs text-white/30">{{ __('messages.no_announcements') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($announcements as $announcement)
                            <div class="border-l-2 border-violet-500/40 pl-3">
                                <h4 class="text-xs font-semibold text-white/75">{{ $announcement->getLocalized('title') }}</h4>
                                <p class="text-[11px] text-white/35 mt-0.5 line-clamp-2">{{ $announcement->getLocalized('content') }}</p>
                                <span class="text-[9px] text-white/20 mt-1 block">{{ $announcement->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Live Classes -->
        <div class="glass-card overflow-hidden animate-fade-in stagger-6">
            <div class="px-5 py-3.5 border-b border-white/[0.06] flex items-center gap-2">
                <span class="animate-pulse text-sm">🔴</span>
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.upcoming_live_classes') }}</h3>
            </div>
            <div class="p-5">
                @if($upcomingClasses->isEmpty())
                    <p class="text-xs text-white/30">{{ __('messages.no_live_classes') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($upcomingClasses as $class)
                            <div class="glass rounded-xl p-3 flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-semibold text-white/75">{{ $class->getLocalized('title') }}</h4>
                                    <p class="text-[10px] text-white/35 mt-0.5">{{ $class->scheduled_at->format('D, M d \a\t h:i A') }}</p>
                                </div>
                                <a href="{{ $class->meeting_link }}" target="_blank" class="px-3 py-1.5 bg-violet-500/20 hover:bg-violet-500/30 text-violet-300 text-[10px] font-bold rounded-lg transition border border-violet-500/20">{{ __('messages.join') }}</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Mentor & Notifications -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="glass-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.assigned_mentor') }}</h3>
            </div>
            <div class="p-5">
                @if($student->assignedMentor)
                    <div class="space-y-1 mb-4">
                        <p class="text-sm font-semibold text-white/80">{{ $student->assignedMentor->name }}</p>
                        <p class="text-xs text-white/40">{{ $student->assignedMentor->subject_specialization ?? __('messages.general_mentor') }}</p>
                        <p class="text-xs text-white/35">{{ $student->assignedMentor->email }}</p>
                        <p class="text-xs text-white/30">{{ $student->assignedMentor->phone ?? __('messages.no_phone') }}</p>
                    </div>
                    <button @click="window.dispatchEvent(new CustomEvent('open-mentor-email'))" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-xl transition shadow-md shadow-violet-500/10">
                        ✉ Send Email
                    </button>
                @else
                    <p class="text-xs text-white/30">{{ __('messages.no_mentor_assigned') }}</p>
                @endif
            </div>
        </div>

        <div class="glass-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.notifications') }}</h3>
            </div>
            <div class="p-5">
                @if($student->portalNotifications->isEmpty())
                    <p class="text-xs text-white/30">{{ __('messages.no_notifications') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($student->portalNotifications as $notice)
                            <div class="glass rounded-xl p-3">
                                <p class="text-xs font-semibold text-white/75">{{ $notice->title }}</p>
                                <p class="text-[10px] text-white/35 mt-1">{{ $notice->message }}</p>
                                <p class="text-[9px] text-white/20 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="glass-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.weekly_activity') }}</h3>
            </div>
            <div class="p-5">
                <canvas id="weeklyChart" height="200"></canvas>
            </div>
        </div>
        <div class="glass-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-white/[0.06]">
                <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.subject_performance') }}</h3>
            </div>
            <div class="p-5">
                <canvas id="subjectChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Lessons -->
    <div class="glass-card overflow-hidden">
        <div class="px-5 py-3.5 border-b border-white/[0.06] flex items-center justify-between">
            <h3 class="font-semibold text-white/80 text-sm">{{ __('messages.recently_accessed') }}</h3>
            <a href="{{ route('student.lessons') }}" class="text-[10px] text-violet-400 hover:text-violet-300 font-semibold transition">{{ __('messages.view_all') }} →</a>
        </div>
        <div class="p-5">
            @if($recentLessons->isEmpty())
                <div class="text-center py-8">
                    <p class="text-white/30 text-xs">{{ __('messages.no_lessons_accessed') }}</p>
                    <a href="{{ route('student.lessons') }}" class="inline-flex items-center mt-3 px-4 py-2 bg-violet-500/15 hover:bg-violet-500/25 text-violet-300 text-xs font-semibold rounded-xl transition border border-violet-500/15">
                        {{ __('messages.browse_lessons') }}
                    </a>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($recentLessons as $report)
                        @if($report->lesson)
                        <a href="{{ route('student.lesson.show', $report->lesson_id) }}"
                           class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/[0.03] transition group">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-xs text-white/75 truncate group-hover:text-violet-300 transition">{{ $report->lesson->getLocalized('title') }}</p>
                                <p class="text-[10px] text-white/30">{{ $report->lesson->subject }} · {{ $report->last_accessed?->diffForHumans() }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if($report->is_completed)
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/15">{{ __('messages.done') }}</span>
                                @else
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-500/15 text-amber-400 border border-amber-500/15">{{ __('messages.in_progress') }}</span>
                                @endif
                            </div>
                        </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $quickActions = [
                ['route' => 'student.lessons', 'label' => __('messages.browse_lessons'), 'icon' => '📖', 'gradient' => 'from-violet-500/10 to-indigo-500/10', 'hoverBorder' => 'hover:border-violet-500/25'],
                ['route' => 'student.quizzes', 'label' => __('messages.take_quiz'), 'icon' => '⚡', 'gradient' => 'from-cyan-500/10 to-blue-500/10', 'hoverBorder' => 'hover:border-cyan-500/25'],
                ['route' => 'student.assignments.index', 'label' => __('messages.my_assignments'), 'icon' => '📝', 'gradient' => 'from-emerald-500/10 to-teal-500/10', 'hoverBorder' => 'hover:border-emerald-500/25'],
                ['route' => 'student.chatbot', 'label' => __('messages.ask_ai'), 'icon' => '🤖', 'gradient' => 'from-pink-500/10 to-rose-500/10', 'hoverBorder' => 'hover:border-pink-500/25'],
            ];
        @endphp
        @foreach($quickActions as $action)
            <a href="{{ route($action['route']) }}"
               class="glass-card bg-gradient-to-br {{ $action['gradient'] }} {{ $action['hoverBorder'] }} p-4 text-center group transition-all duration-300 hover:-translate-y-1">
                <div class="text-2xl mb-2 group-hover:scale-110 transition-transform">{{ $action['icon'] }}</div>
                <div class="text-[10px] font-bold text-white/60 group-hover:text-white/80 uppercase tracking-wider transition">{{ $action['label'] }}</div>
            </a>
        @endforeach
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        const weeklyData = @json($progressByWeek);
        const subjectData = @json($subjectScores);

        // Safe default config
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = 'rgba(255,255,255,0.45)';
            if (Chart.defaults.font) {
                Chart.defaults.font.family = "'Inter', sans-serif";
            }

            const weeklyCanvas = document.getElementById('weeklyChart');
            if (weeklyCanvas) {
                new Chart(weeklyCanvas, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(weeklyData),
                        datasets: [{
                            label: @json(__('messages.chart_lessons')),
                            data: Object.values(weeklyData),
                            backgroundColor: (ctx) => {
                                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                                g.addColorStop(0, 'rgba(139, 92, 246, 0.7)');
                                g.addColorStop(1, 'rgba(139, 92, 246, 0.15)');
                                return g;
                            },
                            borderRadius: 8,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { stepSize: 1, color: 'rgba(255,255,255,0.4)' }, 
                                grid: { color: 'rgba(255,255,255,0.04)' } 
                            },
                            x: { 
                                ticks: { color: 'rgba(255,255,255,0.4)' },
                                grid: { display: false } 
                            }
                        }
                    }
                });
            }

            const subjectCanvas = document.getElementById('subjectChart');
            if (subjectCanvas) {
                new Chart(subjectCanvas, {
                    type: 'radar',
                    data: {
                        labels: Object.keys(subjectData),
                        datasets: [{
                            label: @json(__('messages.score_percent')),
                            data: Object.values(subjectData),
                            backgroundColor: 'rgba(139, 92, 246, 0.12)',
                            borderColor: 'rgba(139, 92, 246, 0.6)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(139, 92, 246, 0.8)',
                            pointBorderColor: 'rgba(139, 92, 246, 0.3)',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: {
                                beginAtZero: true, 
                                max: 100, 
                                ticks: { stepSize: 20, color: 'rgba(255,255,255,0.2)', showLabelBackdrop: false },
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                angleLines: { color: 'rgba(255,255,255,0.05)' },
                                pointLabels: { color: 'rgba(255,255,255,0.5)', font: { size: 10 } }
                            }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        }
    } catch (e) {
        console.error("Error rendering charts: ", e);
    }
});
</script>
@endpush
@endsection
