@extends('layouts.admin')

@section('title', 'Preview Quiz - ' . $quiz->title)

@section('admin-content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.quizzes') }}" class="p-2 hover:bg-gray-100 rounded-xl transition text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Preview Quiz</h1>
                <p class="text-sm text-gray-500">Reviewing: {{ $quiz->title }}</p>
            </div>
        </div>

        @if($quiz->status === 'pending')
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.quizzes.reject', $quiz) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-red-50 text-red-600 px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-100 transition">
                        Reject
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.quizzes.approve', $quiz) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-xl text-sm font-bold hover:bg-emerald-700 transition shadow-sm shadow-emerald-200">
                        Approve & Activate
                    </button>
                </form>
            </div>
        @else
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl text-sm font-bold capitalize">
                Status: {{ $quiz->status }}
            </span>
        @endif
    </div>

    {{-- Details Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-1">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Teacher</p>
            <p class="text-sm text-gray-800 font-semibold">{{ $quiz->teacher->name }}</p>
        </div>
        <div class="space-y-1">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Class & Subject</p>
            <p class="text-sm text-gray-800 font-semibold">{{ $quiz->class_level }} — {{ $quiz->subject }}</p>
        </div>
        <div class="space-y-1">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Time & Passing</p>
            <p class="text-sm text-gray-800 font-semibold">{{ $quiz->time_limit }} mins / {{ $quiz->passing_marks }}%</p>
        </div>
        <div class="md:col-span-3 border-t border-gray-50 pt-4">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider mb-2">Description</p>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $quiz->description ?: 'No description provided.' }}</p>
        </div>
    </div>

    {{-- Questions --}}
    <div class="space-y-4">
        <h2 class="font-bold text-gray-800 text-lg flex items-center gap-2">
            <span>📝</span> Questions ({{ $quiz->questions->count() }})
        </h2>
        
        @foreach($quiz->questions as $index => $q)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                            {{ $index + 1 }}
                        </span>
                        <p class="text-gray-800 font-medium">{{ $q->question_text }}</p>
                    </div>
                    <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-lg uppercase whitespace-nowrap">
                        {{ $q->type }} • {{ $q->marks }} pts
                    </span>
                </div>

                @if($q->type === 'mcq' || $q->type === 'true_false')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-9">
                        @foreach(['a','b','c','d'] as $opt)
                            @php $val = $q->{'option_'.$opt}; @endphp
                            @if($val)
                                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'bg-emerald-50 border-emerald-200' : 'bg-gray-50 border-gray-100' }}">
                                    <span class="w-6 h-6 rounded-lg {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'bg-emerald-500 text-white' : 'bg-white text-gray-400' }} flex items-center justify-center text-xs font-bold uppercase">
                                        {{ $opt }}
                                    </span>
                                    <span class="text-sm {{ $q->correct_answer === $opt || $q->correct_answer === $val ? 'font-bold text-emerald-700' : 'text-gray-600' }}">
                                        {{ $val }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="pl-9 space-y-2">
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Correct Answer Keywords</p>
                        <p class="text-sm text-emerald-700 font-bold bg-emerald-50 border border-emerald-100 p-3 rounded-xl italic">
                            {{ $q->correct_answer }}
                        </p>
                    </div>
                @endif

                @if($q->explanation)
                    <div class="pl-9 mt-4 bg-amber-50 border border-amber-100 p-4 rounded-xl flex gap-3">
                        <span class="text-lg">💡</span>
                        <div class="text-xs text-amber-800 leading-relaxed">
                            <p class="font-bold mb-1 uppercase tracking-wider">Explanation</p>
                            {{ $q->explanation }}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
