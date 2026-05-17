@extends('layouts.student')

@section('title', 'Quiz: ' . $quiz->title)

@section('student-content')
<div class="space-y-6 animate-fade-in" x-data="quizApp()" x-init="startTimer()">
    
    <!-- Quiz Header -->
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="glass-card p-5 glow-violet relative overflow-hidden">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <span class="text-[9px] bg-violet-500/15 text-violet-300 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $quiz->subject }}</span>
                    <h1 class="text-base font-bold text-white/95 mt-1.5" style="font-family: var(--font-display);">{{ $quiz->title }}</h1>
                    <p class="text-[10px] text-white/40 mt-0.5 uppercase tracking-wider font-semibold">{{ $quiz->class_level }}</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-extrabold font-mono transition-all duration-300"
                         :class="timeLeft <= 60 ? 'text-red-400 animate-pulse' : 'text-amber-400'"
                         x-text="formatTime(timeLeft)">
                        {{ sprintf('%02d:%02d', $quiz->time_limit, 0) }}
                    </div>
                    <div class="text-[9px] text-white/35 font-bold uppercase tracking-wider mt-0.5">Time Remaining</div>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div class="mt-4 bg-white/[0.06] rounded-full h-1.5 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-500 to-indigo-500 h-full rounded-full transition-all duration-300"
                     :style="`width: ${(currentQ + 1) / totalQ * 100}%`"></div>
            </div>
            <div class="flex justify-between text-[10px] text-white/40 font-bold mt-2 uppercase tracking-wider">
                <span>Question <span x-text="currentQ + 1" class="text-white/80"></span> of {{ $questions->count() }}</span>
                <span>{{ $quiz->total_marks }} total marks</span>
            </div>
        </div>

        <!-- Quiz Form -->
        <form id="quiz-form" method="POST" action="{{ route('student.quiz.submit', $quiz->id) }}">
            @csrf

            @foreach($questions as $index => $question)
                <div class="question-card glass-card p-6 mb-4"
                     data-question-id="{{ $question->id }}"
                     data-question-type="{{ $question->type ?? 'mcq' }}"
                     x-show="currentQ === {{ $index }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0">

                    <div class="flex items-start gap-3 mb-5">
                        <span class="flex-shrink-0 w-8 h-8 bg-violet-600/20 text-violet-300 border border-violet-500/25 rounded-lg flex items-center justify-center text-xs font-bold">{{ $index + 1 }}</span>
                        <div class="flex-1">
                            <p class="text-white/95 font-medium leading-relaxed text-sm" style="font-family: var(--font-display);">{{ $question->question_text }}</p>
                            @php $qType = $question->type ?? 'mcq'; @endphp
                            <span class="inline-block mt-2 text-[9px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider
                                {{ $qType === 'mcq' ? 'bg-blue-500/15 text-blue-300 border border-blue-500/20' : ($qType === 'true_false' ? 'bg-purple-500/15 text-purple-300 border border-purple-500/20' : 'bg-orange-500/15 text-orange-300 border border-orange-500/20') }}">
                                {{ $qType === 'mcq' ? 'Multiple Choice' : ($qType === 'true_false' ? 'True / False' : 'Text Answer') }}
                            </span>
                        </div>
                    </div>

                    @if($qType === 'mcq')
                        <div class="space-y-2.5">
                            @foreach($question->getOptionsForDisplay() as $key => $option)
                                <label class="flex items-center gap-3 p-3.5 border border-white/[0.08] rounded-xl cursor-pointer hover:border-violet-500/30 hover:bg-white/[0.02] transition-all has-[:checked]:border-violet-500/50 has-[:checked]:bg-violet-500/10 group">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}"
                                           class="quiz-radio w-4 h-4 text-violet-500 cursor-pointer accent-violet-500 bg-white/[0.04] border-white/[0.08]"
                                           onchange="trackAnswer({{ $question->id }})">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-lg border border-white/[0.1] group-has-[:checked]:border-violet-500/40 bg-white/[0.02] flex items-center justify-center text-[10px] font-bold text-white/40 group-has-[:checked]:text-violet-300">
                                        {{ strtoupper($key) }}
                                    </span>
                                    <span class="text-white/80 text-xs">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($qType === 'true_false')
                        <div class="space-y-2.5">
                            @foreach(['a' => 'True', 'b' => 'False'] as $key => $label)
                                <label class="flex items-center gap-3 p-3.5 border border-white/[0.08] rounded-xl cursor-pointer hover:border-violet-500/30 hover:bg-white/[0.02] transition-all has-[:checked]:border-violet-500/50 has-[:checked]:bg-violet-500/10 group">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}"
                                           class="quiz-radio w-4 h-4 text-violet-500 cursor-pointer accent-violet-500 bg-white/[0.04] border-white/[0.08]"
                                           onchange="trackAnswer({{ $question->id }})">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-lg border border-white/[0.1] group-has-[:checked]:border-violet-500/40 bg-white/[0.02] flex items-center justify-center text-[10px] font-bold text-white/40 group-has-[:checked]:text-violet-300">
                                        {{ strtoupper($key) }}
                                    </span>
                                    <span class="text-white/80 text-xs font-bold uppercase tracking-wider">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                    @else
                        {{-- Text Answer --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-white/40 uppercase tracking-wider">Your Answer:</label>
                            <input type="text"
                                   name="answers[{{ $question->id }}]"
                                   class="quiz-text w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 text-xs tracking-wider transition-all"
                                   placeholder="Type your answer here..."
                                   oninput="trackAnswer({{ $question->id }})">
                            <p class="text-[9px] text-white/30 font-semibold uppercase tracking-wider">Answer is checked case-insensitively.</p>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Navigation -->
            <div class="glass-card p-4 flex items-center justify-between gap-3">
                <button type="button" @click="prevQ()" x-show="currentQ > 0"
                        class="px-5 py-2.5 bg-white/[0.06] border border-white/[0.08] hover:bg-white/[0.1] text-white/80 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                    ← Previous
                </button>
                <div class="flex-1 text-center">
                    <span class="text-[10px] text-white/40 font-bold uppercase tracking-wider">
                        <span x-text="answeredCount" class="text-white/80"></span> of {{ $questions->count() }} answered
                    </span>
                </div>
                <button type="button" @click="nextQ()" x-show="currentQ < totalQ - 1"
                        class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md">
                    Next →
                </button>
                <button type="button" x-show="currentQ === totalQ - 1"
                        @click="confirmSubmit()"
                        id="quiz-submit-btn"
                        class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center gap-2 shadow-md">
                    <svg id="quiz-submit-spinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span id="quiz-submit-label">Submit Quiz</span>
                </button>
            </div>

            <!-- Question Navigator -->
            <div class="glass-card p-4 mt-4">
                <p class="text-[9px] font-bold text-white/40 mb-3 uppercase tracking-wider">Jump to question:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($questions as $i => $q)
                        <button type="button" @click="goTo({{ $i }})"
                                :class="currentQ === {{ $i }} ? 'bg-violet-600 text-white border border-violet-500/50' : (isAnswered({{ $q->id }}) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/20' : 'bg-white/[0.04] text-white/40 border border-white/[0.06]')"
                                class="w-9 h-9 rounded-lg text-xs font-bold transition hover:opacity-80">
                            {{ $i + 1 }}
                        </button>
                    @endforeach
                </div>
                <div class="flex flex-wrap items-center gap-4 mt-4 text-[9px] text-white/40 font-bold uppercase tracking-wider">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-violet-600 rounded-md inline-block"></span> Current</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-emerald-500/20 border border-emerald-500/20 rounded-md inline-block"></span> Answered</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-white/[0.04] border border-white/[0.06] rounded-md inline-block"></span> Unanswered</span>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Track answered questions by question ID
const answeredSet = new Set();

function trackAnswer(questionId) {
    answeredSet.add(questionId);
    // Notify Alpine to recompute
    window.dispatchEvent(new CustomEvent('answer-updated'));
}

function isQuestionAnswered(questionId) {
    // Check radio button
    const radio = document.querySelector(`input[name="answers[${questionId}]"][type="radio"]:checked`);
    if (radio) return true;
    // Check text input
    const text = document.querySelector(`input[name="answers[${questionId}]"][type="text"]`);
    if (text && text.value.trim() !== '') return true;
    return false;
}

function quizApp() {
    return {
        currentQ: 0,
        totalQ: {{ $questions->count() }},
        timeLeft: {{ $quiz->time_limit * 60 }},
        answeredCount: 0,

        startTimer() {
            this.updateAnsweredCount();
            window.addEventListener('answer-updated', () => this.updateAnsweredCount());

            const interval = setInterval(() => {
                this.timeLeft--;
                if (this.timeLeft <= 0) {
                    clearInterval(interval);
                    this.setSubmittingState();
                    document.getElementById('quiz-form').submit();
                }
            }, 1000);
        },

        setSubmittingState() {
            const button = document.getElementById('quiz-submit-btn');
            const spinner = document.getElementById('quiz-submit-spinner');
            const label = document.getElementById('quiz-submit-label');

            if (button) {
                button.disabled = true;
                button.classList.add('opacity-60', 'cursor-not-allowed');
            }
            if (spinner) spinner.classList.remove('hidden');
            if (label) label.textContent = 'Submitting...';
        },

        updateAnsweredCount() {
            const cards = document.querySelectorAll('.question-card');
            let count = 0;
            cards.forEach(card => {
                const qId = card.dataset.questionId;
                if (isQuestionAnswered(qId)) count++;
            });
            this.answeredCount = count;
        },

        formatTime(seconds) {
            const m = Math.floor(seconds / 60).toString().padStart(2, '0');
            const s = (seconds % 60).toString().padStart(2, '0');
            return `${m}:${s}`;
        },

        nextQ() { if (this.currentQ < this.totalQ - 1) this.currentQ++; },
        prevQ() { if (this.currentQ > 0) this.currentQ--; },
        goTo(i) { this.currentQ = i; },

        isAnswered(questionId) {
            return isQuestionAnswered(questionId);
        },

        confirmSubmit() {
            this.updateAnsweredCount();
            const unanswered = this.totalQ - this.answeredCount;

            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`)) return;
            }

            this.setSubmittingState();
            document.getElementById('quiz-form').submit();
        }
    }
}
</script>
@endpush
@endsection
