@extends('layouts.student')

@section('title', $lesson->title . ' — ' . $course->title)

@section('student-content')
<div class="space-y-4 animate-fade-in">

    {{-- Breadcrumb + progress --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <nav class="flex items-center gap-2 text-xs text-white/40">
            <a href="{{ route('student.courses') }}" class="hover:text-violet-400 transition">Courses</a>
            <span class="text-white/20">›</span>
            <a href="{{ route('student.courses.show', $course) }}" class="hover:text-violet-400 transition truncate max-w-[160px]">{{ $course->title }}</a>
            <span class="text-white/20">›</span>
            <span class="text-white/80 truncate max-w-[160px]">{{ $lesson->title }}</span>
        </nav>
        <div class="flex items-center gap-2 text-[10px] text-white/40">
            <div class="flex items-center gap-1.5">
                <div class="w-24 h-1.5 bg-white/[0.06] rounded-full overflow-hidden">
                    @php $pct = $allLessons->count() > 0 ? round((($currentIdx + 1) / $allLessons->count()) * 100) : 0; @endphp
                    <div class="h-full bg-violet-500 rounded-full" style="width:{{ $pct }}%"></div>
                </div>
                <span class="font-bold text-white/60">{{ $currentIdx + 1 }} / {{ $allLessons->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-start">

        {{-- ── Main content ── --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Lesson header card --}}
            <div class="glass-card p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-base font-bold text-white/95" style="font-family: var(--font-display);">{{ $lesson->title }}</h1>
                        <p class="text-white/40 text-xs mt-1 leading-relaxed">{{ $lesson->description }}</p>
                    </div>
                    @if($lesson->duration_minutes)
                        <span class="text-[10px] bg-white/[0.04] border border-white/[0.06] text-white/60 px-3 py-1 rounded-md whitespace-nowrap flex-shrink-0 font-bold uppercase tracking-wider">
                            ⏱ {{ $lesson->duration_minutes }} min
                        </span>
                    @endif
                </div>
                @if($lesson->contents->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-white/[0.06]">
                        @foreach($lesson->contents as $c)
                            <span class="text-[9px] bg-violet-500/15 text-violet-300 px-2.5 py-1 rounded-md font-bold uppercase tracking-wider">
                                {{ $c->icon }} {{ ucfirst($c->type) }}{{ $c->title ? ' — ' . $c->title : '' }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Content blocks --}}
            @php
                $hasContent = $lesson->contents->isNotEmpty() || $lesson->file_path || $lesson->content;
            @endphp

            @if(!$hasContent)
                <div class="glass-card p-12 text-center">
                    <div class="text-5xl mb-3">📭</div>
                    <p class="text-white/40 text-xs">No content has been added to this lesson yet.</p>
                </div>
            @else

                {{-- Multi-content blocks (new system) --}}
                @foreach($lesson->contents as $idx => $block)
                    <div class="glass-card p-0 overflow-hidden">

                        {{-- Block header --}}
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/[0.06] bg-white/[0.01]">
                            <span class="text-lg">{{ $block->icon }}</span>
                            <div>
                                <p class="font-bold text-xs text-white/95" style="font-family: var(--font-display);">
                                    {{ $block->title ?: ucfirst($block->type) . ' Content' }}
                                </p>
                                <p class="text-[10px] text-white/40 capitalize font-medium">{{ $block->type }}</p>
                            </div>
                            @if(in_array($block->type, ['pdf','video','image']) && $block->file_path)
                                <a href="{{ $block->file_url }}" target="_blank"
                                   class="ml-auto text-[10px] text-violet-400 hover:text-violet-300 font-bold uppercase tracking-wider flex items-center gap-1">
                                    ↗ Open
                                </a>
                            @endif
                        </div>

                        {{-- Block content --}}
                        <div class="p-5">
                            @if($block->type === 'pdf' && $block->file_path)
                                <div class="rounded-xl overflow-hidden border border-white/[0.08] bg-black/40">
                                    <iframe src="{{ $block->file_url }}" class="w-full"
                                            style="height:70vh; min-height:420px;" title="{{ $block->title }}">
                                        <div class="p-6 text-center">
                                            <a href="{{ $block->file_url }}" target="_blank"
                                               class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md">
                                                Download PDF
                                            </a>
                                        </div>
                                    </iframe>
                                </div>

                            @elseif($block->type === 'video' && $block->file_path)
                                @if(auth()->user()->low_data_mode)
                                    <div class="rounded-xl overflow-hidden bg-white/[0.02] p-6 text-center border border-white/[0.06]">
                                        <div class="text-3xl mb-2">📡</div>
                                        <h3 class="text-xs font-bold text-white/90">Video Hidden (Low Data Mode)</h3>
                                        <p class="text-[11px] text-white/40 mb-4">You have Low Data Mode enabled. Video streaming is disabled to save bandwidth.</p>
                                        <a href="{{ $block->file_url }}" target="_blank" download class="text-xs bg-violet-500/15 text-violet-300 px-4 py-2.5 rounded-xl font-bold uppercase tracking-wider hover:bg-violet-500/20 transition inline-block">
                                            Download Video
                                        </a>
                                    </div>
                                @else
                                    <div class="rounded-xl overflow-hidden bg-black/60 shadow-inner border border-white/[0.08] aspect-video">
                                        <video controls class="w-full h-full" style="max-height:65vh;" preload="metadata">
                                            <source src="{{ $block->file_url }}">
                                            <p class="text-white/60 text-xs p-4">Your browser does not support video playback.</p>
                                        </video>
                                    </div>
                                @endif

                            @elseif($block->type === 'image' && $block->file_path)
                                <div class="flex justify-center">
                                    <img src="{{ $block->file_url }}"
                                         alt="{{ $block->title ?? 'Lesson image' }}"
                                         class="max-w-full max-h-[60vh] rounded-xl border border-white/[0.08] object-contain shadow-sm">
                                </div>

                            @elseif($block->type === 'text' && $block->content_text)
                                <div class="prose prose-invert prose-sm max-w-none bg-white/[0.01] rounded-xl border border-white/[0.06] p-6
                                            text-white/80 leading-relaxed whitespace-pre-wrap text-xs">{{ $block->content_text }}</div>

                            @else
                                <div class="text-center py-6 text-white/30">
                                    <p class="text-xs">Content file is not available.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Legacy single-file fallback --}}
                @if($lesson->contents->isEmpty())
                    <div class="glass-card p-0 overflow-hidden">
                        <div class="flex items-center gap-3 px-5 py-3.5 border-b border-white/[0.06] bg-white/[0.01]">
                            <span class="text-lg">{{ $lesson->file_type === 'video' ? '🎥' : ($lesson->file_type === 'image' ? '🖼️' : '📄') }}</span>
                            <p class="font-bold text-xs text-white/95 capitalize" style="font-family: var(--font-display);">{{ $lesson->file_type }} Content</p>
                        </div>
                        <div class="p-5">
                            @if($lesson->file_type === 'pdf' && $lesson->file_path)
                                <div class="rounded-xl overflow-hidden border border-white/[0.08] bg-black/40">
                                    <iframe src="{{ $lesson->file_url }}" class="w-full" style="height:70vh;"></iframe>
                                </div>
                            @elseif($lesson->file_type === 'video' && $lesson->file_path)
                                @if(auth()->user()->low_data_mode)
                                    <div class="rounded-xl overflow-hidden bg-white/[0.02] p-6 text-center border border-white/[0.06]">
                                        <div class="text-3xl mb-2">📡</div>
                                        <h3 class="text-xs font-bold text-white/90">Video Hidden (Low Data Mode)</h3>
                                        <p class="text-[11px] text-white/40 mb-4">You have Low Data Mode enabled. Video streaming is disabled to save bandwidth.</p>
                                        <a href="{{ $lesson->file_url }}" target="_blank" download class="text-xs bg-violet-500/15 text-violet-300 px-4 py-2.5 rounded-xl font-bold uppercase tracking-wider hover:bg-violet-500/20 transition inline-block">
                                            Download Video
                                        </a>
                                    </div>
                                @else
                                    <div class="rounded-xl overflow-hidden bg-black/60 border border-white/[0.08] aspect-video">
                                        <video controls class="w-full h-full" style="max-height:65vh;" preload="metadata">
                                            <source src="{{ $lesson->file_url }}">
                                        </video>
                                    </div>
                                @endif
                            @elseif($lesson->content)
                                <div class="bg-white/[0.01] rounded-xl border border-white/[0.06] p-6 text-white/80 text-xs leading-relaxed whitespace-pre-wrap">
                                    {{ $lesson->content }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- ── Mark Complete / Auto-next ── --}}
            <div id="completeSection" class="glass-card p-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    @if($isCompleted)
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs font-bold">✓</div>
                        <div>
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Lesson completed!</p>
                            <p class="text-[10px] text-white/40 mt-0.5">Your progress has been saved.</p>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-lg bg-white/[0.04] border border-white/[0.08] text-white/30 flex items-center justify-center text-xs" id="completeIcon">○</div>
                        <div>
                            <p class="text-xs font-bold text-white/80 uppercase tracking-wider">Mark as complete</p>
                            <p class="text-[10px] text-white/40 mt-0.5">Track your progress through this course.</p>
                        </div>
                    @endif
                </div>
                @if(!$isCompleted)
                    <button id="markCompleteBtn" type="button"
                            onclick="markComplete()"
                            class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold px-5 py-2.5 rounded-xl uppercase tracking-wider transition-all shadow-md">
                        <span id="completeBtnText">Mark Complete</span>
                        <span id="completeBtnSpinner" class="hidden">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </span>
                    </button>
                @endif
            </div>

            {{-- ── Prev / Next nav ── --}}
            <div class="flex justify-between gap-4">
                @if($prev)
                    <a href="{{ route('student.courses.lesson', [$course, $prev]) }}"
                       class="flex items-center gap-3 flex-1 glass-card hover:border-violet-500/20 p-4 transition-all duration-300 group">
                        <div class="w-8 h-8 rounded-lg bg-white/[0.04] border border-white/[0.08] group-hover:bg-violet-500/20 group-hover:text-violet-300 flex items-center justify-center flex-shrink-0 transition">
                            <span class="text-white/40 group-hover:text-white/80 text-xs">←</span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] text-white/35 font-bold uppercase tracking-wider">Previous Lesson</div>
                            <div class="font-semibold text-xs text-white/90 truncate mt-0.5">{{ $prev->title }}</div>
                        </div>
                    </a>
                @else
                    <div class="flex-1"></div>
                @endif

                @if($next)
                    <a href="{{ route('student.courses.lesson', [$course, $next]) }}"
                       class="flex items-center justify-end gap-3 flex-1 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white p-4 rounded-2xl transition shadow-lg shadow-violet-500/20 group">
                        <div class="min-w-0 text-right">
                            <div class="text-[10px] text-white/70 font-bold uppercase tracking-wider">Next Lesson</div>
                            <div class="font-semibold text-xs text-white/95 truncate mt-0.5">{{ $next->title }}</div>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-white/10 group-hover:bg-white/20 flex items-center justify-center flex-shrink-0 transition border border-white/10">
                            <span class="text-xs">→</span>
                        </div>
                    </a>
                @else
                    <a href="{{ route('student.courses.show', $course) }}"
                       class="flex items-center justify-center gap-2 flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white px-4 py-4 rounded-2xl transition font-bold uppercase tracking-wider text-xs shadow-lg shadow-emerald-500/20">
                        🎉 Course Complete!
                    </a>
                @endif
            </div>
        </div>

        {{-- ── Sidebar: lesson list ── --}}
        <div class="lg:col-span-1">
            <div class="glass-card p-0 sticky top-20 overflow-hidden">
                {{-- Header --}}
                <div class="px-4 py-3.5 border-b border-white/[0.06] bg-white/[0.01]">
                    <h2 class="font-bold text-white/95 text-xs truncate max-w-[200px]" style="font-family: var(--font-display);">{{ $course->title }}</h2>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                            <div class="h-full bg-violet-500 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-[10px] text-white/40 font-bold">{{ $pct }}%</span>
                    </div>
                </div>

                {{-- Lesson list --}}
                <div class="divide-y divide-white/[0.04] overflow-y-auto custom-scrollbar" style="max-height: calc(100vh - 260px);">
                    @foreach($allLessons as $i => $l)
                        @php $done = in_array($l->id, $completedIds); @endphp
                        <a href="{{ route('student.courses.lesson', [$course, $l]) }}"
                           class="flex items-center gap-3 px-4 py-3 transition
                                  {{ $l->id === $lesson->id
                                     ? 'bg-white/[0.04] border-l-4 border-violet-500'
                                     : 'hover:bg-white/[0.02] border-l-4 border-transparent' }}">

                            <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold flex-shrink-0
                                        {{ $l->id === $lesson->id
                                           ? 'bg-violet-600 text-white'
                                           : ($done ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/20' : 'bg-white/[0.04] text-white/40') }}">
                                @if($done && $l->id !== $lesson->id)
                                    ✓
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>

                            <span class="text-xs leading-snug line-clamp-2 flex-1
                                         {{ $l->id === $lesson->id ? 'font-bold text-violet-400' : 'text-white/60' }}">
                                {{ $l->title }}
                            </span>
                            @if($done && $l->id !== $lesson->id)
                                <span class="text-emerald-400 text-xs flex-shrink-0">✓</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
/* ─── Constants ─────────────────────────────────────────────────────────── */
const LESSON_ID    = {{ $lesson->id }};
const COMPLETE_URL = "{{ route('student.courses.lesson.complete', [$course, $lesson]) }}";
const COURSE_URL   = "{{ route('student.courses.show', $course) }}";
const CSRF         = "{{ csrf_token() }}";
@if($next)
const NEXT_URL = "{{ route('student.courses.lesson', [$course, $next]) }}";
@else
const NEXT_URL = null;
@endif

/* ─── Video Position Save / Restore ─────────────────────────────────────── */
(function () {
    const KEY = `nabha_vpos_${LESSON_ID}`;

    // Restore position on all video elements for this lesson
    document.querySelectorAll('video').forEach(video => {
        const saved = parseFloat(localStorage.getItem(KEY) || '0');

        video.addEventListener('loadedmetadata', () => {
            if (saved > 4 && saved < video.duration - 3) {
                video.currentTime = saved;
                showResumeToast(video, saved);
            }
        });

        // Save position every 5 seconds while playing
        let saveTimer;
        video.addEventListener('play', () => {
            saveTimer = setInterval(() => {
                if (!video.paused && video.currentTime > 2) {
                    localStorage.setItem(KEY, video.currentTime.toFixed(1));
                }
            }, 5000);
        });
        video.addEventListener('pause', () => {
            clearInterval(saveTimer);
            if (video.currentTime > 2) localStorage.setItem(KEY, video.currentTime.toFixed(1));
        });
        // Clear on completion
        video.addEventListener('ended', () => {
            clearInterval(saveTimer);
            localStorage.removeItem(KEY);
        });
    });

    function showResumeToast(video, seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        const at   = mins > 0 ? `${mins}m ${secs}s` : `${secs}s`;
        if (window.toast) window.toast('info', `Resumed from ${at}`);
    }
})();

/* ─── Mark Complete + Auto-next ─────────────────────────────────────────── */
function markComplete() {
    const btn     = document.getElementById('markCompleteBtn');
    const txtEl   = document.getElementById('completeBtnText');
    const spinner = document.getElementById('completeBtnSpinner');
    if (!btn) return;

    btn.disabled = true;
    if (txtEl) txtEl.textContent = 'Saving…';
    if (spinner) spinner.classList.remove('hidden');

    fetch(COMPLETE_URL, {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(response => {
        const data = response.data || response;
        if (!data.completed) return;

        // Update section to "completed" state
        const section = document.getElementById('completeSection');
        if (data.is_course_complete) {
            section.innerHTML = `
                <div class="flex items-center justify-between w-full gap-4 p-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-lg font-bold">🎉</div>
                        <div>
                            <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Course Completed!</p>
                            <p class="text-[10px] text-white/40 mt-0.5">Congratulations! You have completed all lessons in this course.</p>
                        </div>
                    </div>
                    <a href="${COURSE_URL}" class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-[10px] font-bold px-4 py-2.5 rounded-xl uppercase tracking-wider transition-all shadow-md">
                        Back to Course Page
                    </a>
                </div>`;
        } else {
            section.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 flex items-center justify-center text-xs font-bold">✓</div>
                    <div>
                        <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Lesson completed!</p>
                        <p class="text-[10px] text-white/40 mt-0.5">Your progress has been saved.</p>
                    </div>
                </div>`;
        }

        if (window.toast) window.toast('success', 'Lesson marked as complete!');

        // Mark sidebar item as done (best-effort)
        document.querySelectorAll('[data-lesson-id="{{ $lesson->id }}"]').forEach(el => {
            el.classList.add('text-emerald-400');
        });

        if (data.next_url) {
            let countdown = 5;
            const wrap = document.createElement('div');
            wrap.className = 'flex items-center gap-3';
            wrap.innerHTML = `
                <p class="text-[11px] text-white/40">
                    Auto-advancing in <span id="cdCount" class="font-bold text-violet-400">${countdown}</span>s…
                </p>
                <a href="${data.next_url}"
                   class="text-[10px] bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-4 py-2 rounded-xl font-bold uppercase tracking-wider transition-all">
                    Next Lesson →
                </a>
                <button onclick="clearInterval(window._autoNextTimer); this.parentElement.querySelector('#cdCount').closest('p').textContent='Auto-advance cancelled';"
                        class="text-[10px] text-white/40 hover:text-white/70 transition underline font-bold uppercase tracking-wider">Cancel</button>`;
            section.appendChild(wrap);

            window._autoNextTimer = setInterval(() => {
                countdown--;
                const el = document.getElementById('cdCount');
                if (el) el.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(window._autoNextTimer);
                    window.location.href = data.next_url;
                }
            }, 1000);
        }
    })
    .catch(() => {
        if (btn) btn.disabled = false;
        if (txtEl) txtEl.textContent = 'Mark Complete';
        if (spinner) spinner.classList.add('hidden');
        if (window.toast) window.toast('error', 'Could not save progress. Please try again.');
    });
}
</script>
@endpush
@endsection
