@extends('layouts.app')

@section('title', 'My Assignments')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Assignments</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View and submit assignments from your class batches.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isPastDue = $assignment->due_date->isPast();
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-xs font-medium text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">{{ $assignment->batch->name }}</span>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mt-1 line-clamp-1" title="{{ $assignment->title }}">{{ $assignment->title }}</h3>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-300 flex-1 line-clamp-2 mb-4">{{ Str::limit($assignment->description, 100) }}</p>
                
                <div class="space-y-3 mt-auto">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Due Date:</span>
                        <span class="font-medium {{ $isPastDue ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                            {{ $assignment->due_date->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Status:</span>
                        @if($submission)
                            @if($submission->status === 'graded')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Graded ({{ $submission->marks }}/{{ $assignment->max_marks }})</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Submitted</span>
                            @endif
                        @else
                            @if($isPastDue)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Missing</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                            @endif
                        @endif
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('student.assignments.show', $assignment) }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        {{ $submission ? 'View Submission' : 'Submit Assignment' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No assignments</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any assignments due right now.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-8">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
