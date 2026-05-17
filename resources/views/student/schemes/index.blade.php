@extends('layouts.student')

@section('title', __('messages.schemes') . ' - ' . __('messages.platform_name'))

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.schemes_title') }}</h1>
            <p class="text-xs text-white/40 mt-1">Discover government and corporate schemes, scholarships, and resources to assist your academic journey.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-violet-500/10 text-violet-400 text-[10px] font-bold border border-violet-500/10">✨ Active Opportunities</span>
        </div>
    </div>

    @if($schemes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($schemes as $scheme)
                <div class="glass-card p-6 border border-white/[0.06] hover:border-violet-500/30 transition-all duration-300 relative overflow-hidden flex flex-col justify-between group glow-violet">
                    <!-- Gradient accent line -->
                    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-violet-500 via-indigo-500 to-cyan-500"></div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-violet-500/10 text-violet-300 border border-violet-500/10 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                Opportunity
                            </span>
                            <span class="text-[10px] text-white/30 font-medium">Verified ✔</span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-white/90 group-hover:text-violet-300 transition duration-300 leading-snug" style="font-family: var(--font-display);">
                            {{ $scheme->getTranslated('title') }}
                        </h3>
                        
                        <p class="text-xs text-white/40 leading-relaxed font-medium">
                            {{ $scheme->getTranslated('description') }}
                        </p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <!-- Structured Meta Details -->
                        <div class="bg-white/[0.02] border border-white/[0.04] p-3.5 rounded-xl text-xs space-y-3">
                            <div class="flex items-start gap-2.5">
                                <div class="w-5 h-5 rounded-md bg-cyan-500/10 text-cyan-400 flex items-center justify-center flex-shrink-0 text-[10px]">👥</div>
                                <div class="flex-1">
                                    <p class="text-[9px] font-bold text-cyan-400 uppercase tracking-wider mb-0.5">{{ __('messages.target') }}</p>
                                    <p class="text-[11px] text-white/80 leading-normal font-semibold">{{ $scheme->getTranslated('target_audience') ?? __('messages.general') }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-2.5 pt-2.5 border-t border-white/[0.04]">
                                <div class="w-5 h-5 rounded-md bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0 text-[10px]">🎁</div>
                                <div class="flex-1">
                                    <p class="text-[9px] font-bold text-emerald-400 uppercase tracking-wider mb-0.5">{{ __('messages.benefits') }}</p>
                                    <p class="text-[11px] text-white/80 leading-normal font-semibold">{{ $scheme->getTranslated('benefits') ?? __('messages.not_available') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Button -->
                        @if($scheme->url)
                            <a href="{{ $scheme->url }}" target="_blank" class="block w-full text-center bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl transition duration-300 shadow-md shadow-violet-500/10 hover:shadow-violet-500/20">
                                {{ __('messages.learn_more') }} →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card p-12 text-center text-white/40 glow-violet flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-violet-500/5 text-violet-400 flex items-center justify-center text-3xl mb-4 border border-violet-500/10">📭</div>
            <h3 class="text-sm font-bold text-white/80" style="font-family: var(--font-display);">{{ __('messages.no_schemes') }}</h3>
            <p class="text-xs text-white/30 mt-1">There are no new scholarships or government schemes posted at this moment.</p>
        </div>
    @endif
</div>
@endsection
