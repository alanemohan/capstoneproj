@extends('layouts.teacher')

@section('teacher-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Quiz Analytics: {{ $quiz->title }}</h1>
            <p class="text-xs text-gray-500 mt-1">Total Attempts: <span class="font-extrabold text-gray-900 tabular-nums">{{ $quiz->attempts->where('status', 'completed')->count() }}</span></p>
        </div>
        <a href="{{ route('teacher.quizzes') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back to Quizzes</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-gray-200 bg-gray-50">
            <h2 class="text-xs font-bold text-gray-800 uppercase tracking-wider" style="font-family: var(--font-display);">Question Performance Breakdown</h2>
            <p class="text-[10px] text-gray-400 font-semibold mt-1">Identify which concepts students are struggling with.</p>
        </div>
        
        <div class="divide-y divide-gray-150">
            @forelse($questionStats as $id => $stat)
                @php
                    $total = $stat['correct'] + $stat['incorrect'];
                    $correctPct = $total > 0 ? round(($stat['correct'] / $total) * 100) : 0;
                @endphp
                <div class="p-5 hover:bg-gray-50/50 transition">
                    <p class="font-bold text-xs text-gray-900 leading-relaxed mb-3" style="font-family: var(--font-display);">{{ $loop->iteration }}. {{ $stat['text'] }}</p>
                    
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex-1">
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full transition-all" style="width: {{ $correctPct }}%"></div>
                            </div>
                        </div>
                        <div class="w-24 text-right">
                            <span class="font-extrabold uppercase tracking-wider text-[10px] {{ $correctPct < 50 ? 'text-red-500' : 'text-emerald-600' }}">{{ $correctPct }}% Correct</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                        <span><span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>{{ $stat['correct'] }} Correct</span>
                        <span><span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-300 mr-1.5"></span>{{ $stat['incorrect'] }} Incorrect</span>
                        @if($correctPct < 50 && $total > 0)
                            <span class="text-orange-500 font-bold ml-auto flex items-center gap-1">⚠️ Concept needs review</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400 text-xs">
                    No questions found or no attempts made yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
