@extends('layouts.admin')

@section('title', 'Preview: ' . $lesson->title)

@section('admin-content')
<div class="space-y-6 animate-fade-in text-slate-800">
    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.content') }}"
           class="text-[10px] font-bold text-slate-400 hover:text-orange-500 uppercase tracking-wider transition">
            ← Back to Content Review
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        {{-- Title bar --}}
        <div class="p-6 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5 mb-2">
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight" style="font-family: var(--font-display);">{{ $lesson->title }}</h1>
                        <span class="text-[9px] font-bold px-2.5 py-0.5 border rounded-md uppercase tracking-wider
                            {{ $lesson->status === 'published' ? 'bg-emerald-50 border-emerald-250 text-emerald-700'
                             : ($lesson->status === 'pending' ? 'bg-yellow-50 border-yellow-250 text-yellow-750'
                             : 'bg-red-50 border-red-250 text-red-700') }}">
                            {{ $lesson->status }}
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2.5 text-[9px] font-bold uppercase tracking-wider text-slate-450">
                        <span class="text-slate-600">{{ $lesson->teacher->name }}</span>
                        <span>•</span>
                        <span>{{ $lesson->subject }}</span>
                        <span>•</span>
                        <span>{{ $lesson->class_level }}</span>
                        <span>•</span>
                        <span class="normal-case font-semibold text-[10px]">{{ $lesson->created_at->format('d M Y') }}</span>
                        @if($lesson->duration_minutes)
                            <span>•</span>
                            <span>{{ $lesson->duration_minutes }} min</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    @if($lesson->status !== 'published')
                        <form method="POST" action="{{ route('admin.content.approve', $lesson->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-[10px] font-bold uppercase tracking-wider bg-emerald-50 border border-emerald-150 hover:bg-emerald-100 text-emerald-700 px-4 py-2.5 rounded-lg transition shadow-sm">
                                Approve
                            </button>
                        </form>
                    @endif
                    @if($lesson->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.content.reject', $lesson->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-[10px] font-bold uppercase tracking-wider bg-yellow-50 border border-yellow-150 hover:bg-yellow-100 text-yellow-750 px-4 py-2.5 rounded-lg transition shadow-sm">
                                Reject
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Description --}}
        @if($lesson->description)
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-200">
                <h2 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Description</h2>
                <p class="text-slate-700 text-xs leading-relaxed font-medium">{{ $lesson->description }}</p>
            </div>
        @endif

        {{-- Content preview --}}
        <div class="p-6">
            @if($lesson->file_type === 'pdf' && $lesson->file_path)
                <h2 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3.5">PDF Preview</h2>
                <div class="rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                    <iframe src="{{ $lesson->file_url }}"
                            class="w-full"
                            style="height: 75vh; min-height: 500px;"
                            title="{{ $lesson->title }}">
                        <p class="p-4 text-slate-600 text-xs">
                            Your browser does not support PDF embedding.
                            <a href="{{ $lesson->file_url }}" target="_blank"
                               class="text-orange-600 underline font-bold">Download PDF</a>
                        </p>
                    </iframe>
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ $lesson->file_url }}" target="_blank"
                       class="text-[10px] font-bold uppercase tracking-wider text-orange-600 hover:underline flex items-center gap-1">
                        ↗ Open in new tab
                    </a>
                </div>

            @elseif($lesson->file_type === 'video' && $lesson->file_path)
                <h2 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3.5">Video Preview</h2>
                <div class="rounded-xl overflow-hidden bg-black flex items-center justify-center"
                     style="max-height: 70vh;">
                    <video controls
                           class="w-full max-h-full"
                           style="max-height: 70vh;"
                           preload="metadata">
                        <source src="{{ $lesson->file_url }}">
                        Your browser does not support video playback.
                    </video>
                </div>

            @elseif($lesson->content)
                <h2 class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3.5">Lesson Content</h2>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-slate-800 leading-relaxed whitespace-pre-wrap text-xs font-medium">{{ $lesson->content }}</div>

            @else
                <div class="text-center py-16 text-slate-400 text-xs font-semibold">
                    <p>No previewable content available for this lesson.</p>
                    <p class="text-[10px] mt-1 text-slate-400/80 normal-case font-medium">The teacher may not have uploaded a file or added text content yet.</p>
                </div>
            @endif
        </div>

        {{-- Stats footer --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50 flex flex-wrap gap-6 text-[10px] font-bold uppercase tracking-wider text-slate-400">
            <span>{{ $lesson->view_count }} views</span>
            <span>{{ $lesson->download_count }} downloads</span>
            <span>{{ $lesson->quizzes->count() }} quizzes linked</span>
            @if($lesson->approved_at)
                <span>Approved <span class="normal-case font-semibold text-[11px]">{{ $lesson->approved_at->format('d M Y') }}</span></span>
            @endif
        </div>
    </div>
</div>
@endsection
