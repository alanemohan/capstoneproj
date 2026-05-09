@extends('layouts.app')

@section('title', 'Manage Batch: ' . $batch->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ tab: 'students' }">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('teacher.batches.index') }}" class="text-indigo-600 hover:text-indigo-900 flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Batches</span>
        </a>
        <div class="flex space-x-3">
            <a href="{{ route('teacher.batches.edit', $batch) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Edit Details
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
        <div class="p-6 sm:p-10 text-center sm:text-left flex flex-col sm:flex-row items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $batch->name }}</h1>
                <p class="mt-2 text-lg text-gray-500 dark:text-gray-400">{{ $batch->class_level }} • {{ $batch->subject }}</p>
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 max-w-2xl">{{ $batch->description }}</p>
            </div>
            <div class="mt-6 sm:mt-0 bg-indigo-50 dark:bg-indigo-900/30 p-6 rounded-xl border border-indigo-100 dark:border-indigo-800 text-center">
                <div class="text-4xl font-black text-indigo-600 dark:text-indigo-400">{{ $batch->students->count() }}</div>
                <div class="text-sm font-medium text-indigo-800 dark:text-indigo-300 uppercase tracking-wide mt-1">Students</div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-6 py-3">
            <nav class="-mb-px flex space-x-8">
                <button @click="tab = 'students'" :class="{'border-indigo-500 text-indigo-600': tab === 'students', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'students'}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Students
                </button>
                <button @click="tab = 'attendance'" :class="{'border-indigo-500 text-indigo-600': tab === 'attendance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'attendance'}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Mark Attendance
                </button>
                <button @click="tab = 'assignments'" :class="{'border-indigo-500 text-indigo-600': tab === 'assignments', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'assignments'}" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm">
                    Assignments
                </button>
            </nav>
        </div>
    </div>

    <!-- Students Tab -->
    <div x-show="tab === 'students'" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add Student</h3>
            <form action="{{ route('teacher.batches.students.add', $batch) }}" method="POST" class="flex items-center space-x-4">
                @csrf
                <select name="student_id" required class="flex-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">Select a student to add...</option>
                    @foreach($availableStudents as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Add Student
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($batch->students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full" src="{{ $student->avatar_url }}" alt="">
                                <div class="ml-4 font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('teacher.batches.students.remove', [$batch, $student]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-medium" onclick="return confirm('Remove this student from the batch?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">No students enrolled in this batch.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Attendance Tab -->
    <div x-show="tab === 'attendance'" x-cloak class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Mark Attendance for Today ({{ now()->format('M d, Y') }})</h3>
        
        @if($batch->students->isEmpty())
            <p class="text-gray-500">Please add students to the batch before marking attendance.</p>
        @else
            <form action="{{ route('teacher.batches.attendance.store', $batch) }}" method="POST">
                @csrf
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($batch->students as $student)
                                @php
                                    $attendanceRecord = $batch->attendances->where('student_id', $student->id)->first();
                                    $currentStatus = $attendanceRecord ? $attendanceRecord->status : 'present';
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $student->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="inline-flex rounded-md shadow-sm" role="group">
                                            <label class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-l-lg hover:bg-gray-100 hover:text-green-700 cursor-pointer 
                                                {{ $currentStatus === 'present' ? 'bg-green-100 text-green-700 z-10 ring-2 ring-green-600' : 'bg-white text-gray-900' }}">
                                                <input type="radio" name="attendance[{{ $student->id }}]" value="present" class="sr-only" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                                Present
                                            </label>
                                            <label class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 hover:bg-gray-100 hover:text-red-700 cursor-pointer
                                                {{ $currentStatus === 'absent' ? 'bg-red-100 text-red-700 z-10 ring-2 ring-red-600' : 'bg-white text-gray-900' }}">
                                                <input type="radio" name="attendance[{{ $student->id }}]" value="absent" class="sr-only" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                                Absent
                                            </label>
                                            <label class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-r-lg hover:bg-gray-100 hover:text-yellow-700 cursor-pointer
                                                {{ $currentStatus === 'late' ? 'bg-yellow-100 text-yellow-700 z-10 ring-2 ring-yellow-600' : 'bg-white text-gray-900' }}">
                                                <input type="radio" name="attendance[{{ $student->id }}]" value="late" class="sr-only" {{ $currentStatus === 'late' ? 'checked' : '' }}>
                                                Late
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Save Attendance
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Assignments Tab -->
    <div x-show="tab === 'assignments'" x-cloak>
        <div class="mb-4 flex justify-end">
            <a href="{{ route('teacher.assignments.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                Create New Assignment
            </a>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Marks</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($batch->assignments as $assignment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment->due_date->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $assignment->max_marks }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('teacher.assignments.show', $assignment) }}" class="text-indigo-600 hover:text-indigo-900">View / Grade</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No assignments created for this batch.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
