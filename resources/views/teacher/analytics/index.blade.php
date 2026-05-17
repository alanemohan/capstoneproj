@extends('layouts.teacher')

@section('title', 'Analytics - Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Analytics Dashboard</h1>
        <p class="text-xs text-gray-500 mt-1">Track student engagement and academic performance across your classes.</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm transition hover:border-emerald-500/30">
            <div class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ number_format($totalViews) }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Total Lesson Views</div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm transition hover:border-emerald-500/30">
            <div class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ number_format($totalDownloads) }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Downloads</div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm transition hover:border-emerald-500/30">
            <div class="text-2xl font-extrabold text-gray-900 tabular-nums">{{ number_format($totalAttempts) }}</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Quiz Attempts</div>
        </div>
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm transition hover:border-emerald-500/30">
            <div class="text-2xl font-extrabold text-emerald-600 tabular-nums">{{ round($avgQuizScore, 1) }}%</div>
            <div class="text-[10px] font-bold text-gray-400 mt-1.5 uppercase tracking-wider">Avg. Quiz Score</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4" style="font-family: var(--font-display);">Weekly Quiz Attempts</h3>
            <div class="relative h-[220px]">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider mb-4" style="font-family: var(--font-display);">Quiz Performance</h3>
            @if($quizPerformance->isEmpty())
                <div class="flex items-center justify-center h-[220px] text-gray-400 text-xs">No quiz data yet</div>
            @else
                <div class="relative h-[220px]">
                    <canvas id="quizChart"></canvas>
                </div>
            @endif
        </div>
    </div>

    <!-- Lesson Engagement Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider" style="font-family: var(--font-display);">Lesson Engagement</h3>
            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Top Performing</span>
        </div>
        @if($lessonEngagement->isEmpty())
            <div class="p-8 text-center text-gray-400 text-xs leading-relaxed">No lesson data yet. Upload lessons to see engagement.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3 text-left">Lesson</th>
                            <th class="px-5 py-3 text-left">Subject</th>
                            <th class="px-5 py-3 text-center">Views</th>
                            <th class="px-5 py-3 text-center">Downloads</th>
                            <th class="px-5 py-3 text-center">Students</th>
                            <th class="px-5 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @foreach($lessonEngagement as $lesson)
                            <tr class="hover:bg-gray-50/50 transition duration-150">
                                <td class="px-5 py-3.5 font-bold text-gray-900 leading-relaxed">{{ Str::limit($lesson->title, 40) }}</td>
                                <td class="px-5 py-3.5 text-gray-600 font-semibold">{{ $lesson->subject }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-900 font-extrabold">{{ $lesson->view_count }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $lesson->download_count }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $lesson->student_views }}</td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-bold border uppercase tracking-wider
                                        {{ $lesson->status === 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : 'bg-amber-50 text-amber-700 border-amber-200/50' }}">
                                        {{ $lesson->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        if (typeof Chart !== 'undefined') {
            // Theme settings for light minimal dashboard
            Chart.defaults.color = '#9ca3af';
            if (Chart.defaults.font) {
                Chart.defaults.font.family = "'Inter', sans-serif";
            }

            const weeklyData = @json($weeklyAttempts);
            const weeklyCanvas = document.getElementById('weeklyChart');
            if (weeklyCanvas) {
                const ctx = weeklyCanvas.getContext('2d');
                const g = ctx.createLinearGradient(0, 0, 0, 200);
                g.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
                g.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(weeklyCanvas, {
                    type: 'line',
                    data: {
                        labels: Object.keys(weeklyData),
                        datasets: [{
                            label: 'Quiz Attempts',
                            data: Object.values(weeklyData),
                            borderColor: '#10b981',
                            backgroundColor: g,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                ticks: { stepSize: 1, color: '#9ca3af' }, 
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

            @if($quizPerformance->isNotEmpty())
            const quizCanvas = document.getElementById('quizChart');
            if (quizCanvas) {
                const quizData = @json($quizPerformance->values());
                new Chart(quizCanvas, {
                    type: 'bar',
                    data: {
                        labels: quizData.map(q => q.title.substring(0, 15) + (q.title.length > 15 ? '...' : '')),
                        datasets: [
                            {
                                label: 'Avg Score %',
                                data: quizData.map(q => q.avg_score),
                                backgroundColor: 'rgba(99, 102, 241, 0.85)',
                                borderRadius: 6,
                            },
                            {
                                label: 'Pass Rate %',
                                data: quizData.map(q => q.pass_rate),
                                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 12, font: { size: 10 }, color: '#4b5563' }
                            }
                        }
                    }
                });
            }
            @endif
        }
    } catch (e) {
        console.error("Error rendering teacher charts: ", e);
    }
});
</script>
@endpush
@endsection
