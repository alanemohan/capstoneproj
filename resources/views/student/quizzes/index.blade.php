@extends('layouts.student')

@section('title', 'Quizzes - Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white/90 tracking-tight" style="font-family: var(--font-display);">✏️ {{ __('messages.quizzes') }}</h1>
            <p class="text-xs text-gray-500 dark:text-white/40 mt-1">{{ __('Test your knowledge, challenge yourself, and track your learning progress.') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-violet-500/10 text-violet-600 dark:text-violet-400 text-[10px] font-bold border border-violet-500/10">⚡ Challenge Arena</span>
        </div>
    </div>

    <!-- Quiz Statistics Row -->
    @php
        $student = auth()->user();
        $totalQuizzesCount = $quizzes->total();
        $completedAttemptsCount = $student->quizAttempts()->where('status', 'completed')->count();
        $averageQuizScore = $student->total_quiz_score;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Available Card -->
        <div class="glass-card p-4 border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#0b0f19] flex items-center gap-3.5 glow-violet">
            <div class="w-10 h-10 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center text-lg">📝</div>
            <div>
                <div class="text-base font-bold text-gray-900 dark:text-white/90 leading-none">{{ $totalQuizzesCount }}</div>
                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-1">Available Quizzes</div>
            </div>
        </div>
        <!-- Attempted Card -->
        <div class="glass-card p-4 border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#0b0f19] flex items-center gap-3.5 glow-emerald">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">⚡</div>
            <div>
                <div class="text-base font-bold text-gray-900 dark:text-white/90 leading-none">{{ $completedAttemptsCount }}</div>
                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-1">Completed Attempts</div>
            </div>
        </div>
        <!-- Score Card -->
        <div class="glass-card p-4 border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#0b0f19] flex items-center gap-3.5 glow-amber">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">🏆</div>
            <div>
                <div class="text-base font-bold text-gray-900 dark:text-white/90 leading-none">{{ $averageQuizScore }}%</div>
                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-1">Average Score</div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-4 border border-gray-200 dark:border-white/[0.06] bg-white dark:bg-[#0b0f19] glow-violet">
        <form method="GET" data-no-loading class="flex flex-wrap items-center gap-3 w-full">
            <!-- Custom Alpine.js Dropdown -->
            <div x-data="{ 
                    open: false, 
                    selected: @json(request('subject', '')),
                    selectedLabel: @json(request('subject') ? request('subject') : 'All Subjects'),
                    subjects: @json($subjects),
                    selectSubject(val) {
                        this.selected = val;
                        this.selectedLabel = val ? val : 'All Subjects';
                        this.open = false;
                        
                        // Set the hidden input and submit the form
                        const input = document.getElementById('hidden-subject-input');
                        if (input) {
                            input.value = val;
                            input.form.submit();
                        }
                    }
                 }" 
                 @click.away="open = false" 
                 class="relative min-w-[220px] flex-1 sm:flex-none"
            >
                <input type="hidden" id="hidden-subject-input" name="subject" value="{{ request('subject') }}">
                
                <!-- Trigger Button -->
                <button type="button" @click="open = !open" 
                        class="w-full flex items-center justify-between pl-4 pr-3.5 py-2.5 bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 dark:hover:border-violet-500/40 rounded-xl text-gray-800 dark:text-white/80 transition text-xs font-semibold shadow-sm focus:outline-none"
                >
                    <span x-text="selectedLabel"></span>
                    <svg class="w-4 h-4 text-gray-400 dark:text-white/30 transform transition-transform duration-200" :class="open ? 'rotate-180' : 'rotate-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Dropdown List Options -->
                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="absolute left-0 right-0 mt-2 z-50 bg-white dark:bg-[#0c0e1e] border border-gray-200 dark:border-white/[0.08] rounded-2xl shadow-2xl overflow-hidden py-1.5 max-h-56 overflow-y-auto scrollbar-thin scrollbar-thumb-violet-500/20"
                >
                    <!-- All Subjects Option -->
                    <button type="button" @click="selectSubject('')" 
                            class="w-full text-left px-4 py-2.5 text-xs font-medium text-gray-600 dark:text-white/60 hover:text-violet-600 dark:hover:text-violet-300 hover:bg-violet-500/5 dark:hover:bg-violet-500/10 transition-colors flex items-center justify-between"
                            :class="selected === '' ? 'bg-violet-500/10 text-violet-600 dark:text-violet-300 font-bold' : ''"
                    >
                        <span>{{ __('All Subjects') }}</span>
                        <span x-show="selected === ''" class="text-violet-500 text-xs">✓</span>
                    </button>
                    
                    <!-- Subject List Options -->
                    <template x-for="subj in subjects" :key="subj">
                        <button type="button" @click="selectSubject(subj)" 
                                class="w-full text-left px-4 py-2.5 text-xs font-medium text-gray-600 dark:text-white/60 hover:text-violet-600 dark:hover:text-violet-300 hover:bg-violet-500/5 dark:hover:bg-violet-500/10 transition-colors flex items-center justify-between"
                                :class="selected === subj ? 'bg-violet-500/10 text-violet-600 dark:text-violet-300 font-bold' : ''"
                        >
                            <span x-text="subj"></span>
                            <span x-show="selected === subj" class="text-violet-500 text-xs">✓</span>
                        </button>
                    </template>
                </div>
            </div>
            
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition duration-300 shadow-md shadow-violet-500/10 hover:shadow-violet-500/20">
                {{ __('Filter') }}
            </button>
            
            @if(request('subject'))
                <a href="{{ route('student.quizzes') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-white/[0.06] border border-gray-200 dark:border-white/[0.08] hover:bg-gray-200 dark:hover:bg-white/[0.1] text-gray-800 dark:text-white/80 text-xs font-bold uppercase tracking-wider rounded-xl transition duration-300 flex items-center justify-center">
                    {{ __('Clear') }}
                </a>
            @endif
        </form>
    </div>

    <!-- Quiz Grid -->
    @if($quizzes->isEmpty())
        <div class="glass-card p-12 text-center text-gray-400 dark:text-white/40 glow-violet flex flex-col items-center justify-center bg-white dark:bg-[#0b0f19]">
            <div class="w-16 h-16 rounded-full bg-violet-500/5 text-violet-400 flex items-center justify-center text-3xl mb-4 border border-violet-500/10">📋</div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white/80" style="font-family: var(--font-display);">No Quizzes Available</h3>
            <p class="text-xs text-gray-500 dark:text-white/30 mt-1">There are no quizzes created for your class at this time. Please check back later!</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($quizzes as $quiz)
                @php
                    $myAttempts = $quiz->attempts;
                    $bestAttempt = $myAttempts->where('status','completed')->sortByDesc('percentage')->first();
                    $canAttempt = $myAttempts->where('status','completed')->count() < $quiz->max_attempts;
                    $inProgress = $myAttempts->where('status','started')->first();
                @endphp
                <div class="glass-card p-6 border border-gray-200 dark:border-white/[0.06] hover:border-violet-500/30 transition-all duration-300 relative overflow-hidden flex flex-col justify-between group glow-violet bg-white dark:bg-[#0b0f19] h-[260px]">
                    <!-- Colored border accent based on performance/attempt status -->
                    <div class="absolute top-0 left-0 right-0 h-[3px] {{ $bestAttempt && $bestAttempt->passed ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : ($bestAttempt ? 'bg-gradient-to-r from-rose-500 to-red-500' : 'bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500') }}"></div>
                    
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between">
                            <span class="bg-violet-500/10 text-violet-600 dark:text-violet-300 border border-violet-500/10 text-[9px] px-2.5 py-0.5 rounded-md font-bold uppercase tracking-wider">
                                {{ $quiz->subject }}
                            </span>
                            @if($bestAttempt)
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/10">
                                    Best: {{ $bestAttempt->percentage }}%
                                </span>
                            @else
                                <span class="text-[10px] text-gray-400 dark:text-white/30 font-medium">Unattempted</span>
                            @endif
                        </div>

                        <h3 class="font-bold text-sm text-gray-900 dark:text-white/95 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition leading-snug line-clamp-1" style="font-family: var(--font-display);" title="{{ $quiz->title }}">
                            {{ $quiz->title }}
                        </h3>

                        <!-- Grid statistics -->
                        <div class="grid grid-cols-3 gap-2.5 text-center pt-1.5">
                            <div class="bg-gray-50 dark:bg-white/[0.02] border border-gray-150 dark:border-white/[0.04] rounded-xl p-2.5">
                                <div class="text-xs font-bold text-gray-800 dark:text-white/90">{{ $quiz->questions_count ?? '?' }}</div>
                                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-0.5">{{ __('Questions') }}</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-white/[0.02] border border-gray-150 dark:border-white/[0.04] rounded-xl p-2.5">
                                <div class="text-xs font-bold text-gray-800 dark:text-white/90">{{ $quiz->time_limit }}m</div>
                                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-0.5">{{ __('Limit') }}</div>
                            </div>
                            <div class="bg-gray-50 dark:bg-white/[0.02] border border-gray-150 dark:border-white/[0.04] rounded-xl p-2.5">
                                <div class="text-xs font-bold text-gray-800 dark:text-white/90">{{ $myAttempts->where('status','completed')->count() }}/{{ $quiz->max_attempts }}</div>
                                <div class="text-[9px] text-gray-400 dark:text-white/35 font-bold uppercase tracking-wider mt-0.5">{{ __('Attempts') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="pt-4 border-t border-gray-100 dark:border-white/[0.04]">
                        @if($inProgress)
                            <a href="{{ route('student.quiz.start', $quiz->id) }}"
                               class="block w-full text-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition duration-300 shadow-md shadow-amber-500/10">
                                ▶ {{ __('Resume') }}
                            </a>
                        @elseif($canAttempt)
                            <a href="{{ route('student.quiz.start', $quiz->id) }}"
                               class="block w-full text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-wider transition duration-300 shadow-md shadow-violet-500/10">
                                {{ $bestAttempt ? '🔄 ' . __('Retake') : '▶ ' . __('Start') }}
                            </a>
                        @else
                            <div class="w-full text-center bg-gray-100 dark:bg-white/[0.04] text-gray-400 dark:text-white/30 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-wider border border-gray-200 dark:border-white/[0.06]">
                                {{ __('Max attempts reached') }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $quizzes->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
