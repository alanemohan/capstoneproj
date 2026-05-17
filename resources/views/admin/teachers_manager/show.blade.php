@extends('layouts.admin')

@section('title', 'Teacher Profile - ' . $teacher->name)

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <a href="{{ route('admin.teachers_manager.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-orange-500 uppercase tracking-wider transition">
            ← Back to Teachers
        </a>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-2" style="font-family: var(--font-display);">Teacher Profile</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Inspect and review {{ $teacher->name }}'s courses, lessons, and quiz approvals.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-fit">
            <div class="p-6 text-center">
                <img class="h-28 w-28 rounded-xl object-cover border border-slate-200 mx-auto" src="{{ $teacher->avatar_url }}" alt="">
                <h2 class="mt-4 text-base font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $teacher->name }}</h2>
                <p class="text-xs text-slate-450 font-semibold mt-0.5">{{ $teacher->email }}</p>
                <div class="mt-4 flex justify-center">
                    <span class="text-[9px] font-bold px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md uppercase tracking-wider">
                        {{ $teacher->status ?? 'Approved' }} Teacher
                    </span>
                </div>
            </div>
            <div class="border-t border-slate-150 px-6 py-5 bg-slate-50/50">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-lg font-extrabold text-orange-500 tabular-nums">{{ $stats['total_courses'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Courses</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-blue-600 tabular-nums">{{ $stats['total_lessons'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Lessons</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-purple-600 tabular-nums">{{ $stats['total_quizzes'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Quizzes</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-emerald-600 tabular-nums">{{ $stats['students_reached'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Reach</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-150">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Courses Created</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                                <th class="px-5 py-3">Title</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150">
                            @forelse($teacher->courses as $course)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">
                                    {{ $course->title }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[9px] font-bold px-2 py-0.5 border rounded-md uppercase tracking-wider {{ $course->status === 'published' ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-amber-50 border-amber-250 text-amber-755' }}">
                                        {{ $course->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">
                                    {{ $course->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 font-semibold">No courses created yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-150">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Quizzes Created</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                                <th class="px-5 py-3">Title</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150">
                            @forelse($teacher->quizzes->sortByDesc('created_at')->take(5) as $quiz)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">
                                    {{ $quiz->title }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-[9px] font-bold px-2 py-0.5 border rounded-md uppercase tracking-wider {{ $quiz->is_active ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-slate-50 border-slate-200 text-slate-600' }}">
                                        {{ $quiz->is_active ? 'Active' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">
                                    {{ $quiz->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 font-semibold">No quizzes created yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
