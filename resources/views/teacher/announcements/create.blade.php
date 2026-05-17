@extends('layouts.teacher')

@section('teacher-content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    <div class="flex justify-between items-center pb-5 border-b border-gray-200">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">Post Announcement</h1>
            <p class="text-xs text-gray-500 mt-1">Share important updates or notices with your class.</p>
        </div>
        <a href="{{ route('teacher.announcements.index') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← Back</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('teacher.announcements.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Target Course</label>
                <select name="course_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                    <option value="">-- All Students --</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 font-semibold mt-1.5">If "All Students" is selected, this will be visible on the main notice board.</p>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Title *</label>
                <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g., Change in class schedule" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-800">
                @error('title') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">Message *</label>
                <textarea name="content" required rows="5" placeholder="Write your announcement here..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/30 text-xs font-medium text-gray-850 resize-y leading-relaxed">{{ old('content') }}</textarea>
                @error('content') <span class="text-red-500 text-xs mt-1.5 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider py-3 rounded-lg transition text-xs shadow-sm">
                    Post Announcement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
