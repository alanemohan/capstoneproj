@extends('layouts.app')

@section('title', __('messages.register') . ' - ' . __('messages.platform_name'))

@section('content')
<!-- Force Dark Mode for Register Page to preserve the gorgeous dark SaaS aesthetic -->
<script>
    document.documentElement.classList.add('dark');
</script>

<div class="min-h-screen bg-[#090616] text-white font-sans flex flex-col justify-between p-6 md:p-8 relative overflow-hidden select-none">
    
    <!-- Decorative Ambient Glows -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-purple-500/10 blur-[130px] pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full flex items-center justify-between z-20 relative max-w-7xl mx-auto">
        <a href="/" class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#8b5cf6] to-[#4f46e5] flex items-center justify-center text-white text-base font-bold shadow-lg shadow-purple-500/20">
                N
            </div>
            <span class="font-bold text-lg text-white tracking-tight" style="font-family: var(--font-display);">Nabha Digital Learning</span>
        </a>
        
        <!-- Language Switcher -->
        <div class="flex items-center gap-3 text-xs font-semibold">
            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="text-slate-400 hover:text-white transition">English</a>
            <span class="text-slate-700">|</span>
            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="text-slate-400 hover:text-white transition">हिंदी</a>
            <span class="text-slate-700">|</span>
            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="text-slate-400 hover:text-white transition">ਪੰਜਾਬੀ</a>
        </div>
    </header>

    <!-- Main Content Layout -->
    <main class="w-full max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 my-auto py-8 z-10 relative items-center">
        
        <!-- Left Column: Emotional branding and features -->
        <div class="lg:col-span-7 flex flex-col justify-center space-y-8 pr-0 lg:pr-6 text-left">
            <div class="space-y-4">
                <h1 class="text-4xl md:text-5xl lg:text-[54px] leading-[1.15] font-extrabold text-white tracking-tight" style="font-family: var(--font-display);">
                    Empowering Rural <br>
                    Students of <span class="text-[#8b5cf6] bg-clip-text">Nabha</span>
                </h1>
                <div class="w-16 h-1.5 bg-gradient-to-r from-[#8b5cf6] to-[#4f46e5] rounded-full"></div>
            </div>

            <p class="text-slate-350 text-sm md:text-base max-w-xl font-medium leading-relaxed">
                Quality education for every student, everywhere. <br>
                Learn, grow and achieve your dreams with Nabha Digital Learning Platform.
            </p>

            <!-- Translucent Feature Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl">
                <!-- Smart Learning -->
                <div class="bg-white/[0.03] hover:bg-white/[0.05] border border-white/[0.06] rounded-2xl p-4 flex flex-col space-y-3 transition duration-300">
                    <div class="w-9 h-9 rounded-full bg-[#8b5cf6]/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Smart Learning</h3>
                        <p class="text-[10px] text-slate-400 mt-1 font-semibold leading-relaxed">Interactive courses and digital content.</p>
                    </div>
                </div>

                <!-- Track Progress -->
                <div class="bg-white/[0.03] hover:bg-white/[0.05] border border-white/[0.06] rounded-2xl p-4 flex flex-col space-y-3 transition duration-300">
                    <div class="w-9 h-9 rounded-full bg-[#8b5cf6]/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Track Progress</h3>
                        <p class="text-[10px] text-slate-400 mt-1 font-semibold leading-relaxed">Monitor your learning journey.</p>
                    </div>
                </div>

                <!-- Achieve Goals -->
                <div class="bg-white/[0.03] hover:bg-white/[0.05] border border-white/[0.06] rounded-2xl p-4 flex flex-col space-y-3 transition duration-300">
                    <div class="w-9 h-9 rounded-full bg-[#8b5cf6]/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Achieve Goals</h3>
                        <p class="text-[10px] text-slate-400 mt-1 font-semibold leading-relaxed">Build skills and shape your future.</p>
                    </div>
                </div>
            </div>

            <!-- Quote Container -->
            <div class="bg-white/[0.02] border border-white/[0.05] rounded-2xl p-5 max-w-xl shadow-lg relative overflow-hidden backdrop-blur-md">
                <p class="text-xs font-medium text-slate-300 italic leading-relaxed">
                    “ Education is the most powerful weapon which you can use to change the world. ”
                </p>
                <p class="text-[11px] font-bold text-[#8b5cf6] mt-2 uppercase tracking-widest">— Nelson Mandela</p>
            </div>
        </div>

        <!-- Right Column: Glassmorphic Register Card -->
        <div class="lg:col-span-5 flex justify-center">
            <div class="w-full max-w-[480px] bg-[#120e2b]/90 border border-white/[0.07] rounded-3xl p-6 md:p-8 shadow-2xl relative overflow-hidden backdrop-blur-2xl transition duration-300 hover:border-white/[0.1] hover:shadow-purple-500/5">
                
                <!-- Card Header -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-[#8b5cf6]/10 flex items-center justify-center shrink-0 border border-[#8b5cf6]/20">
                        <svg class="w-5 h-5 text-[#8b5cf6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white tracking-tight" style="font-family: var(--font-display);">Join Nabha Learning</h2>
                        <p class="text-xs text-slate-400 font-semibold mt-0.5">Create your student or teacher account</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="mb-5 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl px-4 py-3 text-xs font-semibold">
                        <ul class="space-y-1">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('register.post') }}" class="space-y-4"
                      x-data="{ role: '{{ old('role', 'student') }}' }">
                    @csrf

                    {{-- Role selector --}}
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <label class="cursor-pointer group">
                            <input type="radio" name="role" value="student" x-model="role"
                                   {{ old('role', 'student') === 'student' ? 'checked' : '' }} class="hidden">
                            <div :class="role==='student' ? 'border-[#8b5cf6]/60 bg-[#8b5cf6]/15 text-[#8b5cf6]' : 'border-white/[0.08] bg-white/[0.02] text-slate-450'"
                                 class="p-3 border rounded-2xl text-xs font-bold text-center transition duration-200 hover:border-[#8b5cf6]/40">
                                <div class="text-2xl mb-1">👨&zwj;🎓</div>
                                Student
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="role" value="teacher" x-model="role"
                                   {{ old('role') === 'teacher' ? 'checked' : '' }} class="hidden">
                            <div :class="role==='teacher' ? 'border-emerald-500/60 bg-emerald-500/15 text-emerald-400' : 'border-white/[0.08] bg-white/[0.02] text-slate-450'"
                                 class="p-3 border rounded-2xl text-xs font-bold text-center transition duration-200 hover:border-emerald-500/40">
                                <div class="text-2xl mb-1">👨&zwj;🏫</div>
                                Teacher
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="Enter your full name">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="Enter your email">
                        </div>
                    </div>

                    {{-- Student fields --}}
                    <div x-show="role==='student'" x-cloak style="display: none;" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Your Class</label>
                            <select name="class_level"
                                    class="w-full px-4 py-3 bg-[#161233] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium">
                                <option value="">Select your class</option>
                                @foreach(['Class 6','Class 7','Class 8','Class 9','Class 10'] as $class)
                                    <option value="{{ $class }}" {{ old('class_level')===$class ? 'selected':'' }}>{{ $class }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Teacher fields --}}
                    <div x-show="role==='teacher'" x-cloak style="display: none;" class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Subject Specialization</label>
                            <select name="subject_specialization"
                                    class="w-full px-4 py-3 bg-[#161233] border border-white/[0.08] rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium">
                                <option value="">Select your subject</option>
                                @foreach(['Mathematics','Science','English','Hindi','Social Studies','Physical Education'] as $subj)
                                    <option value="{{ $subj }}" {{ old('subject_specialization')===$subj ? 'selected':'' }}>{{ $subj }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Qualification</label>
                            <input type="text" name="qualification" value="{{ old('qualification') }}"
                                   class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="e.g. B.Ed, M.Sc, B.Tech">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Phone Number (Optional)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </span>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="w-full pl-11 pr-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="Min. 8 chars">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Confirm</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-xl text-white placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-[#8b5cf6]/40 focus:border-[#8b5cf6]/30 transition text-xs font-medium"
                                   placeholder="Repeat password">
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full bg-gradient-to-r from-[#8b5cf6] to-[#6366f1] hover:from-[#7c3aed] hover:to-[#4f46e5] text-white font-bold py-3.5 rounded-xl transition duration-300 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/35 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2 text-xs uppercase tracking-wider mt-2">
                        <span>Create Account</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </form>

                <div class="relative flex py-4 items-center">
                    <div class="flex-grow border-t border-white/[0.05]"></div>
                    <span class="flex-shrink mx-4 text-[10px] text-slate-500 font-extrabold uppercase tracking-widest">or</span>
                    <div class="flex-grow border-t border-white/[0.05]"></div>
                </div>

                <p class="text-center text-xs text-slate-450 font-bold">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-[#8b5cf6] hover:underline font-extrabold ml-1">Sign In Here</a>
                </p>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full flex items-center justify-between z-20 relative max-w-7xl mx-auto pt-6 border-t border-white/[0.03] text-[10px] text-slate-500 font-semibold">
        <p>© 2026 Nabha Digital Learning, All rights reserved.</p>
        <p class="text-[#8b5cf6] font-bold">Rural Education Mission</p>
    </footer>
</div>

<script>
document.querySelectorAll('a[data-lang]').forEach((el) => {
    el.addEventListener('click', () => {
        localStorage.setItem('applocale', el.getAttribute('data-lang'));
    });
});
</script>

<style>
    /* Full bleed background on left for large devices */
    @media (min-width: 1024px) {
        .min-h-screen {
            background-image: linear-gradient(to right, rgba(9, 6, 22, 0.4) 0%, rgba(9, 6, 22, 0.8) 50%, rgba(9, 6, 22, 0.95) 75%, #090616 100%), url('/images/login_bg.png?v=2026');
            background-size: auto 100%, cover;
            background-position: left center, left center;
            background-repeat: no-repeat;
        }
    }
    
    /* Mobile-first fallback background */
    @media (max-width: 1023px) {
        .min-h-screen {
            background-image: linear-gradient(to bottom, rgba(9, 6, 22, 0.4) 0%, rgba(9, 6, 22, 0.8) 60%, rgba(9, 6, 22, 0.95) 85%, #090616 100%), url('/images/login_bg.png?v=2026');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }
    }
    
    h1, h2, button {
        font-family: var(--font-display), sans-serif;
    }
    
    .text-slate-350 {
        color: #cbd5e1;
    }
    
    .text-slate-450 {
        color: #94a3b8;
    }
</style>
@endsection
