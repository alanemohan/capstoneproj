@extends('layouts.app')

@section('content')
<div class="admin-portal min-h-screen bg-white" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
    <!-- Top Navbar -->
    <nav class="bg-white border-b border-slate-200/80 sticky top-0 z-40 shadow-sm">
        <div class="px-4 sm:px-6">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:flex p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-white text-[10px] font-bold">N</div>
                        <div class="hidden sm:block">
                            <span class="font-bold text-sm text-slate-900 tracking-tight">{{ __('messages.platform_name') }}</span>
                            <span class="text-[10px] text-orange-500 font-bold ml-1.5 px-1.5 py-0.5 bg-orange-50 rounded">{{ __('messages.admin') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Language -->
                    <div x-data="{ langOpen: false }" class="relative">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-slate-700 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition">
                            {{ strtoupper(app()->getLocale()) }}
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-1 w-28 bg-white rounded-lg shadow-lg py-1 z-50 text-xs border border-slate-200">
                            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="block px-3 py-2 hover:bg-slate-50 text-slate-600">{{ __('messages.lang_english') }}</a>
                            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="block px-3 py-2 hover:bg-slate-50 text-slate-600">{{ __('messages.lang_hindi') }}</a>
                            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="block px-3 py-2 hover:bg-slate-50 text-slate-600">{{ __('messages.lang_punjabi') }}</a>
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
                                    document.querySelectorAll('.admin-notification-item').forEach(el => {
                                        el.classList.remove('bg-orange-50/40', 'bg-amber-50/40');
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
                        <button @click="open = !open" class="relative p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-1 right-1 w-4 h-4 bg-orange-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center animate-pulse"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl overflow-hidden z-50 border border-slate-200">
                             <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                 <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                                     {{ __('messages.notifications') }}
                                     <span x-show="loading" class="w-3 h-3 border-2 border-orange-500/30 border-t-orange-500 rounded-full animate-spin"></span>
                                 </h3>
                                 <button @click="markAllRead()" :disabled="unreadCount === 0 || loading" class="text-[10px] text-orange-600 hover:text-orange-700 disabled:opacity-40 disabled:no-underline hover:underline font-semibold transition">
                                     <span x-show="!loading">{{ __('messages.mark_all_read') }}</span>
                                     <span x-show="loading">Updating...</span>
                                 </button>
                             </div>
                             <div class="max-h-80 overflow-y-auto">
                                <div x-show="unreadCount > 0" class="divide-y divide-slate-100">
                                    @foreach(\App\Models\User::where('role', 'student')->where('status', 'pending')->get() as $pStud)
                                        <a href="{{ route('admin.users') }}" class="admin-notification-item block p-3.5 border-b border-slate-50 bg-amber-50/40 hover:bg-slate-50 transition">
                                            <div class="flex items-start gap-2.5">
                                                <span class="text-sm">🎓</span>
                                                <div class="text-left">
                                                    <p class="text-xs font-semibold text-slate-800">New Student Registered</p>
                                                    <p class="text-[11px] text-slate-500 mt-0.5"><span class="font-bold">{{ $pStud->name }}</span> is pending approval.</p>
                                                    <p class="text-[10px] text-slate-400 mt-1">{{ $pStud->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                    @foreach(\App\Models\User::where('role', 'teacher')->where('status', 'pending')->get() as $pTeach)
                                        <a href="{{ route('admin.teachers') }}" class="admin-notification-item block p-3.5 border-b border-slate-50 bg-amber-50/40 hover:bg-slate-50 transition">
                                            <div class="flex items-start gap-2.5">
                                                <span class="text-sm">👨‍🏫</span>
                                                <div class="text-left">
                                                    <p class="text-xs font-semibold text-slate-800">New Teacher Registered</p>
                                                    <p class="text-[11px] text-slate-500 mt-0.5"><span class="font-bold">{{ $pTeach->name }}</span> is pending approval.</p>
                                                    <p class="text-[10px] text-slate-400 mt-1">{{ $pTeach->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                    @foreach(auth()->user()->notifications->take(10) as $notification)
                                        <div class="admin-notification-item p-3.5 border-b border-slate-50 {{ $notification->unread() ? 'bg-orange-50/40' : '' }} hover:bg-slate-50 transition text-left">
                                            <p class="text-xs font-semibold text-slate-700">{{ $notification->data['title'] ?? __('messages.notification_update') }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">{{ $notification->data['message'] }}</p>
                                            <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                <div x-show="unreadCount === 0" class="p-8 text-center text-slate-400 text-xs flex flex-col items-center gap-2 animate-fade-in">
                                    <span class="text-2xl">✨</span>
                                    <span>All notifications are read</span>
                                </div>
                             </div>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-2 pl-2 border-l border-slate-200 ml-1">
                        <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-lg" alt="">
                        <span class="text-xs text-slate-600 font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-slate-400 hover:text-slate-600 px-2.5 py-1.5 rounded-lg hover:bg-slate-100 font-medium transition">{{ __('messages.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar - Deep Navy -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-[#0f172a] text-slate-400 transform transition-all duration-300 md:translate-x-0 md:sticky md:top-14 h-[calc(100vh-3.5rem)] border-r border-slate-800/50 md:z-30 shadow-lg md:shadow-none"
               :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'w-16' : 'w-60']"
               x-data="{ search: '' }">
            <div class="p-4 mt-14 md:mt-0 overflow-y-auto h-full scrollbar-thin">
                <!-- User Badge -->
                <div class="flex items-center gap-3 p-3 bg-white/[0.04] rounded-xl mb-5 whitespace-nowrap overflow-hidden transition-all duration-300" x-show="!sidebarCollapsed">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-lg flex-shrink-0" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm text-slate-200">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 font-medium">{{ __('messages.administrator') }}</p>
                    </div>
                </div>
                <!-- Search -->
                <div class="mb-4" x-show="!sidebarCollapsed">
                    <input type="text" x-model="search" placeholder="{{ __('messages.search_tools') }}"
                        class="w-full bg-white/[0.04] border border-white/[0.06] rounded-lg py-2 px-3 text-xs text-slate-300 placeholder-slate-600 focus:ring-1 focus:ring-orange-500/50 focus:bg-white/[0.06] transition-all">
                </div>

                <!-- Navigation -->
                <nav class="space-y-4">
                        @php
                            $modules = [
                                __('messages.learning') => [
                                    ['route' => 'admin.content',     'label' => __('messages.content_review'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'],
                                    ['route' => 'admin.courses',     'label' => __('messages.course_approval'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'],
                                    ['route' => 'admin.quizzes',     'label' => __('messages.quiz_approval'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'],
                                    ['route' => 'admin.scholarships.index', 'label' => __('messages.scholarships_cms'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'],
                                    ['route' => 'admin.schemes.index', 'label' => __('messages.gov_schemes_cms'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'],
                                ],
                                __('messages.assessments') => [
                                    ['route' => 'admin.reports',     'label' => __('messages.student_reports'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>'],
                                    ['route' => 'admin.reconciliation.index', 'label' => __('messages.payments_revenue'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'],
                                ],
                                __('messages.communication') => [
                                    ['route' => 'admin.announcements.index','label' => __('messages.notice_board'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>'],
                                    ['route' => 'admin.complaints.index',   'label' => __('messages.complaints'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>'],
                                    ['route' => 'admin.chatbot-qa',  'label' => __('messages.chatbot_training'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'],
                                ],
                                __('messages.account') => [
                                    ['route' => 'admin.dashboard',   'label' => __('messages.dashboard'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>'],
                                    ['route' => 'admin.users',       'label' => __('messages.manage_users'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
                                    ['route' => 'admin.students_manager.index', 'label' => __('messages.student_master'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'],
                                    ['route' => 'admin.teachers_manager.index', 'label' => __('messages.teacher_master'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'],
                                    ['route' => 'admin.mentor-management.index', 'label' => __('messages.mentor_management'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>'],
                                    ['route' => 'admin.teachers',    'label' => __('messages.teacher_approvals'), 'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'],
                                ]
                            ];
                        @endphp
 
                        @foreach($modules as $moduleName => $links)
                            <div x-show="!sidebarCollapsed ? Object.values(@js($links)).some(l => l.label.toLowerCase().includes(search.toLowerCase())) : true">
                                <h3 x-show="!sidebarCollapsed" class="text-[9px] font-bold text-slate-600 uppercase tracking-[0.15em] mb-1.5 px-2 select-none">{{ $moduleName }}</h3>
                                <div class="space-y-0.5">
                                    @foreach($links as $link)
                                        <a href="{{ route($link['route']) }}"
                                           x-show="!sidebarCollapsed ? '{{ $link['label'] }}'.toLowerCase().includes(search.toLowerCase()) : true"
                                           class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-xs font-medium transition-all duration-150 whitespace-nowrap
                                                  {{ request()->routeIs($link['route']) ? 'bg-orange-500/10 text-orange-400 border border-orange-500/15 font-semibold' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' }}"
                                           :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between'"
                                           :title="sidebarCollapsed ? '{{ $link['label'] }}' : ''">
                                            <div class="flex items-center gap-2.5" :class="sidebarCollapsed ? 'justify-center' : ''">
                                                <span class="{{ request()->routeIs($link['route']) ? 'text-orange-400' : 'text-slate-500 hover:text-slate-400' }}">{!! $link['icon'] !!}</span>
                                                <span x-show="!sidebarCollapsed" class="transition-all duration-200">{{ $link['label'] }}</span>
                                            </div>
                                            @if($link['route'] === 'admin.users' && \App\Models\User::where('role', 'student')->where('status', 'pending')->count() > 0)
                                                <span x-show="!sidebarCollapsed" class="bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ \App\Models\User::where('role', 'student')->where('status', 'pending')->count() }}</span>
                                            @endif
                                            @if($link['route'] === 'admin.teachers' && \App\Models\User::where('role', 'teacher')->where('status', 'pending')->count() > 0)
                                                <span x-show="!sidebarCollapsed" class="bg-amber-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">{{ \App\Models\User::where('role', 'teacher')->where('status', 'pending')->count() }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden" x-cloak></div>

        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8">
            @yield('admin-content')
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
