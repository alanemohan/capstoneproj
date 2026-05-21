@extends('layouts.admin')

@section('title', 'Preview: ' . $course->title)

@push('styles')
<style>
    .content-iframe { height: 480px; }
    @media (max-width: 640px) { .content-iframe { height: 300px; } }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('admin-content')
@php
    use Illuminate\Support\Str;
    $statusMap = [
        'draft'     => ['bg' => 'bg-slate-50 border-slate-200 text-slate-600',    'dot' => 'bg-slate-400'],
        'pending'   => ['bg' => 'bg-yellow-50 border-yellow-250 text-yellow-750', 'dot' => 'bg-yellow-400'],
        'published' => ['bg' => 'bg-emerald-50 border-emerald-250 text-emerald-700','dot'=> 'bg-emerald-500'],
        'rejected'  => ['bg' => 'bg-red-50 border-red-250 text-red-700',       'dot' => 'bg-red-500'],
    ];
    $s = $statusMap[$course->status] ?? $statusMap['draft'];
@endphp

<div class="space-y-6 max-w-5xl animate-fade-in text-slate-800" x-data="{ zoomSrc: null, zoomAlt: '' }">

    {{-- ── Image Zoom Modal ── --}}
    <div x-show="zoomSrc" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         @click.self="zoomSrc = null"
         x-transition>
         <div class="relative max-w-5xl w-full">
            <button @click="zoomSrc = null"
                    class="absolute -top-10 right-0 text-white hover:text-orange-500 text-xs font-bold uppercase tracking-wider">
                &times; Close
            </button>
            <img :src="zoomSrc" :alt="zoomAlt"
                 class="w-full max-h-[85vh] object-contain rounded-xl border border-slate-200 bg-white shadow-2xl">
         </div>
    </div>

    {{-- ── Top nav ── --}}
    <div class="flex items-center justify-between flex-wrap gap-4 pb-5 border-b border-slate-200">
        <a href="{{ route('admin.courses') }}"
           class="text-[10px] font-bold text-slate-400 hover:text-orange-500 uppercase tracking-wider transition">
            ← Back to Course Approval
        </a>

        <div class="flex items-center gap-2">
            @if($course->status !== 'published')
                <form method="POST" action="{{ route('admin.courses.approve', $course) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition shadow-sm">
                        Approve & Publish
                    </button>
                </form>
            @else
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 border border-emerald-250 px-3 py-1 rounded-md flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span> Published
                </span>
            @endif

            @if($course->status !== 'rejected')
                <form method="POST" action="{{ route('admin.courses.reject', $course) }}"
                      onsubmit="return confirm('Reject this course? The teacher will need to resubmit.')" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="bg-yellow-50 hover:bg-yellow-100 text-yellow-750 border border-yellow-150 text-[10px] font-bold uppercase tracking-wider px-4 py-2.5 rounded-lg transition">
                        Reject
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                  onsubmit="return confirm('Permanently delete this course? This cannot be undone.')" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-red-650 hover:text-red-750 hover:bg-red-50 bg-white border border-slate-200 text-[10px] font-bold uppercase tracking-wider px-3 py-2.5 rounded-lg transition shadow-sm">
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- ── Hero ── --}}
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="relative h-60 overflow-hidden">
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-900/35 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 p-6">
                <div class="flex flex-wrap gap-2 mb-2.5">
                    <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider {{ $s['bg'] }} flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full {{ $s['dot'] }}"></span>
                        {{ $course->status }}
                    </span>
                    <span class="text-[9px] font-bold px-2.5 py-0.5 border border-slate-200 rounded-md uppercase tracking-wider bg-white text-slate-800">
                        {{ $course->isFree() ? 'FREE' : '₹' . number_format($course->price, 2) }}
                    </span>
                    <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider bg-orange-500 text-white">
                        {{ $course->subject }}
                    </span>
                    <span class="text-[9px] font-bold px-2.5 py-0.5 rounded-md uppercase tracking-wider bg-white/20 text-white backdrop-blur-sm">
                        {{ $course->class_level }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-white tracking-tight" style="font-family: var(--font-display);">{{ $course->title }}</h1>
                <div class="flex items-center gap-2.5 mt-3 text-white/90 text-[11px] font-semibold">
                    <img src="{{ $course->teacher->avatar_url }}" class="w-6 h-6 rounded-full object-cover border border-white/30">
                    <span>{{ $course->teacher->name }}</span>
                    <span class="opacity-40">·</span>
                    <span class="text-white/70">{{ $course->teacher->subject_specialization ?? 'Teacher' }}</span>
                </div>
            </div>
        </div>

        {{-- Meta bar --}}
        <div class="flex flex-wrap gap-6 px-6 py-3.5 bg-slate-50/50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <span>{{ $course->lessons->count() }} lessons</span>
            <span>{{ $course->enrollments->count() }} enrolled</span>
            <span>Submitted <span class="normal-case font-semibold text-[11px]">{{ $course->created_at->format('d M Y') }}</span></span>
            @if($course->approved_at)
                <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 border border-emerald-250 rounded-md">Approved <span class="normal-case font-semibold text-[11px]">{{ $course->approved_at->format('d M Y') }}</span></span>
            @endif
            @if($course->approvedBy)
                <span>by {{ $course->approvedBy->name }}</span>
            @endif
        </div>

        {{-- Description --}}
        <div class="px-6 py-5 border-b border-slate-200">
            <h2 class="text-[9px] font-bold text-slate-450 uppercase tracking-widest mb-2">Course Description</h2>
            <p class="text-slate-700 text-xs leading-relaxed font-medium">{{ $course->description }}</p>
        </div>

        {{-- ── Lessons ── --}}
        <div class="px-6 py-5" x-data="{ openLesson: 0 }">
            <h2 class="font-bold text-slate-900 text-sm tracking-tight mb-4 flex items-center gap-2" style="font-family: var(--font-display);">
                Lessons
                <span class="text-xs font-normal text-slate-400">({{ $course->lessons->count() }} total)</span>
            </h2>

            @if($course->lessons->isEmpty())
                <div class="flex flex-col items-center py-12 text-slate-400 text-xs font-semibold">
                    <p>No lessons added to this course yet.</p>
                    <p class="text-[10px] mt-1 normal-case text-slate-400/80 font-medium">The teacher needs to add lessons before this can be approved.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($course->lessons as $i => $lesson)
                        <div class="border border-slate-200 rounded-lg overflow-hidden transition hover:border-orange-500/10 shadow-sm">

                            {{-- Accordion header --}}
                            <button type="button"
                                    @click="$dispatch('toggle-lesson', {{ $i }}); openLesson = (openLesson === {{ $i }}) ? -1 : {{ $i }}"
                                    class="w-full flex items-center gap-4 px-5 py-4 text-left transition-colors"
                                    :class="openLesson === {{ $i }} ? 'bg-orange-50/15' : 'bg-white hover:bg-slate-50/40'">

                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0 transition-colors"
                                     :class="openLesson === {{ $i }} ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-700'">
                                    {{ $i + 1 }}
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm text-slate-900 leading-snug" style="font-family: var(--font-display);">{{ $lesson->title }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-[10px] font-semibold text-slate-400">
                                        <span class="line-clamp-1 flex-1">{{ $lesson->description }}</span>
                                        @if($lesson->duration_minutes)
                                            <span>{{ $lesson->duration_minutes }}m</span>
                                        @endif
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-md border uppercase tracking-wider
                                            {{ $lesson->status === 'published' ? 'bg-emerald-50 border-emerald-250 text-emerald-700'
                                             : ($lesson->status === 'pending'  ? 'bg-yellow-50 border-yellow-250 text-yellow-750'
                                             : 'bg-slate-50 border-slate-200 text-slate-600') }}">
                                            {{ $lesson->status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @php
                                        $contentCount = $lesson->contents->count()
                                            + ($lesson->file_path ? 1 : 0)
                                            + ($lesson->content   ? 1 : 0);
                                    @endphp
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        {{ $contentCount }} block{{ $contentCount !== 1 ? 's' : '' }}
                                    </span>
                                    <span class="text-slate-400 transition-transform duration-200 text-[10px]"
                                          :class="openLesson === {{ $i }} ? 'rotate-180' : ''">▼</span>
                                </div>
                            </button>

                            {{-- Accordion body --}}
                            <div x-show="openLesson === {{ $i }}"
                                 x-transition
                                 x-cloak>

                                @php $hasContent = $lesson->contents->isNotEmpty() || $lesson->file_path || $lesson->content; @endphp

                                @if(!$hasContent)
                                    <div class="px-5 py-6 text-center text-slate-400 border-t border-slate-200 bg-white text-xs font-semibold">
                                        <p>No content blocks added to this lesson yet.</p>
                                    </div>
                                @else
                                    @foreach($lesson->contents as $j => $block)
                                        <div class="border-t border-slate-150 bg-white">
                                            <div class="flex items-center justify-between gap-3 px-5 py-3 bg-slate-50/50">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="text-xs font-bold text-slate-900" style="font-family: var(--font-display);">
                                                        {{ $block->title ?: ucfirst($block->type) . ' Content' }}
                                                    </span>
                                                    <span class="text-[9px] font-bold border border-slate-250 bg-white px-2 py-0.5 rounded-md uppercase tracking-wider text-slate-500">
                                                        {{ $block->type }}
                                                    </span>
                                                </div>
                                                @if(in_array($block->type, ['pdf','video','image']) && $block->file_path)
                                                    <a href="{{ $block->file_url }}" target="_blank"
                                                       class="text-[9px] font-bold uppercase tracking-wider text-orange-600 hover:underline flex-shrink-0">
                                                        ↗ Open in new tab
                                                    </a>
                                                @endif
                                            </div>

                                            <div class="px-5 py-4">
                                                @if($block->type === 'video')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="rounded-xl overflow-hidden bg-black shadow-inner">
                                                            <video controls preload="metadata"
                                                                   class="w-full" style="max-height:500px;"
                                                                   onerror="this.parentElement.innerHTML='<div class=\'p-6 text-center text-white text-xs font-semibold\'>Video file could not be loaded. <a href=\'{{ $block->file_url }}\' target=\'_blank\' class=\'underline\'>Try opening directly</a>.</div>'">
                                                                <source src="{{ $block->file_url }}">
                                                                <p class="text-white p-4 text-xs font-semibold">Your browser does not support HTML5 video.</p>
                                                            </video>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-slate-400 bg-slate-50 rounded-xl p-4 border border-dashed border-slate-250 font-semibold">
                                                            File not available — the video may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'pdf')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                                            <div class="flex items-center justify-end px-3 py-2 bg-slate-100 border-b border-slate-200">
                                                                <a href="{{ $block->file_url }}" target="_blank"
                                                                   class="text-[9px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-755 transition">
                                                                    ↗ Open in new tab
                                                                </a>
                                                            </div>
                                                            <iframe src="{{ $block->file_url }}"
                                                                    class="w-full content-iframe"
                                                                    title="{{ $block->title ?? 'PDF Document' }}">
                                                            </iframe>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-slate-400 bg-slate-50 rounded-xl p-4 border border-dashed border-slate-250 font-semibold">
                                                            File not available — the PDF may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'image')
                                                    @if($block->file_path && $block->file_url)
                                                        <div class="flex flex-col items-center justify-center">
                                                            <img src="{{ $block->file_url }}"
                                                                 alt="{{ $block->title ?? 'Image' }}"
                                                                 class="max-w-full max-h-[300px] rounded-xl border border-slate-200 object-contain shadow-sm cursor-zoom-in hover:opacity-95 transition"
                                                                 @click="zoomSrc = '{{ $block->file_url }}'; zoomAlt = '{{ addslashes($block->title ?? 'Image') }}'"
                                                                 title="Click to zoom">
                                                            <p class="text-center text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Click image to zoom</p>
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-slate-400 bg-slate-50 rounded-xl p-4 border border-dashed border-slate-250 font-semibold">
                                                            File not available — the image may not have been uploaded correctly.
                                                        </div>
                                                    @endif

                                                @elseif($block->type === 'text' && $block->content_text)
                                                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap font-medium">{{ $block->content_text }}</div>

                                                @else
                                                    <div class="text-xs text-slate-400 bg-slate-50 rounded-xl p-4 border border-dashed border-slate-250 font-semibold">
                                                        No previewable content for this block.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Legacy fallback --}}
                                    @if($lesson->contents->isEmpty())
                                        <div class="border-t border-slate-150 bg-white">
                                            <div class="flex items-center gap-3 px-5 py-3 bg-slate-50/50">
                                                <span class="text-xs font-bold text-slate-900 capitalize">
                                                    {{ $lesson->file_type ?? 'Text' }} Content
                                                </span>
                                                <span class="text-[9px] font-bold border border-amber-250 bg-amber-50 px-2 py-0.5 rounded-md uppercase tracking-wider text-amber-700">Legacy</span>
                                                @if($lesson->file_path)
                                                    <a href="{{ $lesson->file_url }}" target="_blank"
                                                       class="ml-auto text-[9px] font-bold uppercase tracking-wider text-orange-600 hover:underline flex-shrink-0">↗ Open</a>
                                                @endif
                                            </div>
                                            <div class="px-5 py-4">
                                                @if($lesson->file_type === 'video' && $lesson->file_path)
                                                    <div class="rounded-xl overflow-hidden bg-black shadow-inner">
                                                        <video controls preload="metadata" class="w-full" style="max-height:500px;"
                                                               onerror="this.parentElement.innerHTML='<div class=\'p-4 text-center text-white text-xs font-semibold\'>Video could not be loaded.</div>'">
                                                            <source src="{{ $lesson->file_url }}">
                                                        </video>
                                                    </div>
                                                @elseif($lesson->file_type === 'pdf' && $lesson->file_path)
                                                    <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                                        <div class="flex justify-end px-3 py-2 bg-slate-100 border-b border-slate-200">
                                                            <a href="{{ $lesson->file_url }}" target="_blank"
                                                               class="text-[9px] font-bold uppercase tracking-wider text-orange-600 hover:text-orange-755 font-medium">↗ Open in new tab</a>
                                                        </div>
                                                        <iframe src="{{ $lesson->file_url }}" class="w-full content-iframe"></iframe>
                                                    </div>
                                                @elseif($lesson->file_type === 'image' && $lesson->file_path)
                                                    <div class="flex flex-col items-center justify-center">
                                                        <img src="{{ $lesson->file_url }}" alt="{{ $lesson->title }}"
                                                             class="max-w-full max-h-[300px] rounded-xl border border-slate-200 object-contain cursor-zoom-in hover:opacity-95 transition"
                                                             @click="zoomSrc = '{{ $lesson->file_url }}'; zoomAlt = '{{ addslashes($lesson->title) }}'">
                                                        <p class="text-center text-[10px] text-slate-400 mt-2 font-bold uppercase tracking-wider">Click to zoom</p>
                                                    </div>
                                                @elseif($lesson->content)
                                                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap font-medium">{{ $lesson->content }}</div>
                                                @else
                                                    <div class="text-xs text-slate-400 bg-slate-50 rounded-xl p-4 border border-dashed border-slate-250 font-semibold">
                                                        File not available.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Bottom action bar ── --}}
    @if($course->status === 'pending')
    <div class="sticky bottom-4 bg-white rounded-xl border border-slate-200 p-4.5 flex items-center justify-between gap-4 shadow-lg animate-slide-up">
        <div>
            <p class="font-bold text-xs text-slate-900 uppercase tracking-wider">Pending Review</p>
            <p class="text-[11px] text-slate-500 font-semibold mt-0.5">This course is waiting for your decision.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.courses.approve', $course) }}" class="inline">
                @csrf @method('PATCH')
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition text-xs shadow-sm">
                    Approve & Publish
                </button>
            </form>
            <form method="POST" action="{{ route('admin.courses.reject', $course) }}"
                  onsubmit="return confirm('Reject this course?')" class="inline">
                @csrf @method('PATCH')
                <button type="submit"
                        class="bg-yellow-50 hover:bg-yellow-100 text-yellow-750 border border-yellow-150 font-bold uppercase tracking-wider px-5 py-2.5 rounded-lg transition text-xs">
                    Reject
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
