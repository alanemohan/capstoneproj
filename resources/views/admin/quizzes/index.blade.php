@extends('layouts.admin')

@section('title', 'Quiz Approvals - Admin Dashboard')

@section('admin-content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quiz Management</h1>
            <p class="text-gray-500 text-sm mt-1">Review and approve quizzes submitted by teachers</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quizzes', ['status' => 'pending']) }}" class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-amber-200 transition">
                Pending Approvals
            </a>
            <a href="{{ route('admin.quizzes') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                All Quizzes
            </a>
        </div>
    </div>

    @if($quizzes->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center shadow-sm border border-gray-100">
            <div class="text-5xl mb-4">📋</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Quizzes Found</h3>
            <p class="text-gray-500 text-sm">There are no quizzes matching your current filters.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Quiz Details</th>
                            <th class="px-6 py-4">Teacher</th>
                            <th class="px-6 py-4">Class/Subject</th>
                            <th class="px-6 py-4">Questions</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quizzes as $quiz)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">{{ $quiz->title }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">Created {{ $quiz->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs">
                                            {{ substr($quiz->teacher->name, 0, 1) }}
                                        </div>
                                        <div class="text-sm text-gray-700">{{ $quiz->teacher->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-800 font-medium">{{ $quiz->class_level }}</div>
                                    <div class="text-xs text-indigo-600">{{ $quiz->subject }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $quiz->questions_count }} questions
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $quiz->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 
                                           ($quiz->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ strtoupper($quiz->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.quizzes.preview', $quiz) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Preview Quiz">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @if($quiz->status === 'pending')
                                            <form method="POST" action="{{ route('admin.quizzes.approve', $quiz) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Approve">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                            <button @click="$dispatch('open-reject-modal', {id: {{ $quiz->id }}, title: '{{ addslashes($quiz->title) }}'})" 
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $quizzes->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
