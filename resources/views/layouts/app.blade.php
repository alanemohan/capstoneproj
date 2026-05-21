<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
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

    <!-- Google Fonts: Inter + Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ══════════════════════════════════════════════════════════════
           DESIGN SYSTEM FOUNDATIONS — Three Portal Aesthetics
        ══════════════════════════════════════════════════════════════ */

        :root {
            --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            --font-display: 'Space Grotesk', 'Inter', sans-serif;
        }

        body { font-family: var(--font-sans); }

        [x-cloak] { display: none !important; }

        /* ── Student Portal: Glassmorphic Light by default, Dark when active ── */
        .student-portal {
            --portal-bg: #f8fafc;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-hover-border: rgba(139, 92, 246, 0.35);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent: #7c3aed;
            --accent-glow: rgba(124, 58, 237, 0.05);
            --accent-secondary: #0891b2;
        }

        .dark .student-portal {
            --portal-bg: #0b0f19;
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.06);
            --card-hover-border: rgba(139, 92, 246, 0.3);
            --text-primary: #f1f0ff;
            --text-secondary: rgba(255, 255, 255, 0.6);
            --text-muted: rgba(255, 255, 255, 0.35);
            --accent: #8b5cf6;
            --accent-glow: rgba(139, 92, 246, 0.2);
            --accent-secondary: #06b6d4;
        }

        /* ── Teacher Portal: Productivity Clean ── */
        .teacher-portal {
            --portal-bg: #f9fafb;
            --card-bg: #ffffff;
            --card-border: #e5e7eb;
            --card-hover-border: rgba(16, 185, 129, 0.4);
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --accent: #10b981;
            --accent-hover: #059669;
        }

        .dark .teacher-portal {
            --portal-bg: #0b0f19;
            --card-bg: #111827;
            --card-border: #1f2937;
            --card-hover-border: rgba(52, 211, 153, 0.4);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --text-muted: #4b5563;
            --accent: #34d399;
            --accent-hover: #059669;
        }

        /* ── Admin Portal: Enterprise Navy ── */
        .admin-portal {
            --portal-bg: #f8fafc;
            --sidebar-bg: #0f172a;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #f97316;
            --card-bg: #ffffff;
            --card-border: #e2e8f0;
            --card-hover-border: rgba(249, 115, 22, 0.4);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --accent: #f97316;
            --accent-hover: #ea580c;
        }

        .dark .admin-portal {
            --portal-bg: #0b0f19;
            --sidebar-bg: #0f172a;
            --sidebar-text: #cbd5e1;
            --sidebar-active: #f97316;
            --card-bg: #111827;
            --card-border: #1f2937;
            --card-hover-border: rgba(251, 146, 60, 0.4);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --text-muted: #4b5563;
            --accent: #fb923c;
            --accent-hover: #ea580c;
        }

        /* ── Glassmorphism Utility Classes ── */
        .glass {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
        }
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: var(--card-hover-border);
            box-shadow: var(--accent-glow) 0px 10px 30px;
        }
        .glass-strong {
            background: var(--card-bg);
            backdrop-filter: blur(32px);
            -webkit-backdrop-filter: blur(32px);
            border: 1px solid var(--card-border);
        }

        /* ── Glow Effects ── */
        .glow-violet { box-shadow: 0 0 30px rgba(139, 92, 246, 0.08); }
        .glow-cyan   { box-shadow: 0 0 25px rgba(6, 182, 212, 0.06); }
        .glow-emerald { box-shadow: 0 0 20px rgba(16, 185, 129, 0.05); }
        .glow-coral  { box-shadow: 0 0 20px rgba(249, 115, 22, 0.05); }
        
        .dark .glow-violet { box-shadow: 0 0 30px rgba(139, 92, 246, 0.15); }
        .dark .glow-cyan   { box-shadow: 0 0 25px rgba(6, 182, 212, 0.12); }
        .dark .glow-emerald { box-shadow: 0 0 20px rgba(16, 185, 129, 0.1); }
        .dark .glow-coral  { box-shadow: 0 0 20px rgba(249, 115, 22, 0.1); }

        /* ── Animation Keyframes ── */
        main { animation: fadeSlideUp .25s ease both; }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(12px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.15); }
            50%      { box-shadow: 0 0 40px rgba(139, 92, 246, 0.25); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .animate-fade-in    { animation: fadeIn .3s ease both; }
        .animate-slide-in   { animation: slideInRight .3s ease both; }
        .animate-scale-in   { animation: scaleIn .25s ease both; }
        .animate-pulse-glow { animation: pulseGlow 3s ease-in-out infinite; }
        .animate-count-up   { animation: countUp .4s ease both; }

        /* ── Skeleton Loader ── */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }
        .skeleton-dark {
            background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.08) 50%, rgba(255,255,255,.04) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }

        /* ── Stagger animation for lists ── */
        .stagger-1 { animation-delay: 0.05s; }
        .stagger-2 { animation-delay: 0.10s; }
        .stagger-3 { animation-delay: 0.15s; }
        .stagger-4 { animation-delay: 0.20s; }
        .stagger-5 { animation-delay: 0.25s; }
        .stagger-6 { animation-delay: 0.30s; }
        .stagger-7 { animation-delay: 0.35s; }
        .stagger-8 { animation-delay: 0.40s; }

        /* ── Chat / Markdown ── */
        .markdown-body h2 { font-size: 1.25rem; font-weight: 700; margin: .75rem 0 .5rem; }
        .markdown-body p  { margin-bottom: .5rem; line-height: 1.6; }
        .markdown-body ul { list-style: disc; padding-left: 1.25rem; margin-bottom: .5rem; }
        .markdown-body li { margin-bottom: .25rem; }
        .chat-bubble { white-space: pre-wrap; word-break: break-word; }

        /* ── Offline ── */
        .offline-banner { display: none; }
        body.offline .offline-banner { display: flex; }

        /* ── Star rating ── */
        .star-filled  { color: #f59e0b; }
        .star-half    { color: #f59e0b; }
        .star-empty   { color: #d1d5db; }

        /* ── Loading btn ── */
        .btn-loading { pointer-events: none; opacity: .75; }

        button, a { cursor: pointer; }

        /* ── Form dropdown contrast fix ── */
        select { color: #111827; background-color: #ffffff; }
        select option, select optgroup { color: #111827; background-color: #ffffff; }
        select:disabled, select option:disabled { color: #9ca3af; }

        /* ── Student portal dropdown overrides ── */
        .student-portal select { color: #f1f0ff; background-color: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.12); }
        .student-portal select option { color: #111827; background-color: #ffffff; }

        /* ── Scrollbar styling ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .student-portal ::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); }
        .student-portal ::-webkit-scrollbar-thumb:hover { background: rgba(139,92,246,0.5); }

        /* ── Google Translate Branding Hider ── */
        iframe.goog-te-banner-frame {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
        }
        body {
            top: 0px !important;
        }
        .skiptranslate, #google_translate_element {
            display: none !important;
            visibility: hidden !important;
        }
        .goog-te-combo {
            display: none !important;
        }
        .goog-te-menu-value {
            display: none !important;
        }
        .goog-tooltip, .goog-tooltip:hover {
            display: none !important;
            visibility: hidden !important;
        }
        .goog-text-highlight {
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* ── Premium Dark Mode Style Overrides ── */
        .dark body, 
        .dark .admin-portal,
        .dark .teacher-portal,
        .dark .student-portal {
            background-color: #0b0f19 !important;
            color: #f3f4f6 !important;
        }

        .dark .bg-white,
        .dark .bg-slate-50,
        .dark .bg-slate-100,
        .dark .bg-gray-50,
        .dark .bg-gray-100 {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
        }

        .dark h1, .dark h2, .dark h3, .dark h4, .dark h5, .dark h6,
        .dark .text-slate-900, .dark .text-slate-800, .dark .text-slate-700,
        .dark .text-gray-900, .dark .text-gray-800, .dark .text-gray-700 {
            color: #f3f4f6 !important;
        }

        .dark p, .dark span:not(.bg-amber-500):not(.bg-orange-500), 
        .dark .text-slate-500, .dark .text-slate-600,
        .dark .text-gray-500, .dark .text-gray-600 {
            color: #9ca3af !important;
        }

        .dark .border-slate-200, 
        .dark .border-slate-200\/80,
        .dark .border-slate-100, 
        .dark .border-slate-150,
        .dark .border-gray-200 {
            border-color: #1f2937 !important;
        }

        /* Nav and Cards */
        .dark nav,
        .dark aside,
        .dark .bg-white.rounded-xl,
        .dark .bg-white.rounded-2xl,
        .dark .bg-white.rounded-lg,
        .dark .bg-white.shadow-sm,
        .dark table,
        .dark .bg-white.p-6,
        .dark .bg-white.p-5 {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.3) !important;
        }

        .dark tr:hover {
            background-color: rgba(31, 41, 55, 0.4) !important;
        }

        .dark .hover\:bg-slate-50:hover, 
        .dark .hover\:bg-slate-50\/50:hover,
        .dark .hover\:bg-gray-50:hover {
            background-color: #1f2937 !important;
        }

        /* Forms & Inputs */
        .dark input, 
        .dark select, 
        .dark textarea {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #f3f4f6 !important;
        }

        .dark input::placeholder, 
        .dark textarea::placeholder {
            color: #6b7280 !important;
        }

        .dark input:focus, 
        .dark select:focus, 
        .dark textarea:focus {
            border-color: #8b5cf6 !important;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2) !important;
        }

        /* Modal Dialogs */
        .dark .bg-white.shadow-lg {
            background-color: #111827 !important;
            border-color: #1f2937 !important;
        }

        /* Navigation elements custom */
        .dark .text-slate-400 { color: #9ca3af !important; }
        .dark .text-slate-600 { color: #d1d5db !important; }
        .dark .hover\:text-slate-200:hover { color: #ffffff !important; }

        /* Smooth transitions */
        body, nav, aside, main, div, input, select, textarea, button {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
        }

        /* ── Light Mode Contrast Overrides for Student Portal ── */
        html:not(.dark) .student-portal [class*="text-white"] {
            color: #0f172a !important;
        }

        html:not(.dark) .student-portal [class*="text-white/4"],
        html:not(.dark) .student-portal [class*="text-white/3"],
        html:not(.dark) .student-portal [class*="text-white/2"],
        html:not(.dark) .student-portal [class*="text-white/1"],
        html:not(.dark) .student-portal [class*="text-white/0"],
        html:not(.dark) .student-portal [class*="text-white\/4"],
        html:not(.dark) .student-portal [class*="text-white\/3"],
        html:not(.dark) .student-portal [class*="text-white\/2"],
        html:not(.dark) .student-portal [class*="text-white\/1"],
        html:not(.dark) .student-portal [class*="text-white\/0"] {
            color: #475569 !important;
        }

        html:not(.dark) .student-portal [class*="text-violet"] {
            color: #6d28d9 !important;
        }

        html:not(.dark) .student-portal [class*="text-slate-2"],
        html:not(.dark) .student-portal [class*="text-slate-3"],
        html:not(.dark) .student-portal [class*="text-slate-4"],
        html:not(.dark) .student-portal [class*="text-gray-2"],
        html:not(.dark) .student-portal [class*="text-gray-3"],
        html:not(.dark) .student-portal [class*="text-gray-4"],
        html:not(.dark) .student-portal [class*="text-zinc-2"],
        html:not(.dark) .student-portal [class*="text-zinc-3"],
        html:not(.dark) .student-portal [class*="text-zinc-4"] {
            color: #475569 !important;
        }

        html:not(.dark) .student-portal [class*="bg-white/"],
        html:not(.dark) .student-portal [class*="bg-white\/"] {
            background-color: #f1f5f9 !important;
        }

        html:not(.dark) .student-portal [class*="border-white/"],
        html:not(.dark) .student-portal [class*="border-white\/"] {
            border-color: #cbd5e1 !important;
        }

        html:not(.dark) .student-portal .glass-card,
        html:not(.dark) .student-portal .glass-strong,
        html:not(.dark) .student-portal .glass {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02), 0 1px 2px rgba(15, 23, 42, 0.03) !important;
        }

        html:not(.dark) .student-portal select,
        html:not(.dark) .student-portal input,
        html:not(.dark) .student-portal textarea {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html:not(.dark) .student-portal [class*="placeholder-white"]::placeholder {
            color: #94a3b8 !important;
        }

        html:not(.dark) .student-portal input::placeholder,
        html:not(.dark) .student-portal textarea::placeholder {
            color: #94a3b8 !important;
        }

        /* ── Light Mode Overrides for Teacher and Admin Portals ── */
        html:not(.dark) body,
        html:not(.dark) .teacher-portal,
        html:not(.dark) .admin-portal {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }

        html:not(.dark) .teacher-portal > nav,
        html:not(.dark) .admin-portal > nav {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) .teacher-portal > nav *,
        html:not(.dark) .admin-portal > nav * {
            color: #1e293b !important;
        }

        html:not(.dark) .teacher-portal > nav svg,
        html:not(.dark) .admin-portal > nav svg {
            color: #475569 !important;
        }

        html:not(.dark) .teacher-portal aside {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }

        html:not(.dark) .teacher-portal aside a {
            color: #475569 !important;
        }

        html:not(.dark) .teacher-portal aside a:hover {
            color: #0f172a !important;
            background-color: #f1f5f9 !important;
        }

        html:not(.dark) .teacher-portal aside h3 {
            color: #94a3b8 !important;
        }

        html:not(.dark) .teacher-portal aside p {
            color: #1e293b !important;
        }

        html:not(.dark) .teacher-portal aside span {
            color: #1e293b !important;
        }

        html:not(.dark) .teacher-portal aside input {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* Admin Portal Sidebar (Maintains dark background bg-[#0f172a], but text is white/light slate for maximum contrast) */
        html:not(.dark) .admin-portal aside {
            background-color: #0f172a !important;
            border-color: rgba(255,255,255,0.06) !important;
        }

        html:not(.dark) .admin-portal aside a {
            color: #cbd5e1 !important;
        }

        html:not(.dark) .admin-portal aside a:hover {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.04) !important;
        }

        html:not(.dark) .admin-portal aside h3 {
            color: #64748b !important;
        }

        html:not(.dark) .admin-portal aside p,
        html:not(.dark) .admin-portal aside span:not(.bg-amber-500) {
            color: #e2e8f0 !important;
        }

        html:not(.dark) .admin-portal aside input {
            background-color: rgba(255,255,255,0.04) !important;
            border-color: rgba(255,255,255,0.06) !important;
            color: #ffffff !important;
        }
    </style>

    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

    // Instant Premium Navigation Loader & Hover Prefetcher
    document.addEventListener('DOMContentLoaded', () => {
        document.body.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && 
                link.href && 
                !link.href.startsWith('#') && 
                !link.href.startsWith('javascript:') && 
                !link.target && 
                link.hostname === window.location.hostname &&
                e.button === 0 && 
                !e.metaKey && !e.ctrlKey && !e.shiftKey && !e.altKey
            ) {
                showTopLoader();
            }
        });

        // Hover-based Link Prefetcher for Instant-feeling navigation
        const preloadedUrls = new Set();
        const prefetchLink = (url) => {
            if (preloadedUrls.has(url)) return;
            preloadedUrls.add(url);
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'document';
            document.head.appendChild(link);
        };
        
        const handleHover = (e) => {
            const a = e.target.closest('a');
            if (a && a.href && a.hostname === window.location.hostname && 
                !a.href.startsWith('#') && 
                !a.href.startsWith('javascript:') && 
                !a.target
            ) {
                prefetchLink(a.href);
            }
        };

        document.body.addEventListener('mouseover', handleHover, { passive: true });
        document.body.addEventListener('touchstart', handleHover, { passive: true });
    });

    // Lazy Chart Initialization Helper using IntersectionObserver
    window.lazyRenderChart = function(canvasId, initFn) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    initFn();
                    observer.disconnect();
                }
            });
        }, { rootMargin: '100px' });
        observer.observe(canvas);
    };

    window.addEventListener('beforeunload', showTopLoader);

    function showTopLoader() {
        if (document.getElementById('global-top-loader')) return;
        const bar = document.createElement('div');
        bar.id = 'global-top-loader';
        bar.style.position = 'fixed';
        bar.style.top = '0';
        bar.style.left = '0';
        bar.style.height = '3px';
        bar.style.width = '0%';
        bar.style.backgroundColor = '#8b5cf6';
        bar.style.boxShadow = '0 0 10px #8b5cf6, 0 0 5px #8b5cf6';
        bar.style.zIndex = '99999';
        bar.style.transition = 'width 0.4s cubic-bezier(0.08, 0.82, 0.17, 1)';
        document.body.appendChild(bar);
        
        requestAnimationFrame(() => {
            bar.style.width = '80%';
        });
    }
    </script>

    <!-- Google Translate Dynamic Multilingual Engine -->
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi,pa',
            layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
            autoDisplay: false
        }, 'google_translate_element');
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Auto-inject translation element if missing
        if (!document.getElementById('google_translate_element')) {
            const div = document.createElement('div');
            div.id = 'google_translate_element';
            div.style.display = 'none';
            document.body.appendChild(div);
        }

        // Initialize dynamic translate state from localStorage or browser language
        let lang = localStorage.getItem('applocale') || document.documentElement.lang || 'en';
        if (lang.startsWith('en')) lang = 'en';
        if (lang.startsWith('hi')) lang = 'hi';
        if (lang.startsWith('pa')) lang = 'pa';

        localStorage.setItem('applocale', lang);

        function applyDynamicTranslate() {
            const combo = document.querySelector('.goog-te-combo');
            if (combo) {
                if (combo.value !== lang) {
                    combo.value = lang;
                    combo.dispatchEvent(new Event('change'));
                }
            } else {
                setTimeout(applyDynamicTranslate, 100);
            }
        }
        setTimeout(applyDynamicTranslate, 150);
    });

    window.changeLanguage = function(langCode) {
        localStorage.setItem('applocale', langCode);
        const combo = document.querySelector('.goog-te-combo');
        if (combo) {
            combo.value = langCode;
            combo.dispatchEvent(new Event('change'));
        }
    };

    // Theme Toggle Handler
    document.addEventListener('DOMContentLoaded', () => {
        const applyIcons = () => {
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon?.classList.remove('hidden');
                themeToggleDarkIcon?.classList.add('hidden');
            } else {
                themeToggleLightIcon?.classList.add('hidden');
                themeToggleDarkIcon?.classList.remove('hidden');
            }
        };

        applyIcons();

        // Listen for clicks on the toggle button globally
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('#theme-toggle');
            if (!btn) return;

            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            // toggle icons inside button
            themeToggleDarkIcon?.classList.toggle('hidden');
            themeToggleLightIcon?.classList.toggle('hidden');

            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
            
            // Dispatch event for components to react to dark mode updates
            window.dispatchEvent(new Event('theme-changed'));
        });
    });
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>
</head>
<body class="bg-white min-h-screen font-sans antialiased">

<!-- ── Offline Connection Badge ── -->
<div class="offline-banner fixed bottom-4 left-4 z-[9999] bg-gray-900 border border-gray-800 text-white text-xs font-semibold px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce">
    <span class="relative flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
    </span>
    <span>{{ __('messages.offline_message') }}</span>
</div>

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
                console.log('[SW] Service Worker registered under scope:', reg.scope);

                // Register background sync on online reconnection
                window.addEventListener('online', () => {
                    if (reg.sync) {
                        reg.sync.register('sync-offline-actions')
                            .then(() => console.log('[Sync] Offline background sync tag registered.'))
                            .catch(err => console.error('[Sync] Background sync registration failed:', err));
                    }
                });

                reg.onupdatefound = () => {
                    const installingWorker = reg.installing;
                    installingWorker.onstatechange = () => {
                        if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            window.toast('info', '{{ __('messages.new_version') }}');
                        }
                    };
                };
            })
            .catch(err => console.error('[SW] Registration failed:', err));
    });
}

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
<script src="/js/pwa-offline-store.js" defer></script>
</body>
</html>
