@extends('layouts.student')

@section('title', __('messages.scholarships') . ' - ' . __('messages.platform_name'))

@section('student-content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white/90 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.scholarships_title') }}</h1>
            <p class="text-xs text-gray-500 dark:text-white/40 mt-1">Explore verified scholarship opportunities, fund your education, and apply directly.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold border border-emerald-500/10">🎓 Funding Open</span>
        </div>
    </div>

    @if($scholarships->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($scholarships as $scholarship)
                <div class="glass-card p-6 border border-gray-200 dark:border-white/[0.06] hover:border-emerald-500/30 transition-all duration-300 relative overflow-hidden flex flex-col justify-between group glow-emerald bg-white dark:bg-[#0b0f19]">
                    <!-- Gradient accent line -->
                    <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 via-teal-500 to-indigo-500"></div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 border border-emerald-500/10 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                Scholarship
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-white/30 font-medium">Verified ✔</span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white/95 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition duration-300 leading-snug" style="font-family: var(--font-display);">
                            {{ $scholarship->getTranslated('title') }}
                        </h3>
                        
                        <p class="text-xs text-gray-500 dark:text-white/40 leading-relaxed font-medium">
                            {{ $scholarship->getTranslated('description') }}
                        </p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <!-- Structured Meta Details Card -->
                        <div class="bg-gray-50 dark:bg-white/[0.02] border border-gray-150 dark:border-white/[0.04] p-3.5 rounded-xl text-xs space-y-3">
                            <!-- Amount Row -->
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-white/60">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">💰</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">{{ __('messages.amount') }}</span>
                                </div>
                                <span class="text-emerald-600 dark:text-emerald-400 font-extrabold text-xs">
                                    {{ $scholarship->getTranslated('amount') ?? __('messages.variable') }}
                                </span>
                            </div>
                            
                            <!-- Eligibility Criteria Row -->
                            <div class="flex items-start justify-between text-xs text-gray-600 dark:text-white/60 pt-2.5 border-t border-gray-150 dark:border-white/[0.04]">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">📋</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">Eligibility</span>
                                </div>
                                <span class="text-gray-800 dark:text-white/80 font-semibold text-[11px] text-right line-clamp-1 truncate max-w-[150px]" title="{{ $scholarship->getTranslated('eligibility_criteria') }}">
                                    {{ $scholarship->getTranslated('eligibility_criteria') ?? 'General Eligibility' }}
                                </span>
                            </div>

                            <!-- Deadline Row -->
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-white/60 pt-2.5 border-t border-gray-150 dark:border-white/[0.04]">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">📅</span>
                                    <span class="font-bold uppercase tracking-wider text-[9px] text-gray-400 dark:text-white/35">{{ __('messages.deadline') }}</span>
                                </div>
                                <span class="text-rose-600 dark:text-rose-400 font-extrabold text-[11px]">
                                    {{ $scholarship->deadline ? \Carbon\Carbon::parse($scholarship->deadline)->format('d M Y') : __('messages.ongoing') }}
                                </span>
                            </div>
                        </div>

                        <!-- CTA Apply Button -->
                        @if($scholarship->url)
                            <a href="{{ $scholarship->url }}" target="_blank" class="block w-full text-center bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl transition duration-300 shadow-md shadow-emerald-500/10 hover:shadow-emerald-500/20">
                                {{ __('messages.apply_now') }} →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="glass-card p-12 text-center text-gray-400 dark:text-white/40 glow-emerald flex flex-col items-center justify-center bg-white dark:bg-[#0b0f19]">
            <div class="w-16 h-16 rounded-full bg-emerald-500/5 text-emerald-400 flex items-center justify-center text-3xl mb-4 border border-emerald-500/10">🎓</div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white/80" style="font-family: var(--font-display);">{{ __('messages.no_scholarships') }}</h3>
            <p class="text-xs text-gray-500 dark:text-white/30 mt-1">There are no new scholarships available at this moment. Please check back later!</p>
        </div>
    @endif
</div>
@endsection
