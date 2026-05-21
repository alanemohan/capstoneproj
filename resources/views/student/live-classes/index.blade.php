@extends('layouts.student')

@section('title', 'Live Classes - Nabha Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white/90 tracking-tight" style="font-family: var(--font-display);">Live Classes</h1>
            <p class="text-xs text-gray-500 dark:text-white/40 mt-1">Attend real-time interactive lectures, clear your doubts, and collaborate with peers.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-violet-500/10 text-violet-600 dark:text-violet-400 text-[10px] font-bold border border-violet-500/10">🔴 Interactive Learning</span>
        </div>
    </div>

    @if($liveClasses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($liveClasses as $class)
                @php
                    $status = $class->status ?? 'scheduled';
                    $isLive = $status === 'live';
                    $isScheduled = $status === 'scheduled';
                    $isCompleted = $status === 'completed';
                    $isCancelled = $status === 'cancelled';
                @endphp
                <div class="glass-card p-6 border border-gray-200 dark:border-white/[0.06] hover:border-violet-500/30 transition-all duration-300 relative overflow-hidden flex flex-col justify-between group glow-violet bg-white dark:bg-[#0b0f19]">
                    <!-- Gradient accent line based on status -->
                    @if($isLive)
                        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-red-500 via-pink-500 to-rose-500 animate-pulse"></div>
                    @elseif($isScheduled)
                        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-violet-500 via-indigo-500 to-cyan-500"></div>
                    @else
                        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gray-300 dark:bg-white/10"></div>
                    @endif
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            @if($isLive)
                                <span class="px-2.5 py-0.5 bg-red-500/10 text-red-600 dark:text-red-400 border border-red-500/15 text-[9px] font-bold rounded-md uppercase tracking-wider animate-pulse flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Live Now
                                </span>
                            @elseif($isScheduled)
                                <span class="px-2.5 py-0.5 bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/15 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                    Scheduled
                                </span>
                            @elseif($isCompleted)
                                <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/15 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                    Completed
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 bg-gray-500/10 text-gray-600 dark:text-gray-400 border border-gray-500/15 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                    Cancelled
                                </span>
                            @endif
                            <span class="text-[10px] text-gray-450 dark:text-white/30 font-semibold">
                                {{ $class->course ? $class->course->getLocalized('title') : 'General Class' }}
                            </span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white/95 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition duration-300 leading-snug" style="font-family: var(--font-display);">
                            {{ $class->getLocalized('title') }}
                        </h3>
                        
                        <p class="text-xs text-gray-500 dark:text-white/40 leading-relaxed font-medium">
                            {{ $class->getLocalized('description') ?: 'No additional description provided for this live session.' }}
                        </p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <!-- Structured Meta Details Card -->
                        <div class="bg-gray-50 dark:bg-white/[0.02] border border-gray-150 dark:border-white/[0.04] p-3.5 rounded-xl text-xs space-y-3">
                            <!-- Teacher Row -->
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-white/60">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">👨‍🏫</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">Instructor</span>
                                </div>
                                <span class="text-gray-900 dark:text-white/80 font-bold text-xs">
                                    {{ $class->teacher ? $class->teacher->name : 'Nabha Instructor' }}
                                </span>
                            </div>
                            
                            <!-- Date & Time Row -->
                            <div class="flex items-start justify-between text-xs text-gray-600 dark:text-white/60 pt-2.5 border-t border-gray-150 dark:border-white/[0.04]">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">📅</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">Schedule</span>
                                </div>
                                <span class="text-gray-800 dark:text-white/80 font-semibold text-[11px] text-right">
                                    {{ $class->scheduled_at ? $class->scheduled_at->format('d M Y, h:i A') : 'TBD' }}
                                </span>
                            </div>

                            <!-- Duration Row -->
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-white/60 pt-2.5 border-t border-gray-150 dark:border-white/[0.04]">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">⏱</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">Duration</span>
                                </div>
                                <span class="text-violet-600 dark:text-violet-400 font-extrabold text-[11px]">
                                    {{ $class->duration_minutes ? $class->duration_minutes . ' mins' : '60 mins' }}
                                </span>
                            </div>
                        </div>

                        <!-- CTA Join Button -->
                        @if($isLive || $isScheduled)
                            @if($class->meeting_link)
                                <a href="{{ $class->meeting_link }}" target="_blank" class="block w-full text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl transition duration-300 shadow-md shadow-violet-500/10 hover:shadow-violet-500/20">
                                    Join Class Now →
                                </a>
                            @else
                                <button disabled class="block w-full text-center bg-gray-200 dark:bg-white/5 text-gray-400 dark:text-white/20 text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl cursor-not-allowed">
                                    Link Not Available
                                </button>
                            @endif
                        @elseif($isCompleted)
                            <button disabled class="block w-full text-center bg-gray-100 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.04] text-gray-400 dark:text-white/20 text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl cursor-not-allowed">
                                Class Completed ✅
                            </button>
                        @else
                            <button disabled class="block w-full text-center bg-gray-100 dark:bg-white/[0.02] border border-gray-200 dark:border-white/[0.04] text-gray-400 dark:text-white/20 text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl cursor-not-allowed">
                                Class Cancelled ❌
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card p-12 text-center text-gray-400 dark:text-white/40 glow-violet flex flex-col items-center justify-center bg-white dark:bg-[#0b0f19]">
            <div class="w-16 h-16 rounded-full bg-violet-500/5 text-violet-400 flex items-center justify-center text-3xl mb-4 border border-violet-500/10">📺</div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white/80" style="font-family: var(--font-display);">No Live Classes Available</h3>
            <p class="text-xs text-gray-500 dark:text-white/30 mt-1">There are no live classes scheduled at this moment. We will notify you once one is added!</p>
        </div>
    @endif
</div>
@endsection
