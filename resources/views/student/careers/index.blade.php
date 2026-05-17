@extends('layouts.student')

@section('title', __('careers.title') . ' - Nabha Digital Learning')

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <div class="glass-card p-8 mb-8 text-center glow-violet relative overflow-hidden">
        <h1 class="text-xl font-bold text-white/95 mb-3" style="font-family: var(--font-display);">{{ __('careers.discover_future') }}</h1>
        <p class="text-white/45 text-xs max-w-2xl mx-auto leading-relaxed">{{ __('careers.explore_paths') }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($careers as $career)
            @php
                // Safe fallbacks for colors
                $colorMap = [
                    'emerald' => 'from-emerald-500/10 to-teal-500/10 border-emerald-500/20 text-emerald-400',
                    'blue' => 'from-blue-500/10 to-cyan-500/10 border-blue-500/20 text-blue-400',
                    'purple' => 'from-purple-500/10 to-indigo-500/10 border-purple-500/20 text-purple-400',
                    'amber' => 'from-amber-500/10 to-orange-500/10 border-amber-500/20 text-amber-400',
                    'indigo' => 'from-indigo-500/10 to-violet-500/10 border-indigo-500/20 text-indigo-400',
                    'rose' => 'from-rose-500/10 to-pink-500/10 border-rose-500/20 text-rose-400',
                ];
                $colorClass = $colorMap[$career['bg_color'] ?? 'purple'] ?? $colorMap['purple'];
            @endphp
            <a href="{{ route('student.careers.show', $career['id']) }}" class="block group">
                <div class="glass-card p-6 h-full hover:border-violet-500/20 transition-all duration-300 transform hover:-translate-y-1 relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-white/[0.03] border border-white/[0.08] text-white/80 rounded-xl flex items-center justify-center text-2xl mb-4">
                            {{ $career['icon'] }}
                        </div>
                        <h3 class="text-base font-bold text-white/95 mb-2" style="font-family: var(--font-display);">{{ $career['title'] }}</h3>
                        <p class="text-xs text-white/40 mb-4 line-clamp-3 leading-relaxed">
                            {{ $career['description'] }}
                        </p>
                        <ul class="text-[11px] text-white/60 space-y-2 mb-4">
                            @foreach(array_slice($career['skills'], 0, 3) as $skill)
                                <li class="flex items-center gap-1.5"><span class="text-violet-400">✓</span> {{ $skill }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-4 pt-4 border-t border-white/[0.06]">
                        <span class="text-violet-400 font-bold text-xs uppercase tracking-wider flex items-center group-hover:text-violet-300 transition">
                            {{ __('careers.view_full_roadmap') }} <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
