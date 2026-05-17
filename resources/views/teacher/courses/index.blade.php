@extends('layouts.teacher')

@section('title', 'My Courses - Nabha Learning')

@section('teacher-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight" style="font-family: var(--font-display);">My Courses</h1>
            <p class="text-xs text-gray-500 mt-1">Create and manage your educational curriculum courses.</p>
        </div>
        <a href="{{ route('teacher.courses.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-lg transition text-xs font-bold uppercase tracking-wider shadow-sm">
            New Course
        </a>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-xl p-12 text-center border border-gray-200 shadow-sm">
            <h3 class="text-sm font-bold text-gray-800 mb-2">No Courses Yet</h3>
            <p class="text-xs text-gray-500 mb-5">Create your first course and start teaching!</p>
            <a href="{{ route('teacher.courses.create') }}"
               class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg transition font-bold text-xs uppercase tracking-wider">
                Create First Course
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($courses as $course)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden flex flex-col shadow-sm transition duration-300 hover:border-emerald-500/20">
                    <div class="h-36 bg-gradient-to-br from-indigo-900/10 to-violet-900/10 relative overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3">
                            @php
                                $sc = ['draft'=>'bg-gray-500/15 border-gray-500/20 text-gray-700','pending'=>'bg-yellow-500/15 border-yellow-500/20 text-yellow-700','published'=>'bg-emerald-500/15 border-emerald-500/20 text-emerald-700','rejected'=>'bg-red-500/15 border-red-500/20 text-red-700'];
                            @endphp
                            <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md border uppercase tracking-wider {{ $sc[$course->status] ?? 'bg-gray-500/15 text-gray-700 border-gray-500/20' }}">
                                {{ $course->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-4.5 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 line-clamp-1 leading-snug" style="font-family: var(--font-display);">{{ $course->title }}</h3>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 leading-relaxed">{{ $course->description }}</p>
                        </div>

                        <div class="mt-4 pt-3.5 border-t border-gray-100 flex items-center justify-between text-[10px] text-gray-400 font-bold uppercase tracking-wider">
                            <span>{{ $course->lessons_count }} lessons</span>
                            <span>{{ $course->enrollments_count }} enrolled</span>
                            <span class="font-extrabold text-emerald-600">
                                {{ $course->price > 0 ? '₹' . number_format($course->price, 2) : 'Free' }}
                            </span>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('teacher.courses.show', $course) }}"
                               class="flex-1 text-center text-xs bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 py-2 rounded-lg font-bold transition">
                                Manage
                            </a>
                            <a href="{{ route('teacher.courses.edit', $course) }}"
                               class="text-xs bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 px-3 py-2 rounded-lg font-bold transition">
                                Edit
                            </a>
                            @if($course->status === 'draft' || $course->status === 'rejected')
                                <form method="POST" action="{{ route('teacher.courses.submit', $course) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 text-emerald-700 px-3 py-2 rounded-lg font-bold transition"
                                            onclick="return confirm('Submit for admin review?')">
                                        Submit
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pt-4">{{ $courses->links() }}</div>
    @endif
</div>
@endsection
