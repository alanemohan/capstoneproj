<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.platform_name'))</title>

    <!-- PWA Meta -->
    <meta name="theme-color" content="#4F46E5">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ __('messages.platform_short_name') }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])



    <style>
        [x-cloak] { display: none !important; }

        /* ── Chat / Markdown ── */
        .markdown-body h2 { font-size: 1.25rem; font-weight: 700; margin: .75rem 0 .5rem; }
        .markdown-body p  { margin-bottom: .5rem; line-height: 1.6; }
        .markdown-body ul { list-style: disc; padding-left: 1.25rem; margin-bottom: .5rem; }
        .markdown-body li { margin-bottom: .25rem; }
        .chat-bubble { white-space: pre-wrap; word-break: break-word; }

        /* ── Offline ── */
        .offline-banner { display: none; }
        body.offline .offline-banner { display: flex; }

        /* ── Smooth page transitions ── */
        main { animation: fadeSlideUp .18s ease both; }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Star rating ── */
        .star-filled  { color: #f59e0b; }
        .star-half    { color: #f59e0b; }
        .star-empty   { color: #d1d5db; }

        /* ── Loading btn ── */
        .btn-loading { pointer-events: none; opacity: .75; }

        /* Make interactive elements explicitly show pointer to improve UX */
        button, a { cursor: pointer; }

        /* ── Form dropdown contrast fix (all portals) ── */
        select {
            color: #111827;
            background-color: #ffffff;
        }

        select option,
        select optgroup {
            color: #111827;
            background-color: #ffffff;
        }

        select:disabled,
        select option:disabled {
            color: #9ca3af;
        }
    </style>

    <!-- Alpine.js CDN (Backup/Primary for reliability) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .markdown-body h2 { font-size: 1.25rem; font-weight: 700; margin: .75rem 0 .5rem; }
        .markdown-body p  { margin-bottom: .5rem; line-height: 1.6; }
        /* ... other styles ... */
    </style>
    @stack('styles')
    
    <script>
    /* ─────────────────────────────────────────────────────────────────────────────
       Global Helper Functions
    ───────────────────────────────────────────────────────────────────────────── */
    document.addEventListener('alpine:init', () => {
        Alpine.data('toastSystem', () => ({
            toasts: [],
            _id: 0,
            init() {
                @if(session('success')) this._push('success', @json(session('success'))); @endif
                @if(session('error')) this._push('error', @json(session('error'))); @endif
                @if(session('info')) this._push('info', @json(session('info'))); @endif
                @if($errors->any()) this._push('error', @json($errors->first())); @endif
                window.toast = (type, message) => this._push(type, message);
                window.addEventListener('toast', e => this._push(e.detail.type, e.detail.message));
            },
            _push(type, message) {
                const ttl = type === 'error' ? 6000 : 4500;
                const id = ++this._id;
                this.toasts.push({ id, type, message, visible: true, progress: 100, ttl });
                setTimeout(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.progress = 0;
                }, 50);
                setTimeout(() => this.dismiss({ id }), ttl);
            },
            dismiss(toast) {
                const t = this.toasts.find(t => t.id === toast.id);
                if (t) t.visible = false;
                setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== toast.id); }, 300);
            }
        }));
    });

    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const isHide = input.type === 'password';
        input.type = isHide ? 'text' : 'password';
        const showIcon = document.getElementById(inputId + '-eye-show');
        const hideIcon = document.getElementById(inputId + '-eye-hide');
        if (showIcon) showIcon.classList.toggle('hidden', isHide);
        if (hideIcon) hideIcon.classList.toggle('hidden', !isHide);
    }
    </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

<!-- ── Global Toast Notifications ── -->
<div
    x-data="toastSystem"
    class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="t.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-6 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-6 scale-95"
            class="pointer-events-auto relative overflow-hidden flex items-start gap-3 px-4 py-3.5 rounded-xl shadow-lg border text-sm font-medium"
            :class="{
                'bg-emerald-50 border-emerald-200 text-emerald-800': t.type === 'success',
                'bg-red-50    border-red-200    text-red-800'   : t.type === 'error',
                'bg-blue-50   border-blue-200   text-blue-800'  : t.type === 'info',
            }"
            role="alert"
        >
            <!-- Progress bar -->
            <div class="absolute bottom-0 left-0 h-0.5 transition-all ease-linear"
                 :class="{
                     'bg-emerald-400': t.type === 'success',
                     'bg-red-400'    : t.type === 'error',
                     'bg-blue-400'   : t.type === 'info',
                 }"
                 :style="`width:${t.progress}%; transition-duration:${t.ttl}ms`"
            ></div>

            <p class="flex-1 leading-snug" x-text="t.message"></p>
            <button @click="dismiss(t)" class="flex-shrink-0 opacity-40 hover:opacity-80 transition ml-1 text-lg leading-none font-bold">&times;</button>
        </div>
    </template>
</div>

@yield('content')

<!-- ── PWA Install Banner ── -->
<div id="pwa-install-banner" class="hidden fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-primary-700 text-white rounded-xl shadow-2xl p-4 z-50">
    <div class="flex items-start gap-3">
        <div class="flex-1">
            <p class="font-semibold text-sm">{{ __('messages.install_pwa') }}</p>
            <p class="text-xs text-primary-200 mt-0.5">{{ __('messages.pwa_benefits') }}</p>
        </div>
        <button onclick="document.getElementById('pwa-install-banner').classList.add('hidden')"
                class="text-primary-300 hover:text-white text-xl leading-none">&times;</button>
    </div>
    <div class="flex gap-2 mt-3">
        <button id="pwa-install-btn" class="flex-1 bg-white text-primary-700 text-sm font-semibold py-1.5 rounded-lg hover:bg-primary-50 transition">{{ __('messages.install') }}</button>
        <button onclick="document.getElementById('pwa-install-banner').classList.add('hidden')"
                class="flex-1 border border-primary-400 text-sm py-1.5 rounded-lg hover:bg-primary-600 transition">{{ __('messages.not_now') }}</button>
    </div>
</div>

@stack('scripts')
<script>
/* ─────────────────────────────────────────────────────────────────────────────
   Global Form Loading State
───────────────────────────────────────────────────────────────────────────── */
document.addEventListener('submit', function (e) {
    if (e.target.hasAttribute('data-no-loading')) return;
    const btn = e.target.querySelector('[type="submit"]');
    if (!btn) return;

    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('btn-loading');
    btn.innerHTML =
        `<svg class="w-4 h-4 animate-spin inline-block mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>{{ __('messages.saving') }}`;

    setTimeout(() => {
        btn.disabled = false;
        btn.classList.remove('btn-loading');
        btn.innerHTML = orig;
    }, 5000);
});

/* ─────────────────────────────────────────────────────────────────────────────
   PWA & Offline
───────────────────────────────────────────────────────────────────────────── */
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => {
                reg.onupdatefound = () => {
                    const installingWorker = reg.installing;
                    installingWorker.onstatechange = () => {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            window.toast('info', '{{ __('messages.new_version') }}');
                        }
                    };
                };
            })
            .catch(err => console.log('SW failed:', err));
    });
}
window.addEventListener('online',  () => document.body.classList.remove('offline'));
window.addEventListener('offline', () => document.body.classList.add('offline'));
if (!navigator.onLine) document.body.classList.add('offline');

let deferredPrompt;
window.addEventListener('beforeinstallprompt', e => {
    e.preventDefault();
    deferredPrompt = e;
    document.getElementById('pwa-install-banner')?.classList.remove('hidden');
});
document.getElementById('pwa-install-btn')?.addEventListener('click', async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        document.getElementById('pwa-install-banner')?.classList.add('hidden');
    }
});
</script>
</body>
</html>
