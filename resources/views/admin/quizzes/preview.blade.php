@extends('layouts.admin')

@section('title', 'Preview Quiz - ' . $quiz->title)

@section('admin-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in text-slate-800">
    {{-- Header --}}
    <div class="flex items-center justify-between pb-5 border-b border-slate-200 flex-wrap gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes') }}" class="text-[10px] font-bold text-slate-400 hover:text-orange-500 uppercase tracking-wider transition">
                ← Back to Quizzes
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Preview Quiz</h1>
                <p class="text-xs text-slate-500 mt-1 font-semibold">Reviewing: {{ $quiz->title }}</p>
            </div>
        </div>

        @if($quiz->status === 'pending')
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.quizzes.reject', $quiz) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-750 border border-red-150 text-[10px] font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition">
                        Reject
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.quizzes.approve', $quiz) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition shadow-sm">
                        Approve & Activate
                    </button>
                </form>
            </div>
        @else
            <span class="text-[9px] font-bold px-3 py-1.5 border border-slate-200 rounded-md bg-slate-50 text-slate-600 uppercase tracking-wider">
                Status: {{ $quiz->status }}
            </span>
        @endif
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6 grid grid-cols-1 md:grid-cols-3 gap-6 shadow-sm">
        <div class="space-y-1">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Teacher</p>
            <p class="text-xs text-slate-900 font-bold" style="font-family: var(--font-display);">{{ $quiz->teacher->name }}</p>
        </div>
        <div class="space-y-1">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Class & Subject</p>
            <p class="text-xs text-slate-900 font-bold" style="font-family: var(--font-display);">{{ $quiz->class_level }} — {{ $quiz->subject }}</p>
        </div>
        <div class="space-y-1">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Time & Passing</p>
            <p class="text-xs text-slate-900 font-bold tabular-nums" style="font-family: var(--font-display);">{{ $quiz->time_limit }} mins / {{ $quiz->passing_marks }}%</p>
        </div>
        <div class="md:col-span-3 border-t border-slate-150 pt-4">
            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mb-2">Description</p>
            <p class="text-xs text-slate-655 leading-relaxed font-medium">{{ $quiz->description ?: 'No description provided.' }}</p>
        </div>
    </div>

    {{-- Questions --}}
    <div class="space-y-4">
        <h2 class="font-bold text-slate-900 text-sm tracking-tight flex items-center gap-2 mb-4" style="font-family: var(--font-display);">
            <span>📝</span> Questions ({{ $quiz->questions->count() }})
        </h2>
        
        @foreach($quiz->questions as $index => $q)
            <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4 shadow-sm hover:border-orange-500/10 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 border border-orange-150 text-orange-700 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-slate-900 font-bold text-sm leading-snug" style="font-family: var(--font-display);">{{ $q->question_text }}</p>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md uppercase whitespace-nowrap tracking-wider">
                        {{ $q->type }} • {{ $q->marks }} pts
                    </span>
                </div>

                @if($q->type === 'mcq' || $q->type === 'true_false')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-9">
                        @foreach(['a','b','c','d'] as $opt)
                            @php $val = $q->{'option_'.$opt}; @endphp
                            @if($val)
                                <div class="flex items-center gap-3 p-3 rounded-lg border {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'bg-emerald-50 border-emerald-250' : 'bg-slate-50/50 border-slate-150' }}">
                                    <span class="w-6 h-6 rounded-md {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'bg-emerald-500 text-white font-black' : 'bg-white border border-slate-200 text-slate-400' }} flex items-center justify-center text-xs font-bold uppercase shrink-0">
                                        {{ $opt }}
                                    </span>
                                    <span class="text-xs {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'font-bold text-emerald-700' : 'font-medium text-slate-700' }}">
                                        {{ $val }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="pl-9 space-y-2">
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Correct Answer Keywords</p>
                        <p class="text-xs text-emerald-755 font-bold bg-emerald-50 border border-emerald-250 p-3 rounded-lg italic">
                            {{ $q->correct_answer }}
                        </p>
                    </div>
                @endif

                @if($q->explanation)
                    <div class="pl-9 mt-4">
                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-lg flex gap-3 text-xs">
                            <span class="text-base">💡</span>
                            <div class="text-amber-800 leading-relaxed font-semibold">
                                <p class="font-bold mb-1 uppercase tracking-wider text-[9px] text-amber-600">Explanation</p>
                                {{ $q->explanation }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
