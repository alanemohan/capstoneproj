@extends('layouts.app')

@section('content')
<div class="teacher-portal min-h-screen bg-[#fafafa]" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <!-- Top Navbar — ultra-clean -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-1.5 rounded-md text-gray-500 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:flex p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                    </button>
                    <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-md bg-emerald-600 flex items-center justify-center text-white text-[11px] font-bold">N</div>
                        <span class="font-semibold text-sm text-gray-900 tracking-tight hidden sm:block">{{ __('messages.platform_short_name') }}</span>
                        <span class="text-[10px] text-gray-400 font-medium hidden sm:block">/ {{ __('messages.teacher_portal') }}</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Language -->
                    <div x-data="{ langOpen: false }" class="relative">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-xs font-medium text-gray-500 hover:text-gray-700 px-2.5 py-1.5 rounded-md hover:bg-gray-100 transition">
                            {{ strtoupper(app()->getLocale()) }}
                            <svg class="w-3 h-3 text-gray-400 transition-transform duration-200" :class="langOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-1.5 w-28 bg-white rounded-lg shadow-lg py-1 z-50 text-xs border border-gray-200">
                            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="block px-3 py-1.5 hover:bg-gray-50 text-gray-600">{{ __('messages.lang_english') }}</a>
                            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="block px-3 py-1.5 hover:bg-gray-50 text-gray-600">{{ __('messages.lang_hindi') }}</a>
                            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="block px-3 py-1.5 hover:bg-gray-50 text-gray-600">{{ __('messages.lang_punjabi') }}</a>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" aria-label="Toggle dark mode">
                        <!-- Sun Icon (shows in Dark Mode) -->
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464a1 1 0 10-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <!-- Moon Icon (shows in Light Mode) -->
                        <svg id="theme-toggle-dark-icon" class="w-5 h-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 2.001 0 1010.586 10.586z"></path></svg>
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
                                    document.querySelectorAll('.teacher-notification-item').forEach(el => {
                                        el.classList.remove('bg-emerald-50/30');
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
                        <button @click="open = !open" class="relative p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-1 right-1 w-3.5 h-3.5 bg-emerald-500 text-white text-[8px] font-bold rounded-full flex items-center justify-center animate-pulse"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-1.5 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 border border-gray-200">
                             <div class="p-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                 <h3 class="font-semibold text-gray-800 text-xs flex items-center gap-1.5">
                                     {{ __('messages.notifications') }}
                                     <span x-show="loading" class="w-3 h-3 border-2 border-emerald-500/30 border-t-emerald-500 rounded-full animate-spin"></span>
                                 </h3>
                                 <button @click="markAllRead()" :disabled="unreadCount === 0 || loading" class="text-[10px] text-emerald-600 hover:text-emerald-700 disabled:opacity-40 disabled:no-underline hover:underline font-medium transition">
                                     <span x-show="!loading">{{ __('messages.mark_all_read') }}</span>
                                     <span x-show="loading">Updating...</span>
                                 </button>
                             </div>
                             <div class="max-h-72 overflow-y-auto">
                                <div x-show="unreadCount > 0" class="divide-y divide-gray-50">
                                    @foreach(auth()->user()->notifications->take(10) as $notification)
                                        <div class="teacher-notification-item p-3 border-b border-gray-50 {{ $notification->unread() ? 'bg-emerald-50/30' : '' }} text-left">
                                            <p class="text-xs font-medium text-gray-700">{{ $notification->data['title'] ?? __('messages.notification_update') }}</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $notification->data['message'] }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <div x-show="unreadCount === 0" class="p-8 text-center text-gray-400 text-xs flex flex-col items-center gap-2 animate-fade-in">
                                    <span class="text-2xl">✨</span>
                                    <span>All notifications are read</span>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Profile Info in Navbar -->
                    <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-gray-200 ml-1">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-lg ring-1 ring-emerald-500/20" alt="Avatar">
                        <span class="text-xs text-gray-600 font-medium">{{ auth()->user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 font-medium transition ml-1">
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar — minimal and tight -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-gray-200 transform transition-all duration-300 md:translate-x-0 md:sticky md:top-14 h-[calc(100vh-3.5rem)] md:z-30 shadow-lg md:shadow-none"
               :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'w-16' : 'w-56']"
               x-data="{ search: '' }">
            <div class="p-4 mt-14 md:mt-0 overflow-y-auto h-full scrollbar-thin">
                <div class="flex items-center gap-2.5 p-2.5 bg-gray-50 rounded-lg mb-4 border border-gray-100 whitespace-nowrap overflow-hidden transition-all duration-300" x-show="!sidebarCollapsed">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-md flex-shrink-0" alt="Avatar">
                    <div>
                        <p class="font-semibold text-xs text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-gray-400">{{ auth()->user()->subject_specialization ?? __('messages.teacher') }}</p>
                    </div>
                </div>
                <!-- Search -->
                <div class="mb-4" x-show="!sidebarCollapsed">
                    <input type="text" x-model="search" placeholder="{{ __('messages.search_tools') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-md py-1.5 px-2.5 text-xs text-gray-700 placeholder-gray-400 focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition">
                </div>

                <nav class="space-y-4">
                    @php
                        $modules = [
                            __('messages.learning') => [
                                ['route' => 'teacher.lessons',          'label' => __('messages.my_lessons'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'],
                                ['route' => 'teacher.lessons.create',   'label' => __('messages.upload_lesson'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>'],
                                ['route' => 'teacher.courses',          'label' => __('messages.my_courses'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'],
                                ['route' => 'teacher.live-classes.index', 'label' => __('messages.live_classes'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'],
                            ],
                            __('messages.assessments') => [
                                ['route' => 'teacher.quizzes',          'label' => __('messages.my_quizzes'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'],
                                ['route' => 'teacher.quizzes.create',   'label' => __('messages.create_quiz'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
                                ['route' => 'teacher.analytics',        'label' => __('messages.view_analytics'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'],
                                ['route' => 'teacher.student.progress', 'label' => __('messages.student_progress'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>'],
                            ],
                            __('messages.communication') => [
                                ['route' => 'teacher.announcements.index', 'label' => __('messages.announcements'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>'],
                                ['route' => 'teacher.chatbot-qa',       'label' => __('messages.chatbot_training'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'],
                            ],
                            __('messages.account') => [
                                ['route' => 'teacher.dashboard',        'label' => __('messages.dashboard'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>'],
                                ['route' => 'teacher.profile',          'label' => __('messages.my_profile'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'],
                            ]
                        ];
                    @endphp

                    @foreach($modules as $moduleName => $links)
                        <div x-show="!sidebarCollapsed ? Object.values(@js($links)).some(l => l.label.toLowerCase().includes(search.toLowerCase())) : true">
                            <h3 x-show="!sidebarCollapsed" class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.12em] mb-1.5 px-2 select-none">{{ $moduleName }}</h3>
                            <div class="space-y-0.5">
                                @foreach($links as $link)
                                    <a href="{{ route($link['route']) }}"
                                       x-show="!sidebarCollapsed ? '{{ $link['label'] }}'.toLowerCase().includes(search.toLowerCase()) : true"
                                       class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all duration-150 whitespace-nowrap
                                              {{ request()->routeIs($link['route']) ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-2 border-emerald-500' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-50' }}"
                                       :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                                       :title="sidebarCollapsed ? '{{ $link['label'] }}' : ''">
                                        <div class="flex items-center gap-2.5" :class="sidebarCollapsed ? 'justify-center' : ''">
                                            <span class="{{ request()->routeIs($link['route']) ? 'text-emerald-600' : 'text-gray-400' }}">{!! $link['icon'] !!}</span>
                                            <span x-show="!sidebarCollapsed" class="transition-all duration-200">{{ $link['label'] }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 md:hidden backdrop-blur-sm" x-cloak></div>

        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8 pb-32">
            @yield('teacher-content')
        </main>
    </div>

    {{-- Floating Chatbot Widget --}}
    @include('components.chatbot-widget')
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
