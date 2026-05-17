@extends('layouts.admin')

@section('title', 'Quiz Approvals - Admin Dashboard')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200 flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Quiz Management</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">Review and approve quizzes submitted by teachers.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quizzes', ['status' => 'pending']) }}" class="bg-amber-50 text-amber-700 border border-amber-150 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-amber-100 transition shadow-sm">
                Pending Approvals
            </a>
            <a href="{{ route('admin.quizzes') }}" class="bg-white text-slate-700 border border-slate-300 px-4 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition shadow-sm">
                All Quizzes
            </a>
        </div>
    </div>

    @if($quizzes->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-slate-200 text-xs text-slate-400 font-semibold shadow-sm">
            <div class="text-3xl mb-3">📋</div>
            <h3 class="font-bold text-slate-800 mb-1">No Quizzes Found</h3>
            <p class="text-slate-400 font-medium">There are no quizzes matching your current filters.</p>
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3">Quiz Details</th>
                            <th class="px-5 py-3">Teacher</th>
                            <th class="px-5 py-3">Class/Subject</th>
                            <th class="px-5 py-3">Questions</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150">
                        @foreach($quizzes as $quiz)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $quiz->title }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-1">Created {{ $quiz->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-150 text-orange-700 flex items-center justify-center font-bold text-xs">
                                            {{ substr($quiz->teacher->name, 0, 1) }}
                                        </div>
                                        <div class="font-bold text-slate-800">{{ $quiz->teacher->name }}</div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-800">{{ $quiz->class_level }}</div>
                                    <div class="text-[10px] text-orange-600 font-bold uppercase tracking-wider mt-0.5">{{ $quiz->subject }}</div>
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-650 tabular-nums">
                                    {{ $quiz->questions_count }} questions
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider
                                        {{ $quiz->status === 'active' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 
                                           ($quiz->status === 'pending' ? 'bg-amber-50 border-amber-250 text-amber-750' : 'bg-slate-50 border-slate-200 text-slate-600') }}">
                                        {{ $quiz->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.quizzes.preview', $quiz) }}" class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-700 px-2.5 py-1.5 bg-orange-50 border border-orange-200 rounded-md transition shadow-sm" title="Preview Quiz">
                                            Preview
                                        </a>
                                        @if($quiz->status === 'pending')
                                            <form method="POST" action="{{ route('admin.quizzes.approve', $quiz) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3.5 py-1.5 rounded-md transition shadow-sm" title="Approve">
                                                    Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.quizzes.reject', $quiz) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 px-3.5 py-1.5 rounded-md transition shadow-sm" title="Reject">
                                                    Reject
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                              onsubmit="return confirm('Permanently delete this quiz?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-md transition shadow-sm" title="Delete">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
                {{ $quizzes->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
