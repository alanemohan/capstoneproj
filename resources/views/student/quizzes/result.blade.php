@extends('layouts.student')

@section('title', 'Quiz Result - Nabha Learning')

@section('student-content')
<div class="max-w-3xl mx-auto space-y-6 animate-fade-in">
    <!-- Result Header -->
    <div class="glass-card p-8 text-center glow-violet relative overflow-hidden">
        <div class="text-5xl mb-3">
            {{ $attempt->percentage >= 90 ? '🏆' : ($attempt->percentage >= 60 ? '🎉' : ($attempt->passed ? '✅' : '📚')) }}
        </div>
        <h1 class="text-xl font-bold text-white/95 mb-1" style="font-family: var(--font-display);">
            {{ $attempt->passed ? 'Well Done!' : 'Keep Practicing!' }}
        </h1>
        <p class="text-xs text-white/40 font-medium">{{ $attempt->quiz->title }}</p>

        <!-- Score Circle -->
        <div class="mt-6 flex justify-center">
            <div class="relative w-36 h-36">
                <svg class="w-36 h-36 -rotate-90" viewBox="0 0 160 160">
                    <circle cx="80" cy="80" r="70" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="10"/>
                    <circle cx="80" cy="80" r="70" fill="none"
                            stroke="{{ $attempt->passed ? '#10b981' : '#f43f5e' }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ 440 }}"
                            stroke-dashoffset="{{ 440 - (440 * $attempt->percentage / 100) }}"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-extrabold text-white/90">{{ $attempt->percentage }}%</span>
                    <span class="text-[10px] text-white/40 mt-0.5 font-bold">{{ $attempt->score }}/{{ $attempt->total_marks }}</span>
                    <span class="text-sm font-bold uppercase tracking-wider mt-1 {{ $attempt->passed ? 'text-emerald-400' : 'text-red-400' }}">{{ $attempt->grade }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-white/[0.06]">
            <div>
                <div class="text-base font-extrabold text-white/90">{{ $attempt->score }}</div>
                <div class="text-[9px] text-white/35 font-bold uppercase tracking-wider mt-0.5">Marks Earned</div>
            </div>
            <div>
                <div class="text-base font-extrabold text-white/90">{{ $attempt->time_taken_formatted }}</div>
                <div class="text-[9px] text-white/35 font-bold uppercase tracking-wider mt-0.5">Time Taken</div>
            </div>
            <div>
                <div class="text-base font-extrabold {{ $attempt->passed ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $attempt->passed ? 'PASS' : 'FAIL' }}
                </div>
                <div class="text-[9px] text-white/35 font-bold uppercase tracking-wider mt-0.5">Result (Pass ≥ {{ $attempt->quiz->passing_marks }}%)</div>
            </div>
        </div>
    </div>

    <!-- Question Review -->
    <div class="glass-card p-6">
        <h2 class="text-xs font-bold text-white/90 mb-5 uppercase tracking-wider" style="font-family: var(--font-display);">Detailed Review</h2>
        <div class="space-y-4">
            @foreach($attempt->quiz->questions as $index => $question)
                @php
                    $answerData = $attempt->answers[$question->id] ?? null;
                    $isCorrect = $answerData['is_correct'] ?? false;
                    $givenAnswer = $answerData['given'] ?? null;
                @endphp
                <div class="border rounded-xl p-4 transition-all {{ $isCorrect ? 'border-emerald-500/20 bg-emerald-500/5' : 'border-red-500/20 bg-red-500/5' }}">
                    <div class="flex items-start gap-3">
                        <span class="{{ $isCorrect ? 'text-emerald-400' : 'text-red-400' }} text-base flex-shrink-0 font-bold">
                            {{ $isCorrect ? '✓' : '✗' }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-white/90 text-xs leading-relaxed" style="font-family: var(--font-display);">Q{{ $index + 1 }}. {{ $question->question_text }}</p>
                            <div class="mt-2 text-xs space-y-1">
                                @if($givenAnswer && !$isCorrect)
                                    <p class="text-red-400 font-semibold">Your answer: <strong class="text-white/80">{{ strtoupper($givenAnswer) }}) {{ $question->getOptionAttribute($givenAnswer) }}</strong></p>
                                @elseif(!$givenAnswer)
                                    <p class="text-white/30 italic">Not answered</p>
                                @endif
                                <p class="text-emerald-400 font-semibold">Correct: <strong class="text-white/80">{{ strtoupper($question->correct_answer) }}) {{ $question->correct_answer_text }}</strong></p>
                                @if($question->explanation)
                                    <p class="text-white/60 text-[10px] mt-2.5 bg-black/20 p-2.5 rounded-lg border border-white/[0.04] leading-relaxed">💡 {{ $question->explanation }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $isCorrect ? 'text-emerald-400' : 'text-white/35' }}">
                            +{{ $isCorrect ? $question->marks : 0 }}/{{ $question->marks }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('student.quizzes') }}" class="flex-1 text-center bg-white/[0.06] border border-white/[0.08] hover:bg-white/[0.1] text-white/90 py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center">
            ← Back to Quizzes
        </a>
        @if($attempt->quiz->canAttempt(auth()->id()))
            <a href="{{ route('student.quiz.start', $attempt->quiz_id) }}" class="flex-1 text-center border border-violet-500/30 text-violet-300 py-3 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-violet-500/10 transition-all flex items-center justify-center">
                🔄 Retake Quiz
            </a>
        @endif
        <a href="{{ route('student.chatbot') }}" class="flex-1 text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all flex items-center justify-center shadow-lg shadow-violet-500/20">
            🤖 Ask AI Chatbot
        </a>
    </div>
</div>
@endsection
