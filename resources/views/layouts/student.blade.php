@extends('layouts.app')

@section('content')
<div class="student-portal min-h-screen" x-data="{ sidebarOpen: false }" style="background: var(--portal-bg); color: var(--text-primary);">
    <!-- Top Navbar -->
    <nav class="glass-strong sticky top-0 z-40 border-b border-gray-200 dark:border-white/[0.06]">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-gray-500 dark:text-white/60 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white text-base font-bold shadow-lg shadow-violet-500/20">N</div>
                        <span class="font-bold text-base text-gray-800 dark:text-white/90 tracking-tight hidden sm:block" style="font-family: var(--font-display);">{{ __('messages.platform_short_name') }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Language Switcher -->
                    <div x-data="{ langOpen: false }" class="relative">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-xs font-semibold text-gray-500 dark:text-white/50 hover:text-gray-800 dark:hover:text-white/80 px-2 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.04] transition">
                            {{ strtoupper(app()->getLocale()) }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-28 glass rounded-xl py-1 z-50 text-sm shadow-xl">
                            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="block px-4 py-2 text-gray-600 dark:text-white/70 hover:text-violet-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">{{ __('messages.lang_english') }}</a>
                            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="block px-4 py-2 text-gray-600 dark:text-white/70 hover:text-violet-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">{{ __('messages.lang_hindi') }}</a>
                            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="block px-4 py-2 text-gray-600 dark:text-white/70 hover:text-violet-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/[0.06] transition">{{ __('messages.lang_punjabi') }}</a>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg text-gray-500 dark:text-white/40 hover:text-gray-800 dark:hover:text-white/70 hover:bg-gray-100 dark:hover:bg-white/[0.04] transition" aria-label="Toggle dark mode">
                        <!-- Sun Icon (shows in Dark Mode) -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464a1 1 0 10-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <!-- Moon Icon (shows in Light Mode) -->
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 text-gray-500 dark:text-white/50" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 2.001 0 1010.586 10.586z"></path></svg>
                    </button>

                    <!-- Notifications -->
                    <div x-data="{
                        open: false,
                        unreadCount: {{ auth()->user()->unreadNotifications->count() }},
                        loading: false,
                        async markAllRead() {
                            if (this.unreadCount === 0) return;
                            this.loading = true;
                            try {
                                const response = await fetch('{{ route('notifications.mark-all-read') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-Token': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                });
                                const res = await response.json();
                                if (res.success) {
                                    this.unreadCount = 0;
                                    document.querySelectorAll('.student-notification-item').forEach(el => {
                                        el.classList.remove('bg-violet-500/[0.04]', 'dark:bg-violet-500/[0.06]');
                                    });
                                    if (window.toast) {
                                        window.toast('success', 'All notifications marked as read.');
                                    }
                                }
                            } catch (err) {
                                console.error('Notifications Error:', err);
                                if (window.toast) {
                                    window.toast('error', 'Failed to update notifications.');
                                }
                            } finally {
                                this.loading = false;
                            }
                        }
                    }" class="relative">
                        <button @click="open = !open" class="relative p-2 rounded-lg text-gray-500 dark:text-white/40 hover:text-gray-800 dark:hover:text-white/70 hover:bg-gray-100 dark:hover:bg-white/[0.04] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-1 right-1 w-4 h-4 bg-violet-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center shadow-lg shadow-violet-500/30 animate-pulse"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             class="absolute right-0 mt-2 w-80 glass rounded-2xl overflow-hidden z-50 shadow-2xl">
                             <div class="p-4 border-b border-gray-150 dark:border-white/[0.06] flex justify-between items-center bg-gray-50 dark:bg-white/5">
                                 <h3 class="font-bold text-gray-800 dark:text-white/90 text-sm flex items-center gap-1.5">
                                     {{ __('messages.notifications') }}
                                     <span x-show="loading" class="w-3 h-3 border-2 border-violet-500/30 border-t-violet-500 rounded-full animate-spin"></span>
                                 </h3>
                                 <button @click="markAllRead()" :disabled="unreadCount === 0 || loading" class="text-[10px] text-violet-500 hover:text-violet-400 disabled:opacity-40 disabled:no-underline font-semibold transition">
                                     <span x-show="!loading">{{ __('messages.mark_all_read') }}</span>
                                     <span x-show="loading">Updating...</span>
                                 </button>
                             </div>
                             <div class="max-h-72 overflow-y-auto">
                                <div x-show="unreadCount > 0" class="divide-y divide-gray-100 dark:divide-white/[0.04]">
                                    @foreach(auth()->user()->notifications->take(10) as $notification)
                                        <div class="student-notification-item p-4 border-b border-gray-100 dark:border-white/[0.04] {{ $notification->unread() ? 'bg-violet-500/[0.04] dark:bg-violet-500/[0.06]' : '' }} text-left">
                                            <p class="text-xs font-semibold text-gray-800 dark:text-white/80">{{ $notification->data['title'] ?? __('messages.notification_update') }}</p>
                                            <p class="text-[11px] text-gray-500 dark:text-white/40 mt-0.5">{{ $notification->data['message'] }}</p>
                                            <p class="text-[10px] text-gray-400 dark:text-white/20 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <div x-show="unreadCount === 0" class="p-8 text-center text-gray-400 dark:text-white/30 text-xs flex flex-col items-center gap-2 animate-fade-in">
                                    <span class="text-2xl">✨</span>
                                    <span>All notifications are read</span>
                                </div>
                             </div>
                        </div>
                    </div>

                    @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                    <a href="{{ route('student.cart') }}" class="relative px-2.5 py-1.5 rounded-lg text-gray-500 dark:text-white/40 hover:text-gray-800 dark:hover:text-white/70 hover:bg-gray-100 dark:hover:bg-white/[0.04] transition text-xs font-medium" title="{{ __('messages.cart') }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 bg-cyan-500 text-gray-900 text-[9px] font-bold rounded-full flex items-center justify-center px-0.5">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- Profile Info in Navbar -->
                    <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-gray-200 dark:border-white/[0.06] ml-1">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-lg ring-1 ring-violet-500/20" alt="Avatar">
                        <span class="text-xs text-gray-600 dark:text-white/60 font-medium">{{ auth()->user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-gray-500 dark:text-white/30 hover:text-gray-800 dark:hover:text-white/60 px-2.5 py-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/[0.04] font-medium transition">
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 glass transform transition-all duration-300 md:translate-x-0 md:sticky md:top-16 h-[calc(100vh-4rem)] border-r border-gray-200 dark:border-white/[0.04]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-5 mt-16 md:mt-0 overflow-y-auto h-full">
                <!-- User Badge -->
                <div class="flex items-center gap-3 p-3.5 glass-card mb-6 bg-white/50 dark:bg-white/5">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-xl ring-2 ring-violet-500/30" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm text-gray-800 dark:text-white/90">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-white/40">{{ auth()->user()->class_level }}</p>
                    </div>
                </div>
                <nav class="space-y-5">
                    @php
                        $modules = [
                            __('messages.learning' ) => [
                                ['route' => 'student.courses',    'label' => __('messages.courses'),      'icon' => '📚'],
                                ['route' => 'student.live-classes', 'label' => __('messages.live_classes'), 'icon' => '🔴'],
                                ['route' => 'student.schemes',    'label' => __('messages.schemes'),      'icon' => '🏛️'],
                            ],
                            __('messages.assessments') => [
                                ['route' => 'student.quizzes',    'label' => __('messages.quizzes'),      'icon' => '⚡'],
                            ],
                            __('messages.communication') => [
                                ['route' => 'student.mentors',    'label' => __('messages.mentors'),      'icon' => '👥'],
                                ['route' => 'student.chatbot',    'label' => __('messages.ai_chatbot'),   'icon' => '🤖'],
                                ['route' => 'student.complaints', 'label' => __('messages.complaints'),   'icon' => '💬'],
                            ],
                            __('messages.account') => [
                                ['route' => 'student.dashboard',  'label' => __('messages.dashboard'),    'icon' => '🏠'],
                                ['route' => 'student.cart',       'label' => __('messages.cart'),          'icon' => '🛒'],
                                ['route' => 'student.profile',    'label' => __('messages.my_profile'),   'icon' => '👤'],
                            ]
                        ];
                    @endphp

                    @foreach($modules as $moduleName => $links)
                        <div>
                            <h3 class="text-[10px] font-bold text-gray-400 dark:text-white/20 uppercase tracking-[0.15em] mb-2 px-3">{{ $moduleName }}</h3>
                            <div class="space-y-0.5">
                                @foreach($links as $link)
                                    <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium transition-all duration-200
                                              {{ request()->routeIs($link['route']) ? 'bg-violet-500/10 dark:bg-violet-500/15 text-violet-700 dark:text-violet-300 shadow-sm border border-violet-500/15' : 'text-gray-500 dark:text-white/45 hover:text-gray-900 dark:hover:text-white/70 hover:bg-gray-100 dark:hover:bg-white/[0.04]' }}">
                                        <span class="text-sm opacity-70">{{ $link['icon'] }}</span>
                                        <span class="flex-1">{{ $link['label'] }}</span>
                                        @if($link['route'] === 'student.cart' && $cartCount > 0)
                                            <span class="bg-cyan-400/20 text-cyan-700 dark:text-cyan-300 text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ $cartCount }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
        </aside>

        <!-- Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-20 md:hidden" x-cloak></div>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8 mb-16 md:mb-0">
            @yield('student-content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full glass-strong border-t border-gray-200 dark:border-white/[0.06] z-40 flex items-center justify-around pb-safe">
        <a href="{{ route('student.dashboard') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.dashboard') ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-white/30' }}">
            <span class="text-lg leading-none mb-0.5">🏠</span>
            <span class="text-[9px] font-semibold">{{ __('messages.home') }}</span>
        </a>
        <a href="{{ route('student.courses') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.courses') ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-white/30' }}">
            <span class="text-lg leading-none mb-0.5">📚</span>
            <span class="text-[9px] font-semibold">{{ __('messages.courses') }}</span>
        </a>
        <a href="{{ route('student.live-classes') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.live-classes') ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-white/30' }}">
            <span class="text-lg leading-none mb-0.5">🔴</span>
            <span class="text-[9px] font-semibold">{{ __('messages.live_classes') }}</span>
        </a>
        <a href="{{ route('student.profile') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.profile') ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-white/30' }}">
            <span class="text-lg leading-none mb-0.5">👤</span>
            <span class="text-[9px] font-semibold">{{ __('messages.my_profile') }}</span>
        </a>
    </nav>

    {{-- Floating Chatbot Widget --}}
    @include('components.chatbot-widget')

    <!-- Shared Mentor Email Modal -->
    <div x-data="{ 
            open: false, 
            subject: '', 
            message: '', 
            sending: false,
            init() {
                window.addEventListener('open-mentor-email', () => {
                    this.subject = '';
                    this.message = '';
                    this.open = true;
                });
            },
            async sendEmail() {
                if (!this.subject.trim() || !this.message.trim()) {
                    window.toast('error', 'Please fill in both fields.');
                    return;
                }
                this.sending = true;
                try {
                    const response = await fetch('{{ route('student.mentors.email') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSR-Token': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            subject: this.subject,
                            message: this.message
                        })
                    });
                    const res = await response.json();
                    if (res.success) {
                        window.toast('success', res.message);
                        this.open = false;
                    } else {
                        window.toast('error', res.message || 'Something went wrong.');
                    }
                } catch (err) {
                    window.toast('error', 'Failed to connect. Please try again.');
                } finally {
                    this.sending = false;
                }
            }
         }" 
         x-show="open" 
         x-cloak
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    >
        <!-- Modal Backdrop -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false" 
             class="absolute inset-0 bg-black/70 backdrop-blur-md"
        ></div>

        <!-- Modal Wrapper -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative w-full max-w-lg bg-[#0d1026] dark:bg-[#0d1026] border border-gray-200 dark:border-white/[0.08] rounded-3xl shadow-2xl overflow-hidden glow-violet p-6 sm:p-8 text-left"
        >
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500"></div>
            
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center text-lg">✉</div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-white/90 text-sm" style="font-family: var(--font-display);">Send Email to Mentor</h3>
                        <p class="text-[10px] text-gray-400 dark:text-white/40 mt-0.5">Your email will automatically route to alanemohan@gmail.com</p>
                    </div>
                </div>
                <button @click="open = false" class="p-1 rounded-lg text-gray-400 dark:text-white/40 hover:text-gray-800 dark:hover:text-white/80 hover:bg-gray-100 dark:hover:bg-white/[0.06] transition text-lg leading-none">&times;</button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-white/50 mb-1.5 uppercase tracking-wider">Subject</label>
                    <input type="text" x-model="subject" placeholder="e.g., Doubts in Class 10 Math Lesson 3"
                           class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.06] rounded-xl text-sm text-gray-800 dark:text-white placeholder-gray-450 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 dark:text-white/50 mb-1.5 uppercase tracking-wider">Message</label>
                    <textarea x-model="message" rows="5" placeholder="Write your message to the mentor here..."
                              class="w-full px-4 py-3 bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/[0.06] rounded-xl text-sm text-gray-800 dark:text-white placeholder-gray-450 dark:placeholder-white/20 focus:outline-none focus:ring-2 focus:ring-violet-500/40 transition resize-none"></textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button @click="open = false" class="px-5 py-2.5 bg-gray-100 dark:bg-white/[0.04] border border-gray-200 dark:border-white/[0.08] hover:bg-gray-200 dark:hover:bg-white/[0.08] text-gray-600 dark:text-white/80 text-xs font-semibold rounded-xl transition">
                    Cancel
                </button>
                <button @click="sendEmail()" :disabled="sending" 
                        class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white px-6 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-lg shadow-violet-500/20 disabled:opacity-50 flex items-center gap-2">
                    <span x-show="sending" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span x-text="sending ? 'Sending...' : 'Send Message'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('a[data-lang]').forEach((el) => {
    el.addEventListener('click', () => {
        localStorage.setItem('applocale', el.getAttribute('data-lang'));
    });
});
</script>
@endpush
