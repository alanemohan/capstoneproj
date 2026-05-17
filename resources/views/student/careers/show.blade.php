@extends('layouts.student')

@section('title', $career['title'] . ' - ' . __('careers.title'))

@section('student-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    <div>
        <a href="{{ route('student.careers') }}" class="text-xs text-violet-400 hover:text-violet-300 font-bold uppercase tracking-wider flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>{{ __('careers.back_to_roadmaps') }}</span>
        </a>
    </div>

    <!-- Header Section -->
    <div class="glass-card p-0 overflow-hidden glow-violet">
        <div class="p-8 md:p-12 text-center md:text-left flex flex-col md:flex-row items-center gap-8 bg-gradient-to-r from-violet-900/60 to-indigo-900/60 border-b border-white/[0.06]">
            <div class="w-20 h-20 bg-white/[0.03] rounded-2xl border border-white/[0.08] shadow flex items-center justify-center text-4xl flex-shrink-0">
                {{ $career['icon'] }}
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white/95 mb-3" style="font-family: var(--font-display);">{{ $career['title'] }}</h1>
                <p class="text-sm text-white/70 max-w-2xl leading-relaxed">{{ $career['description'] }}</p>
                <div class="mt-5 inline-flex items-center px-4 py-2 bg-white/[0.03] border border-white/[0.08] rounded-xl text-white/90 font-bold text-xs uppercase tracking-wider shadow-sm">
                    <span class="mr-2 text-base">💰</span>
                    {{ __('careers.salary') }}: <span class="text-violet-400 ml-1 font-extrabold">{{ $career['salary_range'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Skills Section -->
        <div class="glass-card p-6 h-fit">
            <h2 class="text-xs font-bold text-white/90 mb-6 flex items-center uppercase tracking-wider" style="font-family: var(--font-display);">
                <span class="text-base mr-2">🎯</span>
                {{ __('careers.skills') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach($career['skills'] as $skill)
                    <span class="bg-white/[0.04] text-white/80 px-3 py-1.5 rounded-lg text-xs font-bold border border-white/[0.06] uppercase tracking-wider">
                        {{ $skill }}
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Roadmap Timeline -->
        <div class="md:col-span-2 glass-card p-6 md:p-8">
            <h2 class="text-xs font-bold text-white/90 mb-8 flex items-center uppercase tracking-wider" style="font-family: var(--font-display);">
                <span class="text-base mr-2">🚀</span>
                {{ __('careers.roadmap') }}
            </h2>
            
            <div class="space-y-8 relative before:absolute before:inset-y-0 before:left-5 md:before:left-1/2 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-violet-500 before:via-indigo-500 before:to-transparent">
                @foreach($career['roadmap'] as $index => $step)
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                        <div class="flex items-center justify-center w-10 h-10 rounded-xl border border-white/[0.1] bg-slate-900 text-violet-400 font-bold text-xs shadow-md shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 transition-colors group-hover:bg-violet-600 group-hover:text-white">
                            {{ $index + 1 }}
                        </div>
                        <div class="w-[calc(100%-3.5rem)] md:w-[calc(50%-2rem)] p-5 rounded-2xl border border-white/[0.06] bg-white/[0.01] hover:border-violet-500/30 transition-all duration-300">
                            <h3 class="font-bold text-white/90 text-xs uppercase tracking-wider mb-2" style="font-family: var(--font-display);">{{ __('messages.step') }} {{ $index + 1 }}</h3>
                            <p class="text-white/60 text-xs leading-relaxed">{{ $step }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
