@extends('layouts.teacher')

@section('title', 'My Quizzes - Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">My Quizzes</h1>
            <p class="text-xs text-gray-500 mt-1">Create and manage assessments for your students.</p>
        </div>
        <a href="{{ route('teacher.quizzes.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            Create Quiz
        </a>
    </div>

    @if($quizzes->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-2">No Quizzes Yet</h3>
            <p class="text-xs text-gray-500 mb-5">Create your first quiz to test student understanding!</p>
            <a href="{{ route('teacher.quizzes.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg transition font-bold text-xs uppercase tracking-wider shadow-sm">
                Create First Quiz
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($quizzes as $quiz)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm transition duration-300 hover:border-emerald-500/20">
                    <div class="h-1 {{ $quiz->status === 'active' ? 'bg-emerald-500' : ($quiz->status === 'draft' ? 'bg-yellow-400' : 'bg-gray-400') }}"></div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[9px] font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md uppercase tracking-wider">{{ $quiz->subject }}</span>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-md border uppercase tracking-wider
                                {{ $quiz->status === 'active' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : ($quiz->status === 'draft' ? 'bg-yellow-50 border-yellow-250 text-yellow-700' : 'bg-gray-50 border-gray-200 text-gray-650') }}">
                                {{ $quiz->status }}
                            </span>
                        </div>

                        <h3 class="font-bold text-xs text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $quiz->title }}</h3>
                        <p class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $quiz->class_level }}</p>

                        <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2.5">
                                <div class="text-xs font-black text-gray-900 tabular-nums">{{ $quiz->questions_count }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Questions</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2.5">
                                <div class="text-xs font-black text-gray-900 tabular-nums">{{ $quiz->attempts_count }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Attempts</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2.5">
                                <div class="text-xs font-black text-gray-900 tabular-nums">{{ $quiz->time_limit }}m</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Time</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <form method="POST" action="{{ route('teacher.quizzes.toggle', $quiz->id) }}" class="w-full inline" data-no-loading>
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-full text-[10px] font-bold uppercase tracking-wider py-2 rounded-lg border transition
                                               {{ $quiz->status === 'active' ? 'bg-yellow-50 border-yellow-250 text-yellow-750 hover:bg-yellow-100' : 'bg-emerald-50 border-emerald-250 text-emerald-705 hover:bg-emerald-100' }}">
                                    {{ $quiz->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" class="w-full block text-center text-[10px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 py-2 rounded-lg transition">
                                Edit
                              </a>
                            <a href="{{ route('teacher.quizzes.analytics', $quiz->id) }}" class="w-full block text-center text-[10px] font-bold uppercase tracking-wider bg-indigo-50 border border-indigo-150 hover:bg-indigo-100 text-indigo-700 py-2 rounded-lg transition">
                                Analytics
                            </a>
                            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz->id) }}"
                                  onsubmit="return confirm('Delete this quiz?')" class="w-full inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-150 hover:bg-red-100 text-red-700 py-2 rounded-lg transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pt-4">{{ $quizzes->links() }}</div>
    @endif
</div>
@endsection
