@extends('layouts.app')

@section('title', 'Manage Batches')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Class Batches</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your students, assignments, and attendance by batch.</p>
        </div>
        <a href="{{ route('teacher.batches.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Create New Batch
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($batches as $batch)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $batch->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $batch->class_level }} • {{ $batch->subject }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                    {{ $batch->students_count }} Students
                </span>
            </div>
            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 flex-1">{{ Str::limit($batch->description, 100) }}</p>
            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <span class="text-xs text-gray-500">Created {{ $batch->created_at->format('M d, Y') }}</span>
                <a href="{{ route('teacher.batches.show', $batch) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Manage Batch &rarr;</a>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white dark:bg-gray-800 rounded-2xl shadow p-12 text-center border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No batches</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new batch.</p>
            <div class="mt-6">
                <a href="{{ route('teacher.batches.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Batch
                </a>
            </div>
        </div>
        @endforelse
    </div>
    
    <div class="mt-6">
        {{ $batches->links() }}
    </div>
</div>
@endsection
