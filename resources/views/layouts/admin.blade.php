@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
    <nav class="bg-gray-900 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-gray-700 text-sm font-medium">
                        {{ __('messages.menu') }}
                    </button>
                    <div>
                        <span class="font-bold text-lg hidden sm:block">{{ __('messages.platform_name') }}</span>
                        <span class="text-xs text-gray-400 hidden sm:block">{{ __('messages.admin_panel') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div x-data="{ langOpen: false }" class="relative">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-sm font-medium hover:text-gray-200">
                            {{ strtoupper(app()->getLocale()) }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-24 bg-white text-gray-800 rounded-lg shadow-xl py-1 z-50 text-sm">
                            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_english') }}</a>
                            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_hindi') }}</a>
                            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_punjabi') }}</a>
                        </div>
                    </div>
                    <!-- Notifications -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-gray-700 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl overflow-hidden z-50 text-gray-900 border border-gray-100">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="font-bold">{{ __('messages.notifications') }}</h3>
                                <a href="#" class="text-xs text-indigo-600 hover:underline">{{ __('messages.mark_all_read') }}</a>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <div class="p-4 border-b border-gray-50 {{ $notification->unread() ? 'bg-indigo-50' : '' }}">
                                        <p class="text-sm font-semibold">{{ $notification->data['title'] ?? __('messages.notification_update') }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $notification->data['message'] }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-gray-500 text-sm">{{ __('messages.no_notifications') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <span class="text-sm text-gray-400 hidden md:block">{{ __('messages.admin') }}: {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1.5 rounded-lg transition">
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-gray-900 text-white shadow-xl transform transition-transform duration-300 md:translate-x-0 md:static md:z-auto top-16"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 mt-16 md:mt-0">
                <div class="flex items-center gap-3 p-3 bg-gray-800 rounded-xl mb-6">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400">{{ __('messages.administrator') }}</p>
                    </div>
                </div>
                <!-- Sidebar Search -->
                <div class="mb-6" x-data="{ search: '' }">
                    <input type="text" x-model="search" placeholder="{{ __('messages.search_tools') }}" 
                        class="w-full bg-gray-800 border-none rounded-lg py-2 px-3 text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-indigo-500">
                    
                    <nav class="mt-4 space-y-6">
                        @php
                            $modules = [
                                __('messages.learning') => [
                                    ['route' => 'admin.content',     'label' => __('messages.content_review')],
                                    ['route' => 'admin.courses',     'label' => __('messages.course_approval')],
                                    ['route' => 'admin.quizzes',     'label' => __('messages.quiz_approval')],
                                    ['route' => 'admin.scholarships.index', 'label' => __('messages.scholarships_cms')],
                                    ['route' => 'admin.schemes.index',      'label' => __('messages.gov_schemes_cms')],
                                ],
                                __('messages.assessments') => [
                                    ['route' => 'admin.reports',     'label' => __('messages.student_reports')],
                                    ['route' => 'admin.reconciliation.index', 'label' => __('messages.payments_revenue')],
                                ],
                                __('messages.communication') => [
                                    ['route' => 'admin.announcements.index','label' => __('messages.notice_board')],
                                    ['route' => 'admin.complaints.index',   'label' => __('messages.complaints')],
                                    ['route' => 'admin.chatbot-qa',  'label' => __('messages.chatbot_training')],
                                ],
                                __('messages.account') => [
                                    ['route' => 'admin.dashboard',   'label' => __('messages.dashboard')],
                                    ['route' => 'admin.users',       'label' => __('messages.manage_users')],
                                    ['route' => 'admin.students_manager.index', 'label' => __('messages.student_master')],
                                    ['route' => 'admin.teachers_manager.index', 'label' => __('messages.teacher_master')],
                                    ['route' => 'admin.mentor-management.index', 'label' => __('messages.mentor_management')],
                                    ['route' => 'admin.teachers',    'label' => __('messages.teacher_approvals')],
                                ]
                            ];
                        @endphp

                        @foreach($modules as $moduleName => $links)
                            <div x-show="Object.values(@js($links)).some(l => l.label.toLowerCase().includes(search.toLowerCase()))">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 px-4">{{ $moduleName }}</h3>
                                <div class="space-y-1">
                                    @foreach($links as $link)
                                        <a href="{{ route($link['route']) }}"
                                           x-show="'{{ $link['label'] }}'.toLowerCase().includes(search.toLowerCase())"
                                           class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition
                                                  {{ request()->routeIs($link['route']) ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                </div>
            </div>
        </aside>

        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>

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
