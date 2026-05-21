const CACHE_NAME = 'nabha-lms-enterprise-cache-v2';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/locales/en.json',
    '/locales/hi.json',
    '/locales/pa.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/maskable-icon.png',
    '/favicon.ico',
    '/images/login_bg.png?v=2026',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap'
];

// Maximum dynamic cache items size
const MAX_DYNAMIC_ITEMS = 150;

// Helper to limit cache size
const limitCacheSize = (cacheName, maxItems) => {
    caches.open(cacheName).then(cache => {
        cache.keys().then(keys => {
            if (keys.length > maxItems) {
                cache.delete(keys[0]).then(() => limitCacheSize(cacheName, maxItems));
            }
        });
    });
};

// ── Service Worker Lifecycle ────────────────────────────────────────────────
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[SW] Pre-caching Core Shell Assets...');
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        console.log('[SW] Clearing Stale Cache:', key);
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// ── Dynamic Request Routing ─────────────────────────────────────────────────
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Bypass caching for non-GET requests, hot-reloading (Vite), and translations
    if (request.method !== 'GET' ||
        url.hostname.includes('translate.google') ||
        url.hostname.includes('translate.googleapis') ||
        url.port === '5173' || // Vite dev server
        url.pathname.includes('/vite/')
    ) {
        return;
    }

    // Bypass browser extensions and chrome-extension:// protocols
    if (!url.protocol.startsWith('http')) return;

    // 1. Core Navigation & Dashboard Routes (Network-First with Cache/Offline Fallback)
    if (request.mode === 'navigate' || 
        url.pathname.endsWith('/dashboard') ||
        url.pathname.includes('/student/courses/') ||
        url.pathname.includes('/student/lessons/')
    ) {
        event.respondWith(
            fetch(request)
                .then(response => {
                    if (!response || response.status !== 200) return response;
                    const responseCopy = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseCopy);
                        limitCacheSize(CACHE_NAME, MAX_DYNAMIC_ITEMS);
                    });
                    return response;
                })
                .catch(() => {
                    // Offline fallback: try cache, then serve main offline page
                    return caches.match(request).then(cachedResponse => {
                        return cachedResponse || caches.match(OFFLINE_URL);
                    });
                })
        );
        return;
    }

    // 2. Static Assets: CSS, JS, Images, SVGs, Fonts (Cache-First)
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font' ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.woff2') ||
        url.pathname.includes('/images/')
    ) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                if (cachedResponse) return cachedResponse;

                return fetch(request).then(response => {
                    if (!response || response.status !== 200) return response;
                    const responseCopy = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseCopy);
                    });
                    return response;
                }).catch(() => {
                    // Static fallback for offline missing images
                    if (request.destination === 'image') {
                        return new Response(
                            `<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><rect width="20" height="20" x="2" y="2" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>`,
                            { headers: { 'Content-Type': 'image/svg+xml' } }
                        );
                    }
                });
            })
        );
        return;
    }

    // 3. API Requests, Catalog Lists, Notices, Schemes (Stale-While-Revalidate)
    if (url.pathname.includes('/api/') || 
        url.pathname.includes('/locales/') ||
        url.pathname.includes('/student/scholarships') ||
        url.pathname.includes('/student/schemes') ||
        url.pathname.includes('/student/quizzes')
    ) {
        event.respondWith(
            caches.match(request).then(cachedResponse => {
                const fetchPromise = fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseCopy = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(request, responseCopy);
                        });
                    }
                    return networkResponse;
                }).catch(() => null);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // 4. Default Strategy (Network First)
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});

// ── Background Synchronization ──────────────────────────────────────────────
self.addEventListener('sync', event => {
    console.log('[SW] Background sync triggered. Tag:', event.tag);
    if (event.tag === 'sync-offline-actions') {
        event.waitUntil(
            self.clients.matchAll().then(clients => {
                clients.forEach(client => {
                    client.postMessage({ type: 'SYNC_ACTIONS' });
                });
            })
        );
    }
});

// ── Push Notifications ──────────────────────────────────────────────────────
self.addEventListener('push', event => {
    let payload = { title: 'Nabha Learning', body: 'New academic update available!' };

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    const options = {
        body: payload.body,
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-192x192.png',
        vibrate: [100, 50, 100],
        data: {
            url: payload.url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(payload.title, options)
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    event.waitUntil(
        self.clients.matchAll({ type: 'window' }).then(clientList => {
            const urlToOpen = event.notification.data.url;
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(urlToOpen);
            }
        })
    );
});
