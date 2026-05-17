@extends('layouts.student')

@section('title', __('messages.mentors') . ' - Nabha Learning')

@section('student-content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    <div>
        <h1 class="text-xl font-bold text-white/90 tracking-tight" style="font-family: var(--font-display);">Your Academic Mentor</h1>
        <p class="text-xs text-white/40 mt-1">Get personalized guidance and support from your assigned mentor.</p>
    </div>

    @if($mentors->count() > 0)
        @foreach($mentors as $mentor)
            <div class="glass-card p-0 overflow-hidden glow-violet">
                <div class="md:flex">
                    <div class="md:w-1/3 bg-gradient-to-br from-violet-900/60 to-indigo-900/60 p-8 flex flex-col items-center justify-center border-r md:border-b-0 border-b border-white/[0.06] text-center">
                        <div class="relative">
                            <img src="{{ $mentor->avatar_url }}" alt="{{ $mentor->name }}" 
                                 class="w-28 h-28 rounded-2xl border-2 border-white/10 shadow-lg object-cover ring-4 ring-violet-500/20">
                            <div class="absolute -bottom-1 -right-1 w-4.5 h-4.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></div>
                        </div>
                        <h2 class="mt-4 text-base font-bold text-white/95" style="font-family: var(--font-display);">{{ $mentor->name }}</h2>
                        <p class="text-violet-300 text-[10px] uppercase font-bold tracking-wider mt-1">{{ $mentor->subject_specialization ?? 'Academic Expert' }}</p>
                    </div>
                    
                    <div class="md:w-2/3 p-6 sm:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h3 class="text-sm font-bold text-white/95" style="font-family: var(--font-display);">Mentor Profile</h3>
                                    <p class="text-[10px] text-white/40 mt-0.5 font-medium uppercase tracking-wider">Expertise in {{ $mentor->subject_specialization ?? 'General Academics' }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/20 text-[9px] font-bold rounded-md uppercase tracking-wider">
                                    Assigned
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-violet-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-white/35 font-bold uppercase tracking-wider">Email Address</p>
                                        <p class="text-xs font-semibold text-white/80 mt-0.5 truncate max-w-[180px]">{{ $mentor->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-violet-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-white/35 font-bold uppercase tracking-wider">Phone Number</p>
                                        <p class="text-xs font-semibold text-white/80 mt-0.5">{{ $mentor->phone ?? 'Not shared' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-violet-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-white/35 font-bold uppercase tracking-wider">Availability</p>
                                        <p class="text-xs font-semibold text-white/80 mt-0.5">{{ $mentor->availability ?? 'Mon - Fri (10 AM - 4 PM)' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-center text-violet-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m4 0h1m-5 10h5m-5 4h5m-4-4v4m1-4v4m1-4v4"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-white/35 font-bold uppercase tracking-wider">Affiliation</p>
                                        <p class="text-xs font-semibold text-white/80 mt-0.5 truncate max-w-[180px]">{{ $mentor->school }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-violet-500/5 border border-violet-500/10 rounded-2xl p-4 mb-6">
                                <p class="text-xs text-violet-300 leading-relaxed">
                                    <strong>Mentor Guidance:</strong> For any academic queries please contact your assigned mentor. They are here to help you navigate your learning journey and achieve your goals.
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button @click="window.dispatchEvent(new CustomEvent('open-mentor-email'))"
                               class="flex-1 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold py-3 px-6 rounded-xl text-xs uppercase tracking-wider transition-all shadow-lg shadow-violet-500/20 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Send Email
                            </button>
                            @if($mentor->phone)
                            <a href="tel:{{ $mentor->phone }}" 
                               class="bg-white/[0.04] border border-white/[0.08] hover:bg-white/[0.08] text-white/90 font-bold py-3 px-6 rounded-xl text-xs uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                Call Now
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="glass-card p-16 text-center glow-violet">
            <div class="w-16 h-16 bg-white/[0.03] border border-white/[0.08] rounded-xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-white/95 mb-2" style="font-family: var(--font-display);">No mentor assigned yet</h3>
            <p class="text-white/40 text-xs max-w-sm mx-auto leading-relaxed">Please wait for the administrator to assign a mentor to your profile. Mentors help you with your academic progress and career roadmap.</p>
        </div>
    @endif
</div>
@endsection
