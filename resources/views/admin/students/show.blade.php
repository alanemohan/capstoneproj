@extends('layouts.admin')

@section('title', 'Student Profile - ' . $student->name)

@section('admin-content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.students_manager.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Students</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700 col-span-1 h-fit">
            <div class="p-8 text-center">
                <img class="h-32 w-32 rounded-full border-4 border-indigo-100 dark:border-indigo-900 mx-auto" src="{{ $student->avatar_url }}" alt="">
                <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">{{ $student->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $student->email }}</p>
                <div class="mt-6 flex justify-center space-x-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Active Student
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                        🔥 {{ $stats['streak'] }} Day Streak
                    </span>
                </div>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 px-8 py-6 bg-gray-50 dark:bg-gray-900/50">
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_enrollments'] }}</div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mt-1">Courses</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['quizzes_completed'] }}</div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mt-1">Quizzes</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['lessons_completed'] }}</div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mt-1">Lessons</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['average_score'] }}%</div>
                        <div class="text-xs uppercase tracking-wide text-gray-500 font-semibold mt-1">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="col-span-1 lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Recent Enrollments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($student->enrollments->take(5) as $enrollment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $enrollment->course->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $enrollment->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No recent enrollments</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Recent Quiz Attempts</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quiz</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($student->quizAttempts->sortByDesc('created_at')->take(5) as $attempt)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $attempt->quiz->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $attempt->score }} / {{ $attempt->quiz->questions->count() }} ({{ $attempt->percentage }}%)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $attempt->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No quiz attempts yet</td>
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
