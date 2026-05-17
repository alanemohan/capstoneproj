@extends('layouts.admin')

@section('title', 'Manage Students')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="flex items-center justify-between pb-5 border-b border-slate-200 flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Student Master List</h1>
            <p class="text-xs text-slate-500 mt-1 font-semibold">View and manage all registered students and their metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.students_manager.index') }}" class="relative w-64">
                <input type="text" name="search" placeholder="Search students..." value="{{ request('search') }}"
                       class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </form>
            <a href="{{ route('admin.students_manager.export') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Enrollments</th>
                        <th class="px-5 py-3">Quizzes Attempted</th>
                        <th class="px-5 py-3">Streak</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-150">
                    @forelse($students as $student)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img class="h-9 w-9 rounded-lg object-cover border border-slate-200" src="{{ $student->avatar_url }}" alt="">
                                <div>
                                    <div class="font-bold text-slate-900 text-sm leading-snug" style="font-family: var(--font-display);">{{ $student->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[9px] font-bold px-2.5 py-0.5 border border-blue-200 bg-blue-50 text-blue-700 rounded-md uppercase tracking-wider">
                                {{ $student->enrollments_count }} Courses
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-[9px] font-bold px-2.5 py-0.5 border border-purple-200 bg-purple-50 text-purple-700 rounded-md uppercase tracking-wider">
                                {{ $student->quiz_attempts_count }} Attempts
                            </span>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-700 tabular-nums">
                            <div class="flex items-center gap-1">
                                <span class="text-orange-500">🔥</span>
                                <span>{{ $student->streak_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-700">
                            @php
                                $statusVal = $student->status ?? 'approved';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                {{ $statusVal === 'approved' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' :
                                   ($statusVal === 'rejected' ? 'bg-red-50 border-red-250 text-red-700' : 'bg-amber-50 border-amber-250 text-amber-750') }}">
                                {{ $statusVal }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.students_manager.show', $student) }}" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-orange-50 border border-orange-200 hover:text-orange-700 rounded-md text-orange-600 transition shadow-sm">
                                    View Profile
                                </a>
                                
                                @if(($student->status ?? 'approved') === 'pending')
                                    <form method="POST" action="{{ route('admin.students_manager.approve', $student) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 rounded-md transition shadow-sm">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.students_manager.reject', $student) }}" onsubmit="return confirm('Reject {{ addslashes($student->name) }}?')" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-red-50 border border-red-200 hover:bg-red-100 text-red-700 rounded-md transition shadow-sm">
                                            Reject
                                        </button>
                                    </form>
                                @elseif(($student->status ?? 'approved') === 'rejected')
                                    <form method="POST" action="{{ route('admin.students_manager.approve', $student) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1.5 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 rounded-md transition shadow-sm">
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.students_manager.toggle', $student) }}" data-no-loading class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1.5 border rounded-md transition shadow-sm {{ $student->is_active ? 'border-red-200 text-red-700 bg-red-50 hover:bg-red-100' : 'border-emerald-200 text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}">
                                            {{ $student->is_active ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.students_manager.destroy', $student) }}" onsubmit="return confirm('Are you sure you want to delete this student?')" data-no-loading class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1.5 rounded-md transition shadow-sm">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-semibold">No students found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-slate-150 bg-slate-50/50">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
