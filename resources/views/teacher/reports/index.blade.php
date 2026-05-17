@extends('layouts.teacher')

@section('title', 'Student Reports - Teacher')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">

    {{-- Page Header --}}
    <div class="pb-5 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Student Reports</h1>
        <p class="text-xs text-gray-500 mt-1">Students enrolled in your courses or who attempted your quizzes.</p>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" data-no-loading
          class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm space-y-4">

        {{-- Search bar --}}
        <div>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs select-none">&#128269;</span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by student name or email…"
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-gray-808 transition">
            </div>
        </div>

        {{-- Dropdowns + dates --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Course</label>
                <select name="course_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 bg-white text-gray-808 transition">
                    <option value="">All My Courses</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ $courseId == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Student</label>
                <select name="student_id"
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 bg-white text-gray-808 transition">
                    <option value="">All Students</option>
                    @foreach($allStudentsDropdown as $id => $name)
                        <option value="{{ $id }}" {{ $studentId == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">From</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-gray-808 transition">
            </div>
            <div>
                <label class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">To</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-gray-808 transition">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Apply Filters
            </button>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('teacher.reports') }}"
                   class="px-5 py-2.5 border border-gray-350 text-gray-650 hover:bg-gray-50 rounded-lg text-xs font-bold uppercase tracking-wider transition">
                    Clear All
                </a>
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Filters active</span>
            @endif
        </div>
    </form>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Students (this page)', 'value' => $report->count(),                                        'color' => 'text-gray-900',   'bg' => ''],
                ['label' => 'Lessons Completed',    'value' => $report->sum('completed_lessons'),                      'color' => 'text-emerald-600', 'bg' => ''],
                ['label' => 'Quiz Attempts',        'value' => $report->sum('quizzes_taken'),                          'color' => 'text-indigo-650',  'bg' => ''],
                ['label' => 'Avg Quiz Score',       'value' => ($report->count() ? round($report->avg('avg_score'),1) : 0) . '%', 'color' => 'text-amber-600', 'bg' => ''],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm transition hover:border-emerald-500/10">
                <div class="text-xl font-extrabold {{ $card['color'] }} tracking-tight tabular-nums">{{ $card['value'] }}</div>
                <div class="text-[9px] text-gray-400 mt-1 font-bold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Report Table ── --}}
    @if($report->isEmpty())
        <div class="bg-white rounded-xl py-20 text-center border border-gray-200 shadow-sm text-xs text-gray-400">
            <p class="font-semibold">No students match the current filters.</p>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('teacher.reports') }}" class="mt-4 inline-block text-emerald-600 font-bold hover:underline">
                    Clear filters
                </a>
            @else
                <p class="text-gray-400 mt-2 font-medium">Students appear here once they enroll in your courses or attempt your quizzes.</p>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">

            {{-- Mobile: cards --}}
            <div class="md:hidden divide-y divide-gray-150">
                @foreach($report as $row)
                    <div class="p-4 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-bold text-gray-900 text-xs leading-snug" style="font-family: var(--font-display);">{{ $row['name'] }}</p>
                                <p class="text-[10px] text-gray-400 font-semibold">{{ $row['email'] }}</p>
                            </div>
                            <span class="shrink-0 text-[9px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-150 text-gray-500 px-2 py-0.5 rounded-md">{{ $row['class_level'] }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] pt-1">
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2">
                                <div class="font-black text-gray-800 tabular-nums">{{ $row['completed_lessons'] }}/{{ $row['total_lessons'] }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Lessons</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2">
                                <div class="font-black text-gray-800 tabular-nums">{{ $row['quizzes_taken'] }}</div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Quizzes</div>
                            </div>
                            <div class="bg-gray-50 border border-gray-150 rounded-lg p-2">
                                <div class="font-black {{ $row['avg_score'] >= 60 ? 'text-emerald-600' : ($row['avg_score'] > 0 ? 'text-orange-550' : 'text-gray-400') }} tabular-nums">
                                    {{ $row['avg_score'] > 0 ? $row['avg_score'] . '%' : 'N/A' }}
                                </div>
                                <div class="text-[9px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">Avg Score</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-1.5">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full"
                                     style="width: {{ $row['progress_pct'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-extrabold text-gray-900 w-10 text-right tabular-nums">{{ $row['progress_pct'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200 text-left text-[10px] text-gray-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3 w-8">#</th>
                            <th class="px-5 py-3">Student</th>
                            <th class="px-5 py-3">Class</th>
                            <th class="px-5 py-3 text-center">Lessons Done</th>
                            <th class="px-5 py-3 text-center">Total</th>
                            <th class="px-5 py-3 text-center">Quizzes</th>
                            <th class="px-5 py-3 text-center">Avg Score</th>
                            <th class="px-5 py-3">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150">
                        @foreach($report as $i => $row)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-3.5 text-gray-400 tabular-nums font-semibold">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $row['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 font-semibold mt-0.5">{{ $row['email'] }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="px-2.5 py-0.5 bg-gray-50 border border-gray-150 text-gray-600 rounded-md text-[9px] font-bold uppercase tracking-wider">
                                        {{ $row['class_level'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center font-bold text-gray-900 tabular-nums">{{ $row['completed_lessons'] }}</td>
                                <td class="px-5 py-3.5 text-center text-gray-400 font-semibold tabular-nums">{{ $row['total_lessons'] }}</td>
                                <td class="px-5 py-3.5 text-center font-bold text-gray-900 tabular-nums">{{ $row['quizzes_taken'] }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($row['avg_score'] > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                            {{ $row['avg_score'] >= 60 ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-orange-50 border-orange-250 text-orange-700' }}">
                                            {{ $row['avg_score'] }}%
                                        </span>
                                    @else
                                        <span class="text-gray-400 font-semibold">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden min-w-[5rem]">
                                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all"
                                                 style="width: {{ $row['progress_pct'] }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-extrabold text-gray-900 w-9 text-right tabular-nums">
                                            {{ $row['progress_pct'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="px-5 py-4 border-t border-gray-150 flex flex-col sm:flex-row items-center justify-between gap-3 bg-gray-50/50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                        Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ number_format($students->total()) }} students
                    </p>
                    <div class="text-xs">{{ $students->links() }}</div>
                </div>
            @else
                <div class="px-5 py-3 border-t border-gray-150 bg-gray-50/50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $students->total() }} student{{ $students->total() !== 1 ? 's' : '' }} shown</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
