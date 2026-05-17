@extends('layouts.student')

@section('title', 'My Assignments - Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <div class="animate-fade-in">
        <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">My Assignments</h1>
        <p class="text-xs text-white/40 mt-1">View and submit assignments from your class batches.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($assignments as $assignment)
            @php
                $submission = $assignment->submissions->first();
                $isPastDue = $assignment->due_date->isPast();
            @endphp
            <div class="glass-card p-6 hover:border-violet-500/20 transition-all duration-300 flex flex-col justify-between h-[230px] relative group overflow-hidden">
                <div class="space-y-2">
                    <div class="flex justify-between items-start">
                        <span class="text-[9px] bg-violet-500/15 text-violet-300 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $assignment->batch->name }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-white/90 line-clamp-1" title="{{ $assignment->title }}" style="font-family: var(--font-display);">{{ $assignment->title }}</h3>
                    <p class="text-xs text-white/40 line-clamp-2 leading-relaxed">{{ $assignment->description }}</p>
                </div>
                
                <div class="space-y-3 pt-3.5 border-t border-white/[0.06] mt-4">
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-white/45">Due Date:</span>
                        <span class="font-bold {{ $isPastDue ? 'text-red-400' : 'text-white/80' }}">
                            {{ $assignment->due_date->format('M d, Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-[11px]">
                        <span class="text-white/45">Status:</span>
                        @if($submission)
                            @if($submission->status === 'graded')
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 text-[10px] font-bold">Graded ({{ $submission->marks }}/{{ $assignment->max_marks }})</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-500/15 text-blue-300 text-[10px] font-bold">Submitted</span>
                            @endif
                        @else
                            @if($isPastDue)
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-500/15 text-red-300 text-[10px] font-bold">Missing</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-500/15 text-amber-300 text-[10px] font-bold">Pending</span>
                            @endif
                        @endif
                    </div>

                    <a href="{{ route('student.assignments.show', $assignment) }}" class="w-full flex justify-center items-center px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-xl transition-all shadow-md">
                        {{ $submission ? 'View Submission' : 'Submit Assignment' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card p-16 text-center glow-violet">
                <div class="text-5xl mb-4">📭</div>
                <h3 class="text-base font-bold text-white/90 mb-2" style="font-family: var(--font-display);">No assignments</h3>
                <p class="text-white/40 text-xs">You don't have any assignments due right now.</p>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
