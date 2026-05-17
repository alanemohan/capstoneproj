@extends('layouts.teacher')

@section('title', __('messages.teacher_portal') . ' — Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <!-- Welcome Header — minimal clean -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
        <div class="flex items-start justify-between relative z-10">
            <div>
                <h1 class="text-xl font-extrabold text-gray-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.welcome') }}, {{ auth()->user()->name }}</h1>
                <p class="text-xs text-emerald-600 font-bold mt-1 uppercase tracking-wide">{{ auth()->user()->subject_specialization ?? __('messages.teacher') }} · {{ auth()->user()->school }}</p>
                <p class="text-xs mt-3 text-gray-400 font-semibold">{{ __('messages.thank_you_empowering') }}</p>
            </div>
            <div class="hidden sm:block text-right">
                <p class="text-[9px] text-gray-400 uppercase tracking-widest font-extrabold">Today</p>
                <p class="text-xs text-gray-700 font-bold mt-1 bg-gray-50 border border-gray-150 rounded-lg px-3 py-1.5">{{ now()->format('l, M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Row — dense and tight -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-emerald-500/20 transition-all duration-300 animate-count-up stagger-1">
            <div class="text-3xl font-black text-gray-900 tabular-nums">{{ $lessonsCount }}</div>
            <div class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-wider">{{ __('messages.lessons_created') }}</div>
            <a href="{{ route('teacher.lessons') }}" class="inline-flex items-center text-[10px] font-bold text-emerald-600 hover:text-emerald-700 mt-3.5 group uppercase tracking-wider">
                {{ __('messages.manage') }} <span class="ml-1 transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-emerald-500/20 transition-all duration-300 animate-count-up stagger-2">
            <div class="text-3xl font-black text-gray-900 tabular-nums">{{ $quizzesCount }}</div>
            <div class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-wider">{{ __('messages.quizzes_created') }}</div>
            <a href="{{ route('teacher.quizzes') }}" class="inline-flex items-center text-[10px] font-bold text-emerald-600 hover:text-emerald-700 mt-3.5 group uppercase tracking-wider">
                {{ __('messages.manage') }} <span class="ml-1 transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:border-emerald-500/20 transition-all duration-300 animate-count-up stagger-3">
            <div class="text-3xl font-black text-gray-900 tabular-nums">{{ $studentsReached }}</div>
            <div class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-wider">{{ __('messages.students_reached') }}</div>
            <a href="{{ route('teacher.analytics') }}" class="inline-flex items-center text-[10px] font-bold text-emerald-600 hover:text-emerald-700 mt-3.5 group uppercase tracking-wider">
                {{ __('messages.view_analytics') }} <span class="ml-1 transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
    </div>

    <!-- Two Column: Actions + Tips -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
            <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">{{ __('messages.quick_actions') }}</h3>
            <div class="space-y-2">
                <a href="{{ route('teacher.lessons.create') }}" class="flex items-center gap-4.5 p-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition-all duration-200 group">
                    <div class="w-8.5 h-8.5 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs text-gray-800">{{ __('messages.upload_new_lesson') }}</p>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ __('messages.share_pdf_video') }}</p>
                    </div>
                    <span class="text-gray-300 group-hover:text-emerald-600 transition-all text-xs font-bold mr-1">→</span>
                </a>
                <a href="{{ route('teacher.quizzes.create') }}" class="flex items-center gap-4.5 p-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition-all duration-200 group">
                    <div class="w-8.5 h-8.5 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs text-gray-800">{{ __('messages.create_new_quiz') }}</p>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ __('messages.test_understanding') }}</p>
                    </div>
                    <span class="text-gray-300 group-hover:text-emerald-600 transition-all text-xs font-bold mr-1">→</span>
                </a>
                <a href="{{ route('teacher.analytics') }}" class="flex items-center gap-4.5 p-3 rounded-lg border border-gray-100 hover:border-gray-200 hover:bg-gray-50/50 transition-all duration-200 group">
                    <div class="w-8.5 h-8.5 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-xs text-gray-800">{{ __('messages.view_analytics') }}</p>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ __('messages.track_performance') }}</p>
                    </div>
                    <span class="text-gray-300 group-hover:text-emerald-600 transition-all text-xs font-bold mr-1">→</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">{{ __('messages.tips') }}</h3>
                <div class="space-y-3.5 text-xs text-gray-500 font-medium leading-relaxed">
                    @php
                        $tips = [
                            __('messages.tip_pdf'),
                            __('messages.tip_questions'),
                            __('messages.tip_approval'),
                            __('messages.tip_explanations'),
                        ];
                    @endphp
                    @foreach($tips as $tip)
                        <div class="flex items-start gap-2.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></span>
                            <p class="flex-1">{{ $tip }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider" style="font-family: var(--font-display);">{{ __('messages.engagement') }}</h3>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Last 6 weeks</span>
        </div>
        <div class="relative h-60 w-full">
            <canvas id="teacherChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        if (typeof Chart !== 'undefined') {
            const ctx = document.getElementById('teacherChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            Chart.defaults.color = '#9ca3af';
            if (Chart.defaults.font) {
                Chart.defaults.font.family = "'Inter', sans-serif";
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                    datasets: [{
                        label: '{{ __('messages.active_students') }}',
                        data: [12, 19, 25, 22, 30, Math.max(35, {{ $studentsReached ?? 0 }})],
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#f9fafb',
                            bodyColor: '#d1d5db',
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 11 },
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { font: { size: 10 }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 }, color: '#9ca3af' }
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error loading teacher dashboard chart: ", e);
    }
});
</script>
@endpush
@endsection
