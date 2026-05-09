@extends('layouts.teacher')

@section('teacher-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quiz Analytics: {{ $quiz->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Total Attempts: {{ $quiz->attempts->where('status', 'completed')->count() }}</p>
        </div>
        <a href="{{ route('teacher.quizzes') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">← Back to Quizzes</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Question Performance Breakdown</h2>
            <p class="text-sm text-gray-500 mt-1">Identify which concepts students are struggling with.</p>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($questionStats as $id => $stat)
                @php
                    $total = $stat['correct'] + $stat['incorrect'];
                    $correctPct = $total > 0 ? round(($stat['correct'] / $total) * 100) : 0;
                @endphp
                <div class="p-6 hover:bg-gray-50 transition">
                    <p class="font-medium text-gray-800 mb-3">{{ $loop->iteration }}. {{ $stat['text'] }}</p>
                    
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex-1">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $correctPct }}%"></div>
                            </div>
                        </div>
                        <div class="w-32 text-right">
                            <span class="font-semibold {{ $correctPct < 50 ? 'text-red-500' : 'text-emerald-600' }}">{{ $correctPct }}% Correct</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                        <span><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>{{ $stat['correct'] }} Correct</span>
                        <span><span class="inline-block w-2 h-2 rounded-full bg-gray-300 mr-1"></span>{{ $stat['incorrect'] }} Incorrect</span>
                        @if($correctPct < 50 && $total > 0)
                            <span class="text-orange-500 font-medium ml-auto">⚠️ Concept needs review</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    No questions found or no attempts made yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
