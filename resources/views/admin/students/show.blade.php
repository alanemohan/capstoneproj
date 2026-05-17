@extends('layouts.admin')

@section('title', 'Student Profile - ' . $student->name)

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    <div class="pb-5 border-b border-slate-200">
        <a href="{{ route('admin.students_manager.index') }}" class="text-[10px] font-bold text-slate-400 hover:text-orange-500 uppercase tracking-wider transition">
            ← Back to Students
        </a>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight mt-2" style="font-family: var(--font-display);">Student Profile</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Inspect and review {{ $student->name }}'s activity, streak records, and completions.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden h-fit">
            <div class="p-6 text-center">
                <img class="h-28 w-28 rounded-xl object-cover border border-slate-200 mx-auto" src="{{ $student->avatar_url }}" alt="">
                <h2 class="mt-4 text-base font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $student->name }}</h2>
                <p class="text-xs text-slate-450 font-semibold mt-0.5">{{ $student->email }}</p>
                <div class="mt-4 flex justify-center gap-1.5 flex-wrap">
                    <span class="text-[9px] font-bold px-2 py-0.5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-md uppercase tracking-wider">
                        Active Student
                    </span>
                    <span class="text-[9px] font-bold px-2 py-0.5 bg-orange-50 border border-orange-200 text-orange-700 rounded-md uppercase tracking-wider">
                        🔥 {{ $stats['streak'] }} Day Streak
                    </span>
                </div>
            </div>
            <div class="border-t border-slate-150 px-6 py-5 bg-slate-50/50">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-lg font-extrabold text-orange-500 tabular-nums">{{ $stats['total_enrollments'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Courses</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-purple-600 tabular-nums">{{ $stats['quizzes_completed'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Quizzes</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-blue-600 tabular-nums">{{ $stats['lessons_completed'] }}</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Lessons</div>
                    </div>
                    <div>
                        <div class="text-lg font-extrabold text-emerald-600 tabular-nums">{{ $stats['average_score'] }}%</div>
                        <div class="text-[9px] uppercase tracking-wider text-slate-400 font-bold mt-0.5">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-150">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Recent Enrollments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                                <th class="px-5 py-3">Course</th>
                                <th class="px-5 py-3">Enrolled On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150">
                            @forelse($student->enrollments->take(5) as $enrollment)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">
                                    {{ $enrollment->course->title }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">
                                    {{ $enrollment->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-400 font-semibold">No recent enrollments.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-150">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Recent Quiz Attempts</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                                <th class="px-5 py-3">Quiz</th>
                                <th class="px-5 py-3">Score</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150">
                            @forelse($student->quizAttempts->sortByDesc('created_at')->take(5) as $attempt)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4 font-bold text-slate-900" style="font-family: var(--font-display);">
                                    {{ $attempt->quiz->title }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-650 tabular-nums">
                                    {{ $attempt->score }} / {{ $attempt->quiz->questions->count() }} ({{ $attempt->percentage }}%)
                                </td>
                                <td class="px-5 py-4 text-slate-500 font-semibold tabular-nums">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 font-semibold">No quiz attempts yet.</td>
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
