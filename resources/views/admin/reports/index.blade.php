@extends('layouts.admin')

@section('title', 'Student Reports - Admin')

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">

    {{-- Page Header --}}
    <div class="pb-5 border-b border-slate-200">
        <h1 class="text-xl font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">Student Reports</h1>
        <p class="text-xs text-slate-500 mt-1 font-semibold">Platform-wide performance overview &mdash; {{ number_format($totalCount) }} student{{ $totalCount !== 1 ? 's' : '' }} found.</p>
    </div>

    {{-- ── Filters ── --}}
    <form method="GET" data-no-loading
          class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-4">

        {{-- Search bar (full-width row) --}}
        <div>
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs select-none">&#128269;</span>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search by student name or email…"
                       class="w-full pl-9 pr-4 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            </div>
        </div>

        {{-- Second row: dropdowns + dates --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Course</label>
                <select name="course_id"
                        class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 bg-white text-slate-808 transition">
                    <option value="">All Courses</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ $courseId == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Student</label>
                <select name="student_id"
                        class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 bg-white text-slate-808 transition">
                    <option value="">All Students</option>
                    @foreach($allStudents as $id => $name)
                        <option value="{{ $id }}" {{ $studentId == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">From Date</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            </div>
            <div>
                <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">To Date</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-orange-500/40 focus:border-orange-500/30 text-slate-808 transition">
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-slate-150">
            <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                Apply Filters
            </button>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('admin.reports') }}"
                   class="px-5 py-2.5 border border-slate-350 text-slate-600 hover:bg-slate-50 rounded-lg text-xs font-bold uppercase tracking-wider transition shadow-sm">
                    Clear All
                </a>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider ml-1">Filters active</span>
            @endif
        </div>
    </form>

    {{-- ── Summary Cards ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $summaryCards = [
                ['label' => 'Students (this page)', 'value' => $report->count(),                                        'color' => 'text-slate-900'],
                ['label' => 'Total Enrollments',    'value' => $report->sum('enrolled_courses'),                       'color' => 'text-orange-500'],
                ['label' => 'Lessons Completed',    'value' => $report->sum('completed_lessons'),                      'color' => 'text-emerald-600'],
                ['label' => 'Avg Quiz Score',       'value' => ($report->count() ? round($report->avg('avg_score'),1) : 0) . '%', 'color' => 'text-amber-600'],
            ];
        @endphp
        @foreach($summaryCards as $card)
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:border-orange-500/10 transition">
                <div class="text-xl font-extrabold {{ $card['color'] }} tabular-nums">{{ $card['value'] }}</div>
                <div class="text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Report Table ── --}}
    @if($report->isEmpty())
        <div class="bg-white rounded-xl py-20 text-center border border-slate-200 text-xs text-slate-400 font-semibold shadow-sm">
            <p>No students match the current filters.</p>
            @if($search || $courseId || $studentId || $dateFrom || $dateTo)
                <a href="{{ route('admin.reports') }}" class="mt-4 inline-block text-orange-500 hover:underline">
                    Clear filters
                </a>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">

            {{-- Mobile: cards --}}
            <div class="md:hidden divide-y divide-slate-150">
                @foreach($report as $row)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-bold text-slate-900 text-xs" style="font-family: var(--font-display);">{{ $row['name'] }}</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $row['email'] }}</p>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 bg-slate-50 border border-slate-200 text-slate-600 rounded-md uppercase tracking-wider">{{ $row['class_level'] }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-[10px] pt-1">
                            <div class="bg-slate-50/50 border border-slate-150 rounded-lg p-2">
                                <div class="font-extrabold text-slate-800 tabular-nums">{{ $row['enrolled_courses'] }}</div>
                                <div class="text-slate-400 mt-0.5 font-bold uppercase tracking-wider text-[9px]">Enrolled</div>
                            </div>
                            <div class="bg-slate-50/50 border border-slate-150 rounded-lg p-2">
                                <div class="font-extrabold text-slate-800 tabular-nums">{{ $row['completed_lessons'] }}</div>
                                <div class="text-slate-400 mt-0.5 font-bold uppercase tracking-wider text-[9px]">Lessons</div>
                            </div>
                            <div class="bg-slate-50/50 border border-slate-150 rounded-lg p-2">
                                <div class="font-extrabold {{ $row['avg_score'] >= 60 ? 'text-emerald-600' : ($row['avg_score'] > 0 ? 'text-orange-500' : 'text-slate-400') }} tabular-nums">
                                    {{ $row['avg_score'] > 0 ? $row['avg_score'] . '%' : 'N/A' }}
                                </div>
                                <div class="text-slate-400 mt-0.5 font-bold uppercase tracking-wider text-[9px]">Avg Score</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5 pt-1.5">
                            <div class="flex-1 bg-slate-100 rounded-full h-1.5 border border-slate-200">
                                <div class="bg-orange-500 h-1.5 rounded-full transition-all"
                                     style="width: {{ $row['progress_pct'] }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 w-10 text-right tabular-nums">{{ $row['progress_pct'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Desktop: table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Student</th>
                            <th class="px-5 py-3">Class</th>
                            <th class="px-5 py-3 text-center">Courses</th>
                            <th class="px-5 py-3 text-center">Lessons</th>
                            <th class="px-5 py-3 text-center">Quizzes</th>
                            <th class="px-5 py-3 text-center">Avg Score</th>
                            <th class="px-5 py-3">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150">
                        @foreach($report as $i => $row)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-4 text-slate-400 font-bold tabular-nums">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $i + 1 }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $row['name'] }}</div>
                                    <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $row['email'] }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 bg-slate-50 border border-slate-200 text-slate-650 rounded-md text-[9px] font-bold uppercase tracking-wider">
                                        {{ $row['class_level'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-slate-700 tabular-nums">{{ $row['enrolled_courses'] }}</td>
                                <td class="px-5 py-4 text-center font-bold text-slate-700 tabular-nums">{{ $row['completed_lessons'] }}</td>
                                <td class="px-5 py-4 text-center font-bold text-slate-700 tabular-nums">{{ $row['quizzes_taken'] }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if($row['avg_score'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 border rounded-md text-[9px] font-bold uppercase tracking-wider
                                            {{ $row['avg_score'] >= 60 ? 'bg-emerald-50 border-emerald-250 text-emerald-700' : 'bg-orange-50 border-orange-250 text-orange-700' }}">
                                            {{ $row['avg_score'] }}%
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-bold text-[10px]">N/A</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2 min-w-[5rem] border border-slate-200">
                                            <div class="bg-orange-500 h-2 rounded-full transition-all"
                                                 style="width: {{ $row['progress_pct'] }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-500 w-9 text-right tabular-nums">
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
                <div class="px-5 py-4 border-t border-slate-150 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-50/50">
                    <p class="text-xs text-slate-500 font-semibold">
                        Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ number_format($students->total()) }} students
                    </p>
                    <div class="text-xs">{{ $students->links() }}</div>
                </div>
            @else
                <div class="px-5 py-3.5 border-t border-slate-150 bg-slate-50/50">
                    <p class="text-xs text-slate-500 font-semibold">{{ $students->total() }} student{{ $students->total() !== 1 ? 's' : '' }} shown.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
