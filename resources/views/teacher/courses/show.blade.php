@extends('layouts.teacher')

@section('title', $course->title . ' - Manage')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-5">
        <div>
            <a href="{{ route('teacher.courses') }}" class="text-[10px] font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-wider transition">← All Courses</a>
            <h1 class="text-xl font-bold text-gray-900 mt-1.5" style="font-family: var(--font-display);">{{ $course->title }}</h1>
            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 font-semibold">
                <span>{{ $course->subject }}</span> • <span>{{ $course->class_level }}</span>
                @php $sc = ['draft'=>'bg-gray-50 border-gray-200 text-gray-600','pending'=>'bg-yellow-50 border-yellow-200 text-yellow-700','published'=>'bg-emerald-50 border-emerald-200 text-emerald-700','rejected'=>'bg-red-50 border-red-200 text-red-700']; @endphp
                <span class="px-2 py-0.5 rounded-md border text-[9px] font-bold uppercase tracking-wider {{ $sc[$course->status] ?? '' }}">{{ $course->status }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.courses.edit', $course) }}"
               class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-200 rounded-lg shadow-sm text-xs font-bold text-gray-700 hover:bg-gray-50 transition uppercase tracking-wider">
                <svg class="w-3.5 h-3.5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Course
            </a>
            @if(in_array($course->status, ['draft', 'rejected']))
                <form method="POST" action="{{ route('teacher.courses.submit', $course) }}" class="m-0">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 bg-emerald-600 border border-transparent rounded-lg shadow-sm text-xs font-bold text-white hover:bg-emerald-700 transition uppercase tracking-wider"
                            onclick="return confirm('Ready to submit for admin review?')">
                        <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Submit for Review
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Lessons list --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Course Lessons ({{ $course->lessons->count() }})</h2>
                <a href="{{ route('teacher.courses.add-lesson', $course) }}"
                   class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition shadow-sm">
                    Add Lesson
                </a>
            </div>

            @if($course->lessons->isEmpty())
                <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
                    <p class="text-sm font-semibold text-gray-800">No lessons yet. Add your first lesson.</p>
                    <a href="{{ route('teacher.courses.add-lesson', $course) }}"
                       class="mt-4 inline-block text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition shadow-sm">
                        Add First Lesson
                    </a>
                </div>
            @else
                <div class="space-y-3.5">
                    @foreach($course->lessons as $i => $lesson)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4.5 transition duration-200 hover:border-emerald-500/10">
                            <div class="flex items-start gap-4.5">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 font-extrabold text-[10px] flex-shrink-0">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-xs text-gray-900 leading-snug" style="font-family: var(--font-display);">{{ $lesson->title }}</p>
                                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-1 leading-relaxed">{{ $lesson->description }}</p>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($lesson->contents as $content)
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-gray-50 border border-gray-150 text-gray-600 px-2.5 py-0.5 rounded-md">
                                                {{ $content->type }}
                                            </span>
                                        @endforeach
                                        @if($lesson->contents->isEmpty())
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-amber-50 border border-amber-200/50 text-amber-700 px-2.5 py-0.5 rounded-md">No content blocks</span>
                                        @endif
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('teacher.courses.destroy-lesson', [$course, $lesson]) }}"
                                      onsubmit="return confirm('Remove this lesson?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[10px] font-bold uppercase tracking-wider text-red-500 hover:text-red-700 px-2.5 py-1.5 bg-red-50 border border-red-150 rounded-md transition">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Course info sidebar --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                     class="w-full h-40 object-cover border-b border-gray-200">
                <div class="p-5 space-y-4 text-xs font-semibold">
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-400 font-bold uppercase tracking-wider">Price</span>
                        <span class="font-extrabold text-emerald-600 text-sm">{{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-400 font-bold uppercase tracking-wider">Lessons</span>
                        <span class="font-extrabold text-gray-900">{{ $course->lessons->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <span class="text-gray-400 font-bold uppercase tracking-wider">Enrolled</span>
                        <span class="font-extrabold text-gray-900">{{ $course->enrollments->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 font-bold uppercase tracking-wider">Status</span>
                        <span class="font-bold uppercase tracking-wider text-gray-800 text-[10px] bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-md">{{ $course->status }}</span>
                    </div>
                </div>
            </div>

            @if($course->status === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-xl p-4.5 text-xs text-red-700 leading-relaxed">
                    <p class="font-bold uppercase tracking-wider">Course Rejected</p>
                    <p class="mt-1 font-medium">Edit the course content and resubmit for administrative review.</p>
                </div>
            @elseif($course->status === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4.5 text-xs text-yellow-700 leading-relaxed">
                    <p class="font-bold uppercase tracking-wider">Under Review</p>
                    <p class="mt-1 font-medium">Nabha Learning administration is currently reviewing your course.</p>
                </div>
            @elseif($course->status === 'published')
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4.5 text-xs text-emerald-700 leading-relaxed">
                    <p class="font-bold uppercase tracking-wider">Live & Published</p>
                    <p class="mt-1 font-medium">Students can now enroll in this course directly from their portal.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
