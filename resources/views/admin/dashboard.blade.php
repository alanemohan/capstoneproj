@extends('layouts.admin')

@section('title', __('messages.admin_panel') . ' — Nabha Learning')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">

    {{-- ── Hero Strip ── --}}
    <div class="bg-white rounded-xl p-6 border border-slate-200 relative overflow-hidden shadow-sm">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-500/[0.02] rounded-full blur-3xl -mr-32 -mt-32"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.admin_panel') }}</h1>
                <p class="text-slate-500 text-xs mt-1 font-semibold">{{ __('messages.platform_name') }} — {{ __('messages.system_overview') }}</p>
            </div>

            {{-- Period selector --}}
            <form method="GET" action="{{ route('admin.dashboard') }}" data-no-loading class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-lg p-1 shrink-0">
                @foreach([3 => '3M', 6 => '6M', 12 => '1Y'] as $val => $label)
                    <button type="submit" name="period" value="{{ $val }}"
                            class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider transition
                                   {{ $period === $val
                                      ? 'bg-orange-500 text-white shadow-sm'
                                      : 'text-slate-400 hover:text-slate-700' }}">
                        {{ __('messages.period_' . strtolower($label)) }}
                    </button>
                @endforeach
            </form>
        </div>

        {{-- Alert badges --}}
        <div class="flex flex-wrap gap-2 mt-4 relative z-10">
            @if($stats['pending_students'] > 0)
                <a href="{{ route('admin.students_manager.index') }}"
                   class="inline-flex items-center gap-1.5 bg-blue-50 border border-blue-150 text-blue-700 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md hover:bg-blue-100 transition">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                    {{ $stats['pending_students'] }} {{ __('messages.student') }}{{ $stats['pending_students'] !== 1 ? 's' : '' }} {{ __('messages.pending_approval') }}
                </a>
            @endif
            @if($stats['pending_teachers'] > 0)
                <a href="{{ route('admin.teachers_manager.index') }}"
                   class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-150 text-emerald-700 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md hover:bg-emerald-100 transition">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    {{ $stats['pending_teachers'] }} {{ __('messages.teacher') }}{{ $stats['pending_teachers'] !== 1 ? 's' : '' }} {{ __('messages.pending_approval') }}
                </a>
            @endif
            @if($stats['pending_lessons'] > 0)
                <a href="{{ route('admin.content') }}"
                   class="inline-flex items-center gap-1.5 bg-yellow-50 border border-yellow-150 text-yellow-750 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md hover:bg-yellow-100 transition">
                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full"></span>
                    {{ $stats['pending_lessons'] }} {{ __('messages.lessons') }} {{ __('messages.pending_review') }}
                </a>
            @endif
            @if($stats['pending_courses'] > 0)
                <a href="{{ route('admin.courses') }}"
                   class="inline-flex items-center gap-1.5 bg-orange-50 border border-orange-150 text-orange-755 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md hover:bg-orange-100 transition">
                    <span class="w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
                    {{ $stats['pending_courses'] }} {{ __('messages.courses') }} {{ __('messages.pending_review') }}
                </a>
            @endif
            @if(($stats['pending_quizzes'] ?? 0) > 0)
                <a href="{{ route('admin.quizzes', ['status' => 'pending']) }}"
                   class="inline-flex items-center gap-1.5 bg-amber-50 border border-amber-150 text-amber-750 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md hover:bg-amber-100 transition">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                    {{ $stats['pending_quizzes'] }} Quizzes {{ __('messages.pending_review') }}
                </a>
            @endif
            @if($stats['pending_students'] === 0 && $stats['pending_teachers'] === 0 && $stats['pending_lessons'] === 0 && $stats['pending_courses'] === 0)
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-150 text-emerald-700 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-md">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    {{ __('messages.all_approvals_up_to_date') }}
                </span>
            @endif
        </div>
    </div>

    {{-- ── KPI Stats Grid ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statCards = [
                ['value' => $stats['total_students'],    'label' => __('messages.student') . 's',       'color' => 'text-blue-600',   'accent' => 'bg-blue-500'],
                ['value' => $stats['total_teachers'],    'label' => __('messages.teacher') . 's',       'color' => 'text-emerald-600','accent' => 'bg-emerald-500'],
                ['value' => $stats['total_courses'],     'label' => __('messages.total_courses'),    'color' => 'text-slate-800', 'accent' => 'bg-slate-500'],
                ['value' => $stats['published_courses'], 'label' => __('messages.published'),        'color' => 'text-teal-600',   'accent' => 'bg-teal-500'],
                ['value' => $stats['total_lessons'],     'label' => __('messages.recent_lessons'),          'color' => 'text-purple-600', 'accent' => 'bg-purple-500'],
                ['value' => $stats['pending_lessons'],   'label' => __('messages.pending_review'),   'color' => 'text-yellow-600', 'accent' => 'bg-yellow-500'],
                ['value' => $stats['total_enrollments'], 'label' => __('messages.enrollments'),      'color' => 'text-pink-600',   'accent' => 'bg-pink-500'],
                ['value' => '₹' . number_format($stats['total_revenue'], 0),
                                                         'label' => __('messages.total_revenue'),    'color' => 'text-orange-600',  'accent' => 'bg-orange-500', 'raw' => true],
            ];
        @endphp
        @foreach($statCards as $i => $card)
            <div class="bg-white rounded-xl p-5 border border-slate-200 hover:border-orange-500/10 shadow-sm transition">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1.5 h-5 {{ $card['accent'] }} rounded-full shrink-0"></div>
                    <div class="text-xl font-extrabold {{ $card['color'] }} tracking-tight tabular-nums">
                        {{ isset($card['raw']) ? $card['value'] : number_format($card['value']) }}
                    </div>
                </div>
                <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Charts Row ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.weekly_activity') }}</h3>
            </div>
            <div class="relative" style="height:220px">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.subject_performance') }}</h3>
            </div>
            <div class="relative" style="height:220px">
                <canvas id="subjectStatsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.registrations') }}</h3>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ __('messages.last_months', ['months' => $period]) }}</span>
            </div>
            <div class="relative" style="height:180px">
                <canvas id="regChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.revenue') }} (₹)</h3>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ __('messages.last_months', ['months' => $period]) }}</span>
            </div>
            <div class="relative" style="height:180px">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.user_distribution') }}</h3>
                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">{{ __('messages.all_time') }}</span>
            </div>
            <div class="relative" style="height:180px">
                <canvas id="roleChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Analysis Row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 bg-blue-50 text-blue-500 rounded-md flex items-center justify-center text-xs">🎓</span>
                Student Analysis
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Active Students</span>
                    <span class="font-bold text-slate-800 tabular-nums">{{ number_format($stats['active_students']) }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-blue-500 h-1.5 rounded-full transition-all" style="width: {{ $stats['total_students'] > 0 ? ($stats['active_students'] / $stats['total_students'] * 100) : 0 }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Inactive</span>
                    <span class="font-bold text-slate-805 tabular-nums">{{ number_format($stats['inactive_students']) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 flex items-center gap-2">
                <span class="w-6 h-6 bg-emerald-50 text-emerald-500 rounded-md flex items-center justify-center text-xs">👨‍🏫</span>
                Teacher Analysis
            </h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Active Teachers</span>
                    <span class="font-bold text-slate-800 tabular-nums">{{ number_format($stats['active_teachers']) }}</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $stats['total_teachers'] > 0 ? ($stats['active_teachers'] / $stats['total_teachers'] * 100) : 0 }}%"></div>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-500 font-semibold">Inactive</span>
                    <span class="font-bold text-slate-805 tabular-nums">{{ number_format($stats['inactive_teachers']) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Data Tables Row ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.recent_users') }}</h3>
                <a href="{{ route('admin.users') }}" class="text-[9px] text-orange-600 hover:underline font-bold uppercase tracking-wider">{{ __('messages.view_all') }} →</a>
            </div>
            <div class="divide-y divide-slate-150">
                @foreach($recentUsers as $user)
                    <div class="px-5 py-3.5 flex items-center gap-3 hover:bg-slate-50/30 transition">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0 border border-slate-200" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate" style="font-family: var(--font-display);">{{ $user->name }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold truncate mt-0.5">{{ $user->email }}</p>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider whitespace-nowrap
                            {{ $user->role === 'admin'   ? 'bg-slate-50 border border-slate-200 text-slate-650'
                             : ($user->role === 'teacher' ? 'bg-emerald-50 border border-emerald-150 text-emerald-700'
                             : 'bg-blue-50 border border-blue-150 text-blue-700') }}">
                            {{ __('messages.' . $user->role . '_role') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.recent_courses') }}</h3>
                <a href="{{ route('admin.courses') }}" class="text-[9px] text-orange-600 hover:underline font-bold uppercase tracking-wider">{{ __('messages.manage') }} →</a>
            </div>
            <div class="divide-y divide-slate-150">
                @foreach($recentCourses as $course)
                    <div class="px-5 py-3.5 flex items-center gap-3 hover:bg-slate-50/30 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate" style="font-family: var(--font-display);">{{ $course->getLocalized('title') }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $course->teacher->name }} · {{ $course->enrollments_count }} enrolled</p>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider whitespace-nowrap
                            {{ $course->status === 'published' ? 'bg-emerald-50 border border-emerald-150 text-emerald-700'
                             : ($course->status === 'pending'   ? 'bg-yellow-50 border border-yellow-150 text-yellow-750'
                             : ($course->status === 'draft'     ? 'bg-slate-50 border border-slate-200 text-slate-600'
                             : 'bg-red-50 border border-red-150 text-red-700')) }}">
                            {{ __('messages.' . $course->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50/50">
                <h3 class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">{{ __('messages.recent_lessons') }}</h3>
                <a href="{{ route('admin.content') }}" class="text-[9px] text-orange-600 hover:underline font-bold uppercase tracking-wider">{{ __('messages.review_all') }} →</a>
            </div>
            <div class="divide-y divide-slate-150">
                @foreach($recentLessons as $lesson)
                    <div class="px-5 py-3.5 flex items-center gap-3 hover:bg-slate-50/30 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate" style="font-family: var(--font-display);">{{ $lesson->getLocalized('title') }}</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">by {{ $lesson->teacher->name }}</p>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider whitespace-nowrap
                            {{ $lesson->status === 'published' ? 'bg-emerald-50 border border-emerald-150 text-emerald-700'
                             : ($lesson->status === 'pending'  ? 'bg-yellow-50 border border-yellow-150 text-yellow-750'
                             : 'bg-red-50 border border-red-150 text-red-700') }}">
                            {{ __('messages.' . $lesson->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    try {
        const regData     = @json($monthlyRegistrations);
        const revenueData = @json($monthlyRevenue);
        const roleData    = @json($roleDistribution);
        const weeklyActivity = @json($weeklyActivity);
        const subjectStats = @json($subjectStats);

        const initCharts = () => {
            if (typeof Chart === 'undefined') {
                setTimeout(initCharts, 100);
                return;
            }

            /* ── Weekly Activity Multi-Bar Chart ── */
            const weeklyCtx = document.getElementById('weeklyActivityChart');
            if (weeklyCtx) {
                new Chart(weeklyCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(weeklyActivity),
                        datasets: [
                            {
                                label: 'Enrollments',
                                data: Object.values(weeklyActivity).map(d => d.enrollments),
                                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Attempts',
                                data: Object.values(weeklyActivity).map(d => d.attempts),
                                backgroundColor: 'rgba(139, 92, 246, 0.8)',
                                borderRadius: 4,
                            },
                            {
                                label: 'Completions',
                                data: Object.values(weeklyActivity).map(d => d.completions),
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15, font: { size: 9, weight: 'bold' } } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            /* ── Subject Performance Radar Chart ── */
            const subjectCtx = document.getElementById('subjectStatsChart');
            if (subjectCtx) {
                new Chart(subjectCtx, {
                    type: 'radar',
                    data: {
                        labels: Object.keys(subjectStats),
                        datasets: [{
                            label: 'Avg Score %',
                            data: Object.values(subjectStats),
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderColor: 'rgba(249, 115, 22, 0.8)',
                            borderWidth: 2,
                            pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            r: { beginAtZero: true, max: 100, ticks: { display: false } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }

            /* ── Shared chart defaults ── */
            Chart.defaults.font.family = 'ui-sans-serif, system-ui, sans-serif';
            Chart.defaults.font.size   = 10;
            Chart.defaults.color       = '#94a3b8';

            const gridColor  = '#f1f5f9';
            const baseOpts   = (yLabel) => ({
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#e2e8f0',
                        padding: 8,
                        cornerRadius: 6,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { precision: 0 },
                        title: yLabel ? { display: false } : undefined,
                    },
                    x: { grid: { display: false } },
                },
            });

            /* ── Registrations bar chart ── */
            const regCtx = document.getElementById('regChart');
            if (regCtx) {
                new Chart(regCtx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(regData),
                        datasets: [{
                            label: @json(__('messages.chart_registrations')),
                            data: Object.values(regData),
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            hoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                            borderRadius: 4,
                            borderSkipped: false,
                        }],
                    },
                    options: baseOpts(),
                });
            }

            /* ── Revenue area chart ── */
            const revCtx = document.getElementById('revenueChart');
            if (revCtx) {
                new Chart(revCtx, {
                    type: 'line',
                    data: {
                        labels: Object.keys(revenueData),
                        datasets: [{
                            label: @json(__('messages.revenue')) + ' (₹)',
                            data: Object.values(revenueData),
                            borderColor: 'rgba(249, 115, 22, 1)',
                            backgroundColor: (ctx) => {
                                const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, ctx.chart.height);
                                g.addColorStop(0,   'rgba(249,115,22,.15)');
                                g.addColorStop(1,   'rgba(249,115,22,.01)');
                                return g;
                            },
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 1.5,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        }],
                    },
                    options: baseOpts(),
                });
            }

            /* ── Role donut ── */
            const roleCtx = document.getElementById('roleChart');
            if (roleCtx) {
                new Chart(roleCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(roleData),
                        datasets: [{
                            data: Object.values(roleData),
                            backgroundColor: [
                                'rgba(59,  130, 246, .8)',
                                'rgba(16,  185, 129, .8)',
                                'rgba(148, 163, 184, .8)',
                            ],
                            hoverBackgroundColor: [
                                'rgba(59,  130, 246, 1)',
                                'rgba(16,  185, 129, 1)',
                                'rgba(148, 163, 184, 1)',
                            ],
                            borderWidth: 2,
                            borderColor: '#fff',
                            hoverBorderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 8, padding: 10, usePointStyle: true, font: { size: 9, weight: 'bold' } },
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                titleColor: '#f8fafc',
                                bodyColor: '#e2e8f0',
                                padding: 8,
                                cornerRadius: 6,
                            },
                        },
                    },
                });
            }
        };

        initCharts();
    } catch (e) {
        console.error("Chart loading failed:", e);
    }
});
</script>
@endpush
@endsection
