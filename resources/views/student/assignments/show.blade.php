@extends('layouts.student')

@section('title', 'Assignment: ' . $assignment->title)

@section('student-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    <div>
        <a href="{{ route('student.assignments.index') }}" class="text-xs text-violet-400 hover:text-violet-300 font-bold uppercase tracking-wider flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Assignments</span>
        </a>
    </div>

    <div class="glass-card p-0 overflow-hidden glow-violet">
        <!-- Header -->
        <div class="bg-gradient-to-r from-violet-900/60 to-indigo-900/60 p-6 sm:p-8 border-b border-white/[0.06]">
            <div class="space-y-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-white/10 text-white/90 border border-white/10 uppercase tracking-wider">
                    {{ $assignment->batch->name }}
                </span>
                <h1 class="text-xl font-bold text-white/95" style="font-family: var(--font-display);">
                    {{ $assignment->title }}
                </h1>
            </div>
        </div>

        <!-- Details -->
        <div class="px-6 py-4 flex flex-wrap gap-6 border-b border-white/[0.06] bg-white/[0.01]">
            <div class="flex items-center gap-1.5 text-xs text-white/50">
                <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="{{ $assignment->due_date->isPast() ? 'text-red-400 font-bold' : '' }}">
                    Due: {{ $assignment->due_date->format('l, M d, Y') }}
                </span>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-white/50">
                <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Max Marks: <strong class="text-white/80">{{ $assignment->max_marks }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-white/50">
                <svg class="h-4 w-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Teacher: <strong class="text-white/80">{{ $assignment->teacher->name }}</strong></span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-3">
            <h3 class="text-xs font-bold text-white/40 uppercase tracking-wider">Instructions</h3>
            <div class="text-white/70 text-xs leading-relaxed whitespace-pre-wrap">{{ $assignment->description }}</div>
        </div>
        
        <!-- Submission Section -->
        <div class="bg-white/[0.01] p-6 sm:p-8 border-t border-white/[0.06]">
            <h3 class="text-xs font-bold text-white/50 mb-4 uppercase tracking-wider">Your Submission</h3>
            
            @if($submission)
                <div class="bg-white/[0.02] rounded-xl border border-white/[0.08] overflow-hidden shadow-sm">
                    <div class="p-4 flex items-center justify-between border-b border-white/[0.06] flex-wrap gap-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-9 w-9 bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 rounded-lg flex items-center justify-center text-xs font-bold">
                                ✓
                            </div>
                            <div class="ml-3">
                                <h4 class="text-xs font-bold text-white/90">Submitted Successfully</h4>
                                <p class="text-[10px] text-white/40 mt-0.5">On {{ $submission->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-white/[0.06] border border-white/[0.08] hover:bg-white/[0.1] text-white/80 text-[10px] font-bold uppercase tracking-wider rounded-lg transition-all">
                            Download File
                        </a>
                    </div>
                    
                    @if($submission->status === 'graded')
                        <div class="p-4 bg-emerald-500/5 border-t border-emerald-500/10">
                            <h4 class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-2">Teacher Feedback</h4>
                            <div class="flex justify-between items-start flex-wrap gap-4">
                                <p class="text-xs text-white/70 leading-relaxed flex-1 pr-4">{{ $submission->feedback ?? 'No written feedback provided.' }}</p>
                                <div class="bg-white/[0.02] border border-white/[0.08] px-4 py-2.5 rounded-xl text-center min-w-[70px]">
                                    <div class="text-xl font-extrabold text-emerald-400">{{ $submission->marks }}</div>
                                    <div class="text-[9px] text-white/40 font-bold uppercase tracking-wider mt-0.5">/ {{ $assignment->max_marks }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-amber-500/5">
                            <p class="text-xs text-amber-300/80 leading-relaxed font-medium">Your assignment is waiting to be graded by the teacher.</p>
                        </div>
                    @endif
                </div>
            @elseif($assignment->due_date->isPast())
                <div class="rounded-xl bg-red-500/10 border border-red-500/15 p-4 flex gap-3 leading-relaxed">
                    <div class="flex-shrink-0 text-red-400 text-sm">⚠️</div>
                    <div>
                        <h3 class="text-xs font-bold text-red-400 uppercase tracking-wider">Assignment is Past Due</h3>
                        <p class="text-xs text-white/60 mt-1">The deadline for this assignment has passed and submissions are no longer accepted.</p>
                    </div>
                </div>
            @else
                <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data" class="bg-white/[0.02] border border-white/[0.06] rounded-xl p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-white/40 mb-2 uppercase tracking-wider">Upload your work (PDF, DOCX, ZIP)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-white/[0.08] border-dashed rounded-xl hover:border-violet-500/50 transition-colors bg-white/[0.01]">
                            <div class="space-y-2 text-center">
                                <svg class="mx-auto h-10 w-10 text-white/20" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-xs text-white/40 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md font-bold text-violet-400 hover:text-violet-300 focus-within:outline-none">
                                        <span>Select a file</span>
                                        <input id="file-upload" name="submission_file" type="file" class="sr-only" required accept=".pdf,.doc,.docx,.zip">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[10px] text-white/30">up to 10MB</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Submit Assignment
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Simple file name display
    const fileUpload = document.getElementById('file-upload');
    if(fileUpload) {
        fileUpload.addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if(fileName) {
                const label = this.closest('.space-y-2').querySelector('.text-violet-400');
                label.innerHTML = `<span class="text-emerald-400 font-bold">Selected: ${fileName}</span>`;
            }
        });
    }
</script>
@endpush
@endsection
