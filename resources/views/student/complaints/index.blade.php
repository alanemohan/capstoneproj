@extends('layouts.student')

@section('title', __('messages.help_complaints') . ' - Nabha Digital Learning')

@section('student-content')
<div class="space-y-6 max-w-4xl mx-auto animate-fade-in">
    <div>
        <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">{{ __('messages.help_complaints') }}</h1>
        <p class="text-xs text-white/40 mt-1">Submit support requests or report issues directly to the administration.</p>
    </div>

    <div class="glass-card p-6 md:p-8 glow-violet">
        <h2 class="text-sm font-bold text-white/90 mb-4 uppercase tracking-wider" style="font-family: var(--font-display);">{{ __('messages.submit_request') }}</h2>
        
        @if(session('success'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/15 text-emerald-400 rounded-xl px-4 py-3 text-xs leading-relaxed font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.complaints.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-white/40 mb-1.5 uppercase tracking-wider">{{ __('messages.subject') }}</label>
                <input type="text" name="subject" required
                       class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs"
                       placeholder="{{ __('messages.subject_placeholder') }}">
                @error('subject') <span class="text-red-400 text-[10px] font-bold uppercase tracking-wider mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold text-white/40 mb-1.5 uppercase tracking-wider">{{ __('messages.message') }}</label>
                <textarea name="message" rows="4" required
                          class="w-full px-4 py-2.5 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white/90 placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 focus:border-violet-500/30 transition text-xs"
                          placeholder="{{ __('messages.message_placeholder') }}"></textarea>
                @error('message') <span class="text-red-400 text-[10px] font-bold uppercase tracking-wider mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold py-3 px-6 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20">
                {{ __('messages.submit') }}
            </button>
        </form>
    </div>

    @if($complaints->count() > 0)
        <div class="glass-card p-0 overflow-hidden">
            <div class="p-6 border-b border-white/[0.06] bg-white/[0.01]">
                <h2 class="text-xs font-bold text-white/90 uppercase tracking-wider" style="font-family: var(--font-display);">{{ __('messages.previous_requests') }}</h2>
            </div>
            <div class="divide-y divide-white/[0.04]">
                @foreach($complaints as $complaint)
                    <div class="p-6 flex flex-col md:flex-row justify-between gap-4">
                        <div class="space-y-1">
                            <h4 class="font-bold text-xs text-white/90" style="font-family: var(--font-display);">{{ $complaint->subject }}</h4>
                            <p class="text-xs text-white/60 leading-relaxed">{{ $complaint->message }}</p>
                            <span class="text-[10px] text-white/30 font-semibold uppercase tracking-wider block pt-1">{{ $complaint->created_at->diffForHumans() }}</span>
                        </div>
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[9px] font-bold border uppercase tracking-wider {{ $complaint->status === 'resolved' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/20' : 'bg-amber-500/20 text-amber-300 border-amber-500/20' }}">
                                {{ ucfirst($complaint->status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
