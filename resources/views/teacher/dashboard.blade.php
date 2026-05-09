@extends('layouts.teacher')

@section('title', __('messages.teacher_portal') . ' - Nabha Learning')

@section('teacher-content')
<div class="space-y-6">
    <div class="relative bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-6 text-white overflow-hidden shadow-lg transition-transform hover:scale-[1.01] duration-300">
        <!-- Glass effect overlays -->
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-teal-400/20 rounded-full blur-xl"></div>
        
        <div class="relative z-10">
            <h1 class="text-3xl font-extrabold tracking-tight">{{ __('messages.welcome') }}, {{ auth()->user()->name }}!</h1>
            <p class="text-emerald-100 mt-2 font-medium">{{ auth()->user()->subject_specialization ?? __('messages.teacher') }} | {{ auth()->user()->school }}</p>
            <p class="text-sm mt-3 text-emerald-50">{{ __('messages.thank_you_empowering') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Glassmorphism Cards -->
        <div class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-500 to-teal-600">{{ $lessonsCount }}</div>
            <div class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wide">{{ __('messages.lessons_created') }}</div>
            <a href="{{ route('teacher.lessons') }}" class="inline-flex items-center text-xs font-bold text-emerald-600 hover:text-emerald-700 mt-3 group">
                {{ __('messages.manage') }} <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>
        <div class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-indigo-600">{{ $quizzesCount }}</div>
            <div class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wide">{{ __('messages.quizzes_created') }}</div>
            <a href="{{ route('teacher.quizzes') }}" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-700 mt-3 group">
                {{ __('messages.manage') }} <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>
        <div class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:-translate-y-1 transition-transform duration-300">
            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-500 to-pink-600">{{ $studentsReached }}</div>
            <div class="text-sm font-semibold text-gray-500 mt-2 uppercase tracking-wide">{{ __('messages.students_reached') }}</div>
            <a href="{{ route('teacher.analytics') }}" class="inline-flex items-center text-xs font-bold text-purple-600 hover:text-purple-700 mt-3 group">
                {{ __('messages.view_analytics') }} <span class="ml-1 transition-transform group-hover:translate-x-1">→</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.quick_actions') }}</h3>
            <div class="space-y-3">
                <a href="{{ route('teacher.lessons.create') }}" class="flex items-center gap-3 p-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-emerald-800">{{ __('messages.upload_new_lesson') }}</p>
                        <p class="text-xs text-gray-500">{{ __('messages.share_pdf_video') }}</p>
                    </div>
                    <span class="ml-auto text-emerald-500 group-hover:translate-x-1 transition">→</span>
                </a>
                <a href="{{ route('teacher.quizzes.create') }}" class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-blue-800">{{ __('messages.create_new_quiz') }}</p>
                        <p class="text-xs text-gray-500">{{ __('messages.test_understanding') }}</p>
                    </div>
                    <span class="ml-auto text-blue-500 group-hover:translate-x-1 transition">→</span>
                </a>
                <a href="{{ route('teacher.analytics') }}" class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition group">
                    <div>
                        <p class="font-medium text-sm text-purple-800">{{ __('messages.view_analytics') }}</p>
                        <p class="text-xs text-gray-500">{{ __('messages.track_performance') }}</p>
                    </div>
                    <span class="ml-auto text-purple-500 group-hover:translate-x-1 transition">→</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.tips') }}</h3>
            <div class="space-y-3 text-sm text-gray-600">
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>{{ __('messages.tip_pdf') }}</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>{{ __('messages.tip_questions') }}</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>{{ __('messages.tip_approval') }}</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>{{ __('messages.tip_explanations') }}</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-500 mt-0.5 flex-shrink-0">✓</span>
                    <p>{{ __('messages.tip_analytics') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Chart Section -->
    <div class="bg-white/70 backdrop-blur-md rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">{{ __('messages.engagement') }}</h3>
        <div class="relative h-64 w-full">
            <canvas id="teacherChart"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('teacherChart').getContext('2d');
    
    // Gradient fill
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
            datasets: [{
                label: '{{ __('messages.active_students') }}',
                data: [12, 19, 25, 22, 30, Math.max(35, {{ $studentsReached ?? 0 }})],
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1f2937',
                    bodyColor: '#4b5563',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    boxPadding: 6,
                    usePointStyle: true,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f3f4f6', drawBorder: false }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
</script>
@endpush
@endsection
