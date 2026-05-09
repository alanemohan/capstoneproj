@extends('layouts.app')

@section('title', 'Assignment: ' . $assignment->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('student.assignments.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Assignments</span>
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8 sm:p-10 sm:pb-8">
            <div class="flex justify-between items-start">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white mb-3 shadow-sm backdrop-blur-sm">
                        {{ $assignment->batch->name }}
                    </span>
                    <h1 class="text-3xl font-extrabold text-white sm:text-4xl">
                        {{ $assignment->title }}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Details -->
        <div class="px-6 py-6 sm:px-10 flex flex-wrap gap-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="{{ $assignment->due_date->isPast() ? 'text-red-600 font-medium' : '' }}">
                    Due: {{ $assignment->due_date->format('l, M d, Y') }}
                </span>
            </div>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Max Marks: {{ $assignment->max_marks }}
            </div>
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Teacher: {{ $assignment->teacher->name }}
            </div>
        </div>

        <div class="px-6 py-8 sm:p-10 prose dark:prose-invert max-w-none">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Instructions</h3>
            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $assignment->description }}</div>
        </div>
        
        <!-- Submission Section -->
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-8 sm:p-10 border-t border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Your Submission</h3>
            
            @if($submission)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-green-200 dark:border-green-900 shadow-sm overflow-hidden">
                    <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Submitted Successfully</h4>
                                <p class="text-sm text-gray-500">On {{ $submission->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            Download File
                        </a>
                    </div>
                    
                    @if($submission->status === 'graded')
                        <div class="p-5 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-t border-green-100 dark:border-green-800/30">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Teacher Feedback</h4>
                            <div class="flex justify-between items-start">
                                <p class="text-sm text-gray-700 dark:text-gray-300 flex-1 pr-4">{{ $submission->feedback ?? 'No written feedback provided.' }}</p>
                                <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm text-center border border-green-200 dark:border-green-800">
                                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $submission->marks }}</div>
                                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wide">/ {{ $assignment->max_marks }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-5 bg-yellow-50 dark:bg-yellow-900/20">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">Your assignment is waiting to be graded by the teacher.</p>
                        </div>
                    @endif
                </div>
            @elseif($assignment->due_date->isPast())
                <div class="rounded-xl bg-red-50 dark:bg-red-900/20 p-5 border border-red-200 dark:border-red-800">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Assignment is Past Due</h3>
                            <div class="mt-2 text-sm text-red-700 dark:text-red-400">
                                <p>The deadline for this assignment has passed and submissions are no longer accepted.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload your work (PDF, DOCX, ZIP)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:border-indigo-500 transition-colors bg-gray-50 dark:bg-gray-700/50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Select a file</span>
                                        <input id="file-upload" name="submission_file" type="file" class="sr-only" required accept=".pdf,.doc,.docx,.zip">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">up to 10MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Submit Assignment
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple file name display
    const fileUpload = document.getElementById('file-upload');
    if(fileUpload) {
        fileUpload.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if(fileName) {
                const label = this.closest('.space-y-1').querySelector('.text-indigo-600');
                label.innerHTML = `<span class="text-green-600 dark:text-green-400 font-bold">Selected: ${fileName}</span>`;
            }
        });
    }
</script>
@endpush
@endsection
