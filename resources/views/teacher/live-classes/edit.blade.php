@extends('layouts.teacher')

@section('teacher-content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <div class="flex justify-between items-center pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Edit Live Class</h1>
            <p class="text-xs text-gray-500 mt-1">Configure meeting link, date/time, and status.</p>
        </div>
        <a href="{{ route('teacher.live-classes.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('teacher.live-classes.update', $liveClass) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Title *</label>
                <input type="text" name="title" required value="{{ old('title', $liveClass->title) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808" placeholder="e.g., Biology Chapter 2 Revision">
                @error('title') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Associated Course (Optional)</label>
                <select name="course_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 bg-white">
                    <option value="">-- General Class --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $liveClass->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Meeting Link *</label>
                <input type="url" name="meeting_link" required value="{{ old('meeting_link', $liveClass->meeting_link) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808">
                @error('meeting_link') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Date & Time *</label>
                    <input type="datetime-local" name="scheduled_at" required value="{{ old('scheduled_at', $liveClass->scheduled_at->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808">
                    @error('scheduled_at') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" required min="15" max="300" value="{{ old('duration_minutes', $liveClass->duration_minutes) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-808">
                    @error('duration_minutes') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Status *</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 bg-white">
                    <option value="scheduled" {{ $liveClass->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="live" {{ $liveClass->status === 'live' ? 'selected' : '' }}>Live Now</option>
                    <option value="completed" {{ $liveClass->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $liveClass->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 resize-none leading-relaxed" placeholder="Optional details or agenda for the class...">{{ old('description', $liveClass->description) }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition text-xs shadow-sm">
                    Update Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
