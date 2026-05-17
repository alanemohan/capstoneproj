@extends('layouts.teacher')

@section('title', 'Student Progress - Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">

    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Student Progress</h1>
            <p class="text-xs text-gray-500 mt-1">Detailed performance tracking of students who accessed your lessons.</p>
        </div>
        @if(!$students->isEmpty())
            <a href="{{ route('teacher.student.progress.export') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition-all shadow-sm">
                Export CSV
            </a>
        @endif
    </div>

    @if($students->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
            <p class="text-sm font-semibold text-gray-800">No students have accessed your lessons yet.</p>
            <p class="text-xs text-gray-400 mt-1">Students who view or complete your lessons will appear here.</p>
        </div>
    @else

        {{-- Summary cards --}}
        @php
            $totalStudents   = $students->count();
            $avgClassScore   = $students->avg('avg_score');
            $avgProgress     = $students->avg('progress_pct');
            $fullyCompleted  = $students->where('progress_pct', 100)->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm text-center transition hover:border-emerald-500/25">
                <div class="text-2xl font-extrabold text-emerald-600 tabular-nums">{{ $totalStudents }}</div>
                <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Students Reached</div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm text-center transition hover:border-emerald-500/25">
                <div class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ round($avgClassScore, 1) }}%</div>
                <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Avg Quiz Score</div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm text-center transition hover:border-emerald-500/25">
                <div class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ round($avgProgress, 1) }}%</div>
                <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Avg Lesson Progress</div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm text-center transition hover:border-emerald-500/25">
                <div class="text-2xl font-extrabold text-amber-600 tabular-nums">{{ $fullyCompleted }}</div>
                <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Fully Completed</div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4" style="font-family: var(--font-display);">Quiz Score by Student</h2>
                <div class="relative h-[250px] w-full">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4" style="font-family: var(--font-display);">Lesson Completion Progress</h2>
                <div class="relative h-[250px] w-full">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Student Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider" style="font-family: var(--font-display);">Student Details</h2>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $totalLessons }} lesson(s) total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3 text-left">#</th>
                            <th class="px-5 py-3 text-left">Student</th>
                            <th class="px-5 py-3 text-left">Class</th>
                            <th class="px-5 py-3 text-center">Lessons Done</th>
                            <th class="px-5 py-3 text-center">Quizzes</th>
                            <th class="px-5 py-3 text-center">Avg Score</th>
                            <th class="px-5 py-3 text-right">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @foreach($students as $i => $student)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3.5 text-gray-400 font-semibold">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-md bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 font-extrabold text-[10px]">
                                            {{ strtoupper(substr($student['name'], 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-gray-900 leading-snug">{{ $student['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-600 font-semibold">{{ $student['class_level'] }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-900">
                                    <span class="font-extrabold">{{ $student['completed_lessons'] }}</span>
                                    <span class="text-gray-400">/ {{ $student['total_lessons'] }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-center text-gray-600 font-semibold">{{ $student['quizzes_taken'] }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @php $score = $student['avg_score']; @endphp
                                    <span class="font-extrabold
                                        {{ $score >= 75 ? 'text-emerald-600' : ($score >= 50 ? 'text-yellow-600' : 'text-red-500') }}">
                                        {{ $score > 0 ? $score . '%' : '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-2.5">
                                        <div class="w-20 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-1.5 rounded-full transition-all
                                                {{ $student['progress_pct'] >= 75 ? 'bg-emerald-500'
                                                 : ($student['progress_pct'] >= 40 ? 'bg-yellow-400'
                                                 : 'bg-red-400') }}"
                                                 style="width: {{ $student['progress_pct'] }}%">
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-gray-500 w-8 text-right font-bold tabular-nums">{{ $student['progress_pct'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = '#9ca3af';
            if (Chart.defaults.font) {
                Chart.defaults.font.family = "'Inter', sans-serif";
            }

            const labels   = @json($chartLabels);
            const scores   = @json($chartScores);
            const progress = @json($chartProgress);

            const scoreCanvas = document.getElementById('scoreChart');
            if (scoreCanvas) {
                new Chart(scoreCanvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Avg Quiz Score (%)',
                            data: scores,
                            backgroundColor: 'rgba(99, 102, 241, 0.85)',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                max: 100, 
                                ticks: { color: '#9ca3af' },
                                grid: { color: '#f3f4f6' } 
                            },
                            x: { 
                                ticks: { color: '#9ca3af' },
                                grid: { display: false } 
                            }
                        }
                    }
                });
            }

            const progCanvas = document.getElementById('progressChart');
            if (progCanvas) {
                new Chart(progCanvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Lesson Completion (%)',
                            data: progress,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                max: 100, 
                                ticks: { color: '#9ca3af' },
                                grid: { color: '#f3f4f6' } 
                            },
                            x: { 
                                ticks: { color: '#9ca3af' },
                                grid: { display: false } 
                            }
                        }
                    }
                });
            }
        }
    } catch (e) {
        console.error("Error rendering progress charts: ", e);
    }
});
</script>
@endpush
@endsection
