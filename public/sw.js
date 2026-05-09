const CACHE_NAME = 'nabha-learning-cache-v3';
const OFFLINE_URL = '/offline.html';

const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/locales/en.json',
    '/locales/hi.json',
    '/locales/pa.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap'
];

// Install: Cache static assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate: Cleanup old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch: Smart Strategy
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);

    // 1. Navigation requests (HTML pages): Network First
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Update cache with fresh version
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                    return response;
                })
                .catch(() => {
                    // Offline: try cache, then offline page
                    return caches.match(event.request).then(cachedResponse => {
                        return cachedResponse || caches.match(OFFLINE_URL);
                    });
                })
        );
        return;
    }

    // 2. Static Assets (CSS, JS, Images, Fonts): Cache First
    if (
        event.request.destination === 'style' ||
        event.request.destination === 'script' ||
        event.request.destination === 'image' ||
        event.request.destination === 'font' ||
        url.pathname.startsWith('/locales/')
    ) {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) return cachedResponse;

                return fetch(event.request).then(response => {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, copy));
                    return response;
                });
            })
        );
        return;
    }

    // 3. Other requests: Network First (default)
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});
