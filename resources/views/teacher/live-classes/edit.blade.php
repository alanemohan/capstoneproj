@extends('layouts.teacher')

@section('teacher-content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Edit Live Class</h1>
        <a href="{{ route('teacher.live-classes.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">← Back</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('teacher.live-classes.update', $liveClass) }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $liveClass->title) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Associated Course (Optional)</label>
                <select name="course_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- General Class --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $liveClass->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Link *</label>
                <input type="url" name="meeting_link" required value="{{ old('meeting_link', $liveClass->meeting_link) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                @error('meeting_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time *</label>
                    <input type="datetime-local" name="scheduled_at" required value="{{ old('scheduled_at', $liveClass->scheduled_at->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    @error('scheduled_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" required min="15" max="300" value="{{ old('duration_minutes', $liveClass->duration_minutes) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    @error('duration_minutes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="scheduled" {{ $liveClass->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="live" {{ $liveClass->status === 'live' ? 'selected' : '' }}>Live Now</option>
                    <option value="completed" {{ $liveClass->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $liveClass->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">{{ old('description', $liveClass->description) }}</textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 rounded-lg transition">
                    Update Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
