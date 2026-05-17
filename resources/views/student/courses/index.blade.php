@extends('layouts.student')

@section('title', 'Courses - Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">

    {{-- Page header --}}
    <div class="relative bg-gradient-to-r from-violet-800/40 to-indigo-800/40 rounded-2xl p-6 text-white overflow-hidden border border-white/[0.06] shadow-lg shadow-violet-500/5">
        <div class="absolute -right-16 -top-16 w-44 h-44 bg-violet-500/20 rounded-full blur-2xl"></div>
        <div class="relative z-10 space-y-1">
            <h1 class="text-xl font-bold tracking-tight" style="font-family: var(--font-display);">Explore Courses</h1>
            <p class="text-white/60 text-xs font-medium">Learn from expert teachers at your own pace</p>
        </div>
    </div>

    {{-- Search + Filters --}}
    <form method="GET" action="{{ route('student.courses') }}" data-no-loading
          class="glass-card p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search courses, subjects..."
                       class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs">
            </div>

            <select name="subject"
                    class="px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/80 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs">
                <option value="" class="bg-gray-900 text-white">All Subjects</option>
                @foreach($subjects as $s)
                    <option value="{{ $s }}" {{ request('subject') === $s ? 'selected':'' }} class="bg-gray-900 text-white">{{ $s }}</option>
                @endforeach
            </select>

            <select name="class_level"
                    class="px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/80 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs">
                <option value="" class="bg-gray-900 text-white">All Classes</option>
                @foreach($classLevels as $cl)
                    <option value="{{ $cl }}" {{ request('class_level') === $cl ? 'selected':'' }} class="bg-gray-900 text-white">{{ $cl }}</option>
                @endforeach
            </select>

            <button type="submit"
                    class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md">
                Search
            </button>
            @if(request()->hasAny(['search','subject','class_level']))
                <a href="{{ route('student.courses') }}"
                   class="flex items-center justify-center px-4 py-2.5 bg-white/[0.06] border border-white/[0.08] rounded-xl text-xs text-white/80 font-bold uppercase tracking-wider hover:bg-white/[0.1] transition-all">
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Results count --}}
    @if($courses->total() > 0)
        <p class="text-xs text-white/40">Showing {{ $courses->firstItem() }}–{{ $courses->lastItem() }} of {{ $courses->total() }} courses</p>
    @endif

    @if($courses->isEmpty())
        <div class="glass-card p-16 text-center glow-violet animate-scale-in">
            <h3 class="text-base font-bold text-white/90 mb-2" style="font-family: var(--font-display);">No courses found</h3>
            <p class="text-white/40 text-xs mb-5 max-w-sm mx-auto">Try adjusting your search or browse all courses.</p>
            <a href="{{ route('student.courses') }}"
               class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                Browse All
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($courses as $course)
                @php $isEnrolled = in_array($course->id, $enrolled); @endphp
                <div class="group glass-card overflow-hidden hover:border-violet-500/20 transition-all duration-300 relative">

                    {{-- Thumbnail --}}
                    <a href="{{ route('student.courses.show', $course) }}" class="block relative h-44 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                        {{-- Badges --}}
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            @if($course->isFree())
                                <span class="bg-emerald-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">FREE</span>
                            @endif
                            @if($isEnrolled)
                                <span class="bg-violet-600 text-white text-[9px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider">Enrolled</span>
                            @endif
                        </div>

                        {{-- Subject tag --}}
                        <div class="absolute bottom-3 left-3">
                            <span class="bg-white/10 backdrop-blur-md text-white/90 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider border border-white/10">
                                {{ $course->subject }}
                            </span>
                        </div>
                    </a>

                    {{-- Card body --}}
                    <div class="p-5 space-y-4">
                        <div>
                            <a href="{{ route('student.courses.show', $course) }}">
                                <h3 class="font-bold text-white/90 text-sm line-clamp-2 leading-snug group-hover:text-violet-400 transition" style="font-family: var(--font-display);">
                                    {{ $course->title }}
                                </h3>
                            </a>
                            <p class="text-xs text-white/40 mt-1 line-clamp-2 leading-relaxed">{{ $course->description }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <img src="{{ $course->teacher->avatar_url }}" alt="{{ $course->teacher->name }}"
                                 class="w-5 h-5 rounded-md object-cover ring-1 ring-white/10">
                            <span class="text-xs text-white/50">{{ $course->teacher->name }}</span>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-white/40">
                            <span>{{ $course->lessons_count }} lessons</span>
                            <span>&middot;</span>
                            <span>{{ $course->enrollments_count }} enrolled</span>
                            <span>&middot;</span>
                            <span>{{ $course->class_level }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-3.5 border-t border-white/[0.06]">
                            <span class="font-extrabold {{ $course->isFree() ? 'text-emerald-400' : 'text-white/90' }} text-base">
                                {{ $course->isFree() ? 'Free' : '₹' . number_format($course->price, 2) }}
                            </span>

                            @if($isEnrolled)
                                <a href="{{ route('student.courses.show', $course) }}"
                                   class="text-xs bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider transition-all shadow-md">
                                    Continue →
                                </a>
                            @else
                                <a href="{{ route('student.courses.show', $course) }}"
                                   class="text-xs bg-white/[0.06] hover:bg-white/[0.1] text-white/85 px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider transition-all">
                                    View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $courses->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
