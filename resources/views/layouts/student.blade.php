@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Top Navbar -->
    <nav class="bg-primary-700 text-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="md:hidden p-2 rounded-md hover:bg-primary-600 text-sm font-medium">
                        {{ __('messages.menu') }}
                    </button>
                    <div>
                        <span class="font-bold text-lg hidden sm:block">{{ __('messages.platform_name') }}</span>
                        <span class="text-xs text-primary-200 hidden sm:block">{{ __('messages.student_portal') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Language Switcher -->
                    <div x-data="{ langOpen: false }" class="relative">
                        <button @click="langOpen = !langOpen" class="flex items-center gap-1 text-sm font-medium hover:text-primary-200">
                            {{ strtoupper(app()->getLocale()) }} 
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-cloak class="absolute right-0 mt-2 w-24 bg-white text-gray-800 rounded-lg shadow-xl py-1 z-50 text-sm">
                            <a href="{{ route('lang.switch', 'en') }}" data-lang="en" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_english') }}</a>
                            <a href="{{ route('lang.switch', 'hi') }}" data-lang="hi" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_hindi') }}</a>
                            <a href="{{ route('lang.switch', 'pa') }}" data-lang="pa" class="block px-4 py-2 hover:bg-gray-100">{{ __('messages.lang_punjabi') }}</a>
                        </div>
                    </div>

                    <span class="text-sm text-primary-200 hidden md:block">{{ auth()->user()->name }} | {{ auth()->user()->class_level }}</span>
                    @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                    <!-- Notifications -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="relative p-2 rounded-lg hover:bg-primary-600 transition">
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
                                <a href="#" class="text-xs text-primary-600 hover:underline">{{ __('messages.mark_all_read') }}</a>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @forelse(auth()->user()->notifications->take(10) as $notification)
                                    <div class="p-4 border-b border-gray-50 {{ $notification->unread() ? 'bg-primary-50' : '' }}">
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

                    <a href="{{ route('student.cart') }}"
                       class="relative px-3 py-1.5 rounded-lg hover:bg-primary-600 transition text-sm font-medium" title="{{ __('messages.cart') }}">
                        {{ __('messages.cart') }}
                        @if($cartCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-amber-400 text-gray-900 text-[10px] font-bold rounded-full flex items-center justify-center px-0.5">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm bg-primary-600 hover:bg-primary-500 px-3 py-1.5 rounded-lg transition">
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-xl transform transition-transform duration-300 md:translate-x-0 md:static md:shadow-none md:z-auto top-16"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="p-4 mt-16 md:mt-0">
                <div class="flex items-center gap-3 p-3 bg-primary-50 rounded-xl mb-6">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-10 h-10 rounded-full" alt="Avatar">
                    <div>
                        <p class="font-semibold text-sm text-gray-800">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->class_level }}</p>
                    </div>
                </div>
                <nav class="space-y-6">
                    @php
                        $modules = [
                            __('messages.learning') => [
                                ['route' => 'student.courses',    'label' => __('messages.courses')],
                                ['route' => 'student.scholarships', 'label' => __('messages.scholarships')],
                                ['route' => 'student.schemes',    'label' => __('messages.schemes')],
                                ['route' => 'student.careers',    'label' => __('messages.career')],
                            ],
                            __('messages.assessments') => [
                                ['route' => 'student.quizzes',    'label' => __('messages.quizzes')],
                            ],
                            __('messages.communication') => [
                                ['route' => 'student.mentors',    'label' => __('messages.mentors')],
                                ['route' => 'student.chatbot',    'label' => __('messages.ai_chatbot')],
                                ['route' => 'student.complaints', 'label' => __('messages.complaints')],
                            ],
                            __('messages.account') => [
                                ['route' => 'student.dashboard',  'label' => __('messages.dashboard')],
                                ['route' => 'student.cart',       'label' => __('messages.cart')],
                                ['route' => 'student.profile',    'label' => __('messages.my_profile')],
                            ]
                        ];
                    @endphp

                    @foreach($modules as $moduleName => $links)
                        <div>
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-4">{{ $moduleName }}</h3>
                            <div class="space-y-1">
                                @foreach($links as $link)
                                    <a href="{{ Route::has($link['route']) ? route($link['route']) : '#' }}"
                                       class="flex items-center px-4 py-2 rounded-lg text-sm font-medium transition
                                              {{ request()->routeIs($link['route']) ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                                        <span class="flex-1">{{ $link['label'] }}</span>
                                        @if($link['route'] === 'student.cart' && $cartCount > 0)
                                            <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                                {{ $cartCount }}
                                            </span>
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
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 md:hidden" x-cloak></div>

        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8 mb-16 md:mb-0">
            @yield('student-content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-gray-200 z-40 flex items-center justify-around pb-safe">
        <a href="{{ route('student.dashboard') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.dashboard') ? 'text-primary-600' : 'text-gray-500' }}">
            <span class="text-xl leading-none mb-1">🏠</span>
            <span class="text-[10px] font-medium">{{ __('messages.home') }}</span>
        </a>
        <a href="{{ route('student.courses') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.courses') ? 'text-primary-600' : 'text-gray-500' }}">
            <span class="text-xl leading-none mb-1">📚</span>
            <span class="text-[10px] font-medium">{{ __('messages.courses') }}</span>
        </a>
        <a href="{{ route('student.scholarships') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.scholarships') ? 'text-primary-600' : 'text-gray-500' }}">
            <span class="text-xl leading-none mb-1">🎓</span>
            <span class="text-[10px] font-medium">{{ __('messages.aid') }}</span>
        </a>
        <a href="{{ route('student.profile') }}" class="flex flex-col items-center p-3 {{ request()->routeIs('student.profile') ? 'text-primary-600' : 'text-gray-500' }}">
            <span class="text-xl leading-none mb-1">👤</span>
            <span class="text-[10px] font-medium">{{ __('messages.my_profile') }}</span>
        </a>
    </nav>
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
