@extends('layouts.student')

@section('title', $course->title . ' — Nabha Learning')

@section('student-content')
@php
    use Illuminate\Support\Str;

    /* ── Mock rating (deterministic, seeded by course ID) ── */
    $rating      = $course->mock_rating;
    $reviewCount = $course->mock_review_count;
    $fullStars   = (int) floor($rating);
    $halfStar    = ($rating - $fullStars) >= 0.5;
    $emptyStars  = 5 - $fullStars - ($halfStar ? 1 : 0);

    /* ── Mock reviews ── */
    $mockReviews = collect([
        ['name' => 'Priya Sharma',   'avatar' => 'PS', 'rating' => 5, 'date' => '2 weeks ago',
         'text'  => 'Absolutely fantastic course! The explanations are crystal-clear and the content is well-structured. I could follow along easily even as a beginner.'],
        ['name' => 'Arjun Mehta',    'avatar' => 'AM', 'rating' => 4, 'date' => '1 month ago',
         'text'  => 'Great content overall. The lessons are engaging and the teacher explains concepts very well. Would have loved a few more practice exercises.'],
        ['name' => 'Divya Nair',     'avatar' => 'DN', 'rating' => 5, 'date' => '3 weeks ago',
         'text'  => 'This course exceeded my expectations! The way topics are broken down made it very easy to understand. Highly recommend to anyone.'],
        ['name' => 'Rahul Verma',    'avatar' => 'RV', 'rating' => 4, 'date' => '2 months ago',
         'text'  => 'Solid course with great depth. Took detailed notes on every lesson. The instructor is clearly an expert in this subject.'],
        ['name' => 'Sneha Patel',    'avatar' => 'SP', 'rating' => 5, 'date' => '5 days ago',
         'text'  => 'One of the best learning experiences I\'ve had. Everything is well-explained and the pacing is just right. 10/10!'],
    ])->slice($course->id % 3, 3)->values(); // show 3 reviews, offset by course id
@endphp

<div class="space-y-6 animate-fade-in" x-data="{ payModal: false }">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-white/40">
        <a href="{{ route('student.courses') }}" class="hover:text-violet-400 transition">Courses</a>
        <span class="text-white/20">›</span>
        <span class="text-white/80 font-medium truncate">{{ Str::limit($course->title, 50) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left column ── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Hero banner --}}
            <div class="relative rounded-2xl overflow-hidden border border-white/[0.08] shadow-lg shadow-violet-500/5" style="height:260px;">
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                     class="w-full h-full object-cover scale-100 hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 space-y-3">
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-violet-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">{{ $course->subject }}</span>
                        <span class="bg-white/10 text-white/90 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider backdrop-blur-md border border-white/10">{{ $course->class_level }}</span>
                        @if($course->isFree())
                            <span class="bg-emerald-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider">FREE</span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-white/90 leading-snug" style="font-family: var(--font-display);">{{ $course->title }}</h1>

                    {{-- Inline rating on hero --}}
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex items-center gap-0.5 text-amber-400">
                            @for($i = 0; $i < $fullStars; $i++)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                            @if($halfStar)
                                <svg class="w-4 h-4 opacity-70" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                            @for($i = 0; $i < $emptyStars; $i++)
                                <svg class="w-4 h-4 text-white/20" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-white font-bold text-xs">{{ $rating }}</span>
                        <span class="text-white/40 text-[10px]">({{ number_format($reviewCount) }} ratings)</span>
                    </div>
                </div>
            </div>

            {{-- Stats strip --}}
            <div class="glass-card px-5 py-4">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">📚</span>
                        <div>
                            <div class="font-bold text-white/90 text-sm">{{ $course->lessons->count() }}</div>
                            <div class="text-[10px] text-white/40 uppercase font-semibold">Lessons</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">⏱</span>
                        <div>
                            <div class="font-bold text-white/90 text-sm">{{ $course->total_duration }}</div>
                            <div class="text-[10px] text-white/40 uppercase font-semibold">Duration</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">👥</span>
                        <div>
                            <div class="font-bold text-white/90 text-sm">{{ $course->enrollments->count() }}</div>
                            <div class="text-[10px] text-white/40 uppercase font-semibold">Students</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">⭐</span>
                        <div>
                            <div class="font-bold text-white/90 text-sm">{{ $rating }}</div>
                            <div class="text-[10px] text-white/40 uppercase font-semibold">Rating</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-lg">🌐</span>
                        <div>
                            <div class="font-bold text-white/90 text-sm">English</div>
                            <div class="text-[10px] text-white/40 uppercase font-semibold">Language</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- About --}}
            <div class="glass-card p-6">
                <h2 class="font-bold text-white/90 mb-3 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">About this Course</h2>
                <p class="text-white/70 text-xs leading-relaxed">{{ $course->description }}</p>
            </div>

            {{-- Instructor card --}}
            <div class="glass-card p-6">
                <h2 class="font-bold text-white/90 mb-4 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">Your Instructor</h2>
                <div class="flex items-center gap-4">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->teacher->name }}"
                         class="w-14 h-14 rounded-2xl object-cover ring-2 ring-violet-500/20">
                    <div>
                        <p class="font-bold text-white/90 text-sm">{{ $course->teacher->name }}</p>
                        <p class="text-xs text-violet-400 font-semibold mt-0.5">{{ $course->teacher->subject_specialization ?? 'Teacher' }}</p>
                        <p class="text-[10px] text-white/40 mt-1 font-medium">{{ $course->teacher->school ?? 'Nabha School' }}</p>
                    </div>
                </div>
            </div>

            {{-- Curriculum --}}
            <div class="glass-card p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between">
                    <h2 class="font-bold text-white/90 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">📋 Course Curriculum</h2>
                    <span class="text-xs text-white/40">{{ $course->lessons->count() }} lessons</span>
                </div>

                @if($course->lessons->isEmpty())
                    <div class="p-8 text-center text-white/30">
                        <p class="text-xs">No lessons added yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-white/[0.04]">
                        @foreach($course->lessons as $i => $lesson)
                            <div class="flex items-center gap-4 px-6 py-4 {{ $isEnrolled ? 'hover:bg-white/[0.02] group transition-all duration-200' : '' }}">
                                {{-- Number / Play --}}
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-xs font-bold transition-all
                                            {{ $isEnrolled ? 'bg-violet-500/20 text-violet-300 group-hover:bg-violet-600 group-hover:text-white' : 'bg-white/[0.04] text-white/30' }}">
                                    {{ $isEnrolled ? '▶' : ($i + 1) }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    @if($isEnrolled)
                                        <a href="{{ route('student.courses.lesson', [$course, $lesson]) }}"
                                           class="font-semibold text-xs text-white/90 group-hover:text-violet-400 transition block truncate" style="font-family: var(--font-display);">
                                            {{ $lesson->title }}
                                        </a>
                                    @else
                                        <span class="font-semibold text-xs text-white/40 block truncate" style="font-family: var(--font-display);">{{ $lesson->title }}</span>
                                    @endif
                                    <p class="text-[10px] text-white/40 truncate mt-0.5">{{ Str::limit($lesson->description, 60) }}</p>
                                </div>

                                <div class="flex items-center gap-3 flex-shrink-0">
                                    @if($lesson->duration_minutes)
                                        <span class="text-[10px] text-white/30">{{ $lesson->duration_minutes }}m</span>
                                    @endif
                                    @if($isEnrolled)
                                        <span class="text-violet-400 text-xs font-bold uppercase tracking-wider group-hover:text-violet-300 transition">Open →</span>
                                    @else
                                        <svg class="w-4 h-4 text-white/20" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Reviews --}}
            <div class="glass-card p-0 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/[0.06] flex items-center justify-between">
                    <h2 class="font-bold text-white/90 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">⭐ Student Reviews</h2>

                    {{-- Summary rating bar --}}
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-extrabold text-white/90">{{ $rating }}</span>
                        <div>
                            <div class="flex items-center gap-0.5 text-amber-400">
                                @for($i = 0; $i < $fullStars; $i++)
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                                @if($halfStar)
                                    <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                                @for($i = 0; $i < $emptyStars; $i++)
                                    <svg class="w-3 h-3 text-white/20" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-[10px] text-white/40 mt-0.5">{{ number_format($reviewCount) }} reviews</p>
                        </div>
                    </div>
                </div>

                <div class="divide-y divide-white/[0.04]">
                    @foreach($mockReviews as $review)
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-md">
                                    {{ $review['avatar'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-xs text-white/90" style="font-family: var(--font-display);">{{ $review['name'] }}</span>
                                        <span class="text-[10px] text-white/40 font-medium">{{ $review['date'] }}</span>
                                    </div>
                                    {{-- Stars --}}
                                    <div class="flex items-center gap-0.5 mt-1 text-amber-400">
                                        @for($s = 0; $s < $review['rating']; $s++)
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        @for($s = $review['rating']; $s < 5; $s++)
                                            <svg class="w-3 h-3 text-white/20" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <p class="text-xs text-white/70 mt-2 leading-relaxed">{{ $review['text'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Right column ── --}}
        <div class="lg:col-span-1">
            <div class="glass-card p-0 overflow-hidden sticky top-20 glow-violet">

                {{-- Thumbnail --}}
                <div class="relative h-36 overflow-hidden border-b border-white/[0.06]">
                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/45"></div>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Price --}}
                    <div class="text-center">
                        @if($course->isFree())
                            <div class="text-2xl font-extrabold text-emerald-400">Free</div>
                        @else
                            <div class="text-2xl font-extrabold text-white/95">₹{{ number_format($course->price, 2) }}</div>
                            <p class="text-[10px] text-white/40 mt-0.5 uppercase tracking-wider font-semibold">One-time purchase</p>
                        @endif
                    </div>

                    {{-- CTA --}}
                    @if($isEnrolled)
                        @php
                            $completedCount = \App\Models\ProgressReport::where('student_id', auth()->id())
                                ->whereIn('lesson_id', $course->lessons->pluck('id'))
                                ->where('is_completed', true)
                                ->count();
                            $isCourseComplete = ($course->lessons->count() > 0 && $completedCount === $course->lessons->count());
                        @endphp

                        @if($isCourseComplete)
                            <div class="bg-emerald-500/10 border border-emerald-500/15 rounded-xl p-4 text-center space-y-3">
                                <p class="text-emerald-300 font-bold text-base" style="font-family: var(--font-display);">🎉 Course Completed!</p>
                                <p class="text-[11px] text-emerald-400/80 leading-relaxed">You have successfully finished all lessons in this course.</p>
                                
                                <div class="flex flex-col gap-2 pt-2">
                                    <form method="POST" action="{{ route('student.courses.reset', $course) }}" onsubmit="return confirm('This will clear all your progress for this course. Are you sure?')">
                                        @csrf
                                        <button type="submit" class="w-full bg-white/[0.04] border border-white/[0.08] text-white/90 hover:bg-white/[0.08] py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all">
                                            🔄 Restart Course Again
                                        </button>
                                    </form>
                                    <a href="{{ route('student.courses.lesson', [$course, $course->lessons->first()]) }}" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:from-emerald-500 hover:to-teal-500 transition-all text-center">
                                        📖 Review Lessons
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="bg-violet-500/10 border border-violet-500/15 rounded-xl px-4 py-3 text-center">
                                <p class="text-violet-300 font-bold text-xs uppercase tracking-wider">✅ Enrolled</p>
                            </div>
                            @if($course->lessons->isNotEmpty())
                                @php
                                    $target = $resumeLesson ?? $course->lessons->first();
                                @endphp
                                <a href="{{ route('student.courses.lesson', [$course, $target]) }}"
                                   class="block w-full text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20">
                                    {{ $resumeLesson ? '▶ Resume Learning' : 'Start Learning →' }}
                                </a>
                                @if($resumeLesson && $resumeLesson->id !== $course->lessons->first()->id)
                                    <p class="text-center text-[10px] text-white/40 -mt-2 truncate">
                                        Picking up at <em>{{ Str::limit($resumeLesson->title, 30) }}</em>
                                    </p>
                                @endif
                            @endif
                        @endif
                    @elseif($course->isFree())
                        <form method="POST" action="{{ route('student.courses.enroll', $course) }}" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            <button type="submit"
                                    :disabled="submitting"
                                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2">
                                <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-show="!submitting">🎓 Enroll for Free</span>
                                <span x-show="submitting" x-cloak>Processing...</span>
                            </button>
                        </form>
                    @else
                        {{-- Paid, not enrolled --}}
                        @if($inCart)
                            <a href="{{ route('student.cart') }}"
                               class="block w-full text-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md">
                                🛒 Go to Cart
                             </a>
                            <form method="POST" action="{{ route('student.cart.remove', $course) }}" x-data="{ submitting: false }" @submit="submitting = true">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        :disabled="submitting"
                                        class="w-full text-xs text-white/40 hover:text-red-400 py-2 transition disabled:opacity-60 uppercase font-bold tracking-wider">
                                    <span x-show="!submitting">Remove from cart</span>
                                    <span x-show="submitting" x-cloak>Removing...</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('student.cart.add', $course) }}" x-data="{ submitting: false }" @submit="submitting = true">
                                @csrf
                                <button type="submit"
                                        :disabled="submitting"
                                        class="w-full bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-md flex items-center justify-center gap-2">
                                    <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span x-show="!submitting">🛒 Add to Cart</span>
                                    <span x-show="submitting" x-cloak>Adding...</span>
                                </button>
                            </form>
                        @endif
                        <button @click="payModal = true"
                                class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20">
                            💳 Buy Now — ₹{{ number_format($course->price, 2) }}
                        </button>
                    @endif

                    {{-- Course includes --}}
                    <div class="border-t border-white/[0.06] pt-4 space-y-3 text-xs text-white/60">
                        <p class="text-[9px] font-bold text-white/35 uppercase tracking-wider">This course includes</p>
                        <div class="flex items-center gap-2.5"><span>📚</span> {{ $course->lessons->count() }} lessons</div>
                        <div class="flex items-center gap-2.5"><span>⏱</span> {{ $course->total_duration }}</div>
                        <div class="flex items-center gap-2.5"><span>🎓</span> {{ $course->class_level }}</div>
                        <div class="flex items-center gap-2.5"><span>📖</span> {{ $course->subject }}</div>
                        <div class="flex items-center gap-2.5"><span>♾️</span> Lifetime access</div>
                        <div class="flex items-center gap-2.5"><span>⭐</span> {{ $rating }} course rating</div>
                        @if(!$course->isFree())
                            <div class="flex items-center gap-2.5"><span>🏆</span> Certificate of completion</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Payment Modal ── --}}
    @if(!$course->isFree() && !$isEnrolled)
    <div x-show="payModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" @click="payModal = false"></div>

        <div class="relative glass-card border border-white/[0.08] p-0 w-full max-w-md z-10 glow-violet overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center justify-between px-6 py-4 border-b border-white/[0.06]">
                <div>
                    <h3 class="font-bold text-white/95 text-sm uppercase tracking-wider" style="font-family: var(--font-display);">Complete Purchase</h3>
                    <p class="text-[10px] text-white/40 mt-0.5">Simulated payment — no real charges</p>
                </div>
                <button @click="payModal = false" class="text-white/40 hover:text-white/70 transition p-1.5 rounded-lg hover:bg-white/[0.04]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mx-6 mt-4 bg-white/[0.02] border border-white/[0.06] rounded-xl p-3.5 flex items-center justify-between">
                <div>
                    <p class="text-[10px] text-white/40 font-medium">Enrolling in</p>
                    <p class="font-semibold text-violet-300 text-xs truncate max-w-[200px]" style="font-family: var(--font-display);">{{ Str::limit($course->title, 40) }}</p>
                </div>
                <p class="text-lg font-extrabold text-white/90">₹{{ number_format($course->price, 2) }}</p>
            </div>

            <form method="POST" action="{{ route('student.courses.purchase', $course) }}"
                  class="px-6 pb-6 pt-4 space-y-4"
                  x-data="paymentForm()"
                  @submit="loading = true">
                @csrf

                {{-- Payment method selector --}}
                <div>
                    <p class="text-[10px] font-bold text-white/40 mb-2 uppercase tracking-wider">Payment Method</p>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-2.5 border rounded-xl px-4 py-3.5 cursor-pointer transition"
                               :class="method === 'card' ? 'border-violet-500/50 bg-violet-500/10 text-violet-300' : 'border-white/[0.08] hover:border-white/[0.12] text-white/50'">
                            <input type="radio" name="payment_method" value="card" x-model="method" class="accent-violet-500">
                            <span class="text-sm font-bold uppercase tracking-wider">Card</span>
                        </label>
                        <label class="flex items-center justify-center gap-2.5 border rounded-xl px-4 py-3.5 cursor-pointer transition"
                               :class="method === 'upi' ? 'border-violet-500/50 bg-violet-500/10 text-violet-300' : 'border-white/[0.08] hover:border-white/[0.12] text-white/50'">
                            <input type="radio" name="payment_method" value="upi" x-model="method" class="accent-violet-500">
                            <span class="text-sm font-bold uppercase tracking-wider">UPI</span>
                        </label>
                    </div>
                </div>

                {{-- Card fields --}}
                <div x-show="method === 'card'" x-transition class="space-y-3.5">
                    <div>
                        <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Cardholder Name</label>
                        <input type="text" name="card_name" x-model="name"
                               placeholder="Name on card"
                               class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Card Number</label>
                        <div class="relative">
                            <input type="text" x-model="cardDisplay"
                                   @input="formatCard($event)"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 pr-12 tracking-widest transition text-xs">
                            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-white/30 text-base">💳</div>
                        </div>
                        <input type="hidden" name="card_number" :value="cardRaw">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">Expiry</label>
                            <input type="text" name="card_expiry" @input="formatExpiry($event)"
                                   placeholder="MM/YY" maxlength="5"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 tracking-widest transition text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-white/50 mb-1.5 uppercase tracking-wider">CVV</label>
                            <input type="text" name="card_cvv" placeholder="•••" maxlength="4"
                                   class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 tracking-widest transition text-xs">
                        </div>
                    </div>
                </div>

                {{-- UPI section --}}
                <div x-show="method === 'upi'" x-transition class="space-y-3">
                    <div class="flex flex-col items-center gap-3 py-2">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?data=upi://pay?pa=8247592083@axl%26am={{ $course->price }}%26cu=INR&size=180x180&bgcolor=ffffff&color=4f46e5&margin=8"
                             alt="UPI QR Code"
                             class="w-44 h-44 rounded-2xl border border-white/[0.08] shadow-md p-1.5 bg-white">
                        <div class="text-center">
                            <p class="text-[10px] text-white/40 mb-0.5">Scan to Pay</p>
                            <p class="text-lg font-extrabold text-violet-400">₹{{ number_format($course->price, 2) }}</p>
                        </div>
                        <div class="bg-white/[0.02] border border-white/[0.06] rounded-xl px-5 py-3 text-center w-full">
                            <p class="text-[10px] text-white/40 mb-0.5">UPI ID</p>
                            <p class="text-xs font-bold text-violet-300 tracking-wide select-all">8247592083@axl</p>
                        </div>
                    </div>
                    <input type="hidden" name="upi_id" value="8247592083@axl">
                    <div class="bg-blue-500/10 border border-blue-500/15 rounded-xl p-3 text-[11px] text-blue-300 leading-relaxed">
                        Scan the QR code or use the UPI ID above to complete payment. This is a simulated demo — no real money is charged.
                    </div>
                </div>

                <div class="bg-amber-500/10 border border-amber-500/15 rounded-xl p-3 text-[11px] text-amber-300 flex gap-2 leading-relaxed">
                    <span class="flex-shrink-0">⚠️</span>
                    <span><strong>Demo only.</strong> No real money is charged.</span>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-violet-500/20">
                    <svg x-show="loading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="!loading" x-text="method === 'upi' ? '📱 Pay via UPI & Enroll' : '🔒 Pay ₹{{ number_format($course->price, 2) }} & Enroll'"></span>
                    <span x-show="loading" x-cloak>Processing...</span>
                </button>

                <p class="text-center text-[10px] text-white/35">🔒 Secured simulation</p>
            </form>
        </div>
    </div>
    @endif

</div>

@if($errors->hasAny(['card_number','card_expiry','card_cvv','card_name','upi_id','payment_method']))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[x-data]');
            if (root && root._x_dataStack) root._x_dataStack[0].payModal = true;
        });
    </script>
    @endpush
@endif

@push('scripts')
<script>
function paymentForm() {
    return {
        method: '{{ old("payment_method", "card") }}',
        loading: false,
        name: '',
        cardDisplay: '',
        cardRaw: '',
        formatCard(e) {
            const digits = e.target.value.replace(/\D/g, '').slice(0, 16);
            this.cardRaw     = digits;
            this.cardDisplay = digits.replace(/(.{4})/g, '$1 ').trim();
            e.target.value   = this.cardDisplay;
        },
        formatExpiry(e) {
            let v = e.target.value.replace(/\D/g, '').slice(0, 4);
            if (v.length >= 3) v = v.slice(0, 2) + '/' + v.slice(2);
            e.target.value = v;
        },
    };
}
</script>
@endpush
@endsection
