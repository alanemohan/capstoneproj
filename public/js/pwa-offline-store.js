/**
 * Nabha Learning LMS — PWA Offline Storage & Sync Coordinator
 * Enterprise-grade client-side IndexedDB manager for robust offline-first functionality.
 */
class PWAOfflineStore {
    constructor() {
        this.dbName = 'NabhaLMSOfflineDB';
        this.dbVersion = 1;
        this.db = null;
        this.isSyncing = false;
        
        this.initDatabase().then(() => {
            this.registerNetworkStatusListeners();
            this.interceptFormSubmissions();
            this.loadCachedDashboardData();
            this.cacheActiveDashboardState();
        });
    }

    // ── Initialize IndexedDB ────────────────────────────────────────────────
    initDatabase() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);

            request.onerror = (e) => {
                console.error('[PWA DB] Database initialization failed:', e);
                reject(e);
            };

            request.onsuccess = (e) => {
                this.db = e.target.result;
                console.log('[PWA DB] Database initialized successfully.');
                resolve(this.db);
            };

            request.onupgradeneeded = (e) => {
                const db = e.target.result;

                // Store for cached HTML modules/JSON statistics
                if (!db.objectStoreNames.contains('dashboard-cache')) {
                    db.createObjectStore('dashboard-cache', { keyPath: 'key' });
                }

                // Store for deferred API/form operations made while offline
                if (!db.objectStoreNames.contains('offline-sync-queue')) {
                    db.createObjectStore('offline-sync-queue', { keyPath: 'id', autoIncrement: true });
                }

                console.log('[PWA DB] Schema upgraded successfully.');
            };
        });
    }

    // ── Queue Offline Action ────────────────────────────────────────────────
    queueAction(url, method, headers, body, type = 'form') {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                console.error('[PWA DB] Database not ready.');
                return reject();
            }

            const transaction = this.db.transaction(['offline-sync-queue'], 'readwrite');
            const store = transaction.objectStore('offline-sync-queue');

            const record = {
                url,
                method,
                headers: Array.from(new Headers(headers).entries()),
                body: body instanceof FormData ? this.serializeFormData(body) : body,
                isFormData: body instanceof FormData,
                type,
                timestamp: Date.now()
            };

            const request = store.add(record);
            request.onsuccess = () => {
                console.log('[PWA DB] Successfully queued offline action:', record);
                this.showToast('info', 'You are offline. Your request has been securely saved and will sync once connection is restored.');
                resolve();
            };
            request.onerror = (e) => reject(e);
        });
    }

    // Helper to serialize FormData into plain object
    serializeFormData(formData) {
        const obj = {};
        formData.forEach((value, key) => {
            obj[key] = value;
        });
        return obj;
    }

    // Helper to reconstruct FormData
    deserializeFormData(obj) {
        const formData = new FormData();
        Object.keys(obj).forEach(key => {
            formData.append(key, obj[key]);
        });
        return formData;
    }

    // ── Background Sync Engine ──────────────────────────────────────────────
    async processSyncQueue() {
        if (this.isSyncing || !navigator.onLine || !this.db) return;
        this.isSyncing = true;

        const transaction = this.db.transaction(['offline-sync-queue'], 'readwrite');
        const store = transaction.objectStore('offline-sync-queue');
        const request = store.getAll();

        request.onsuccess = async (e) => {
            const records = e.target.result;
            if (records.length === 0) {
                this.isSyncing = false;
                return;
            }

            console.log(`[PWA Sync] Reconnection detected. Processing ${records.length} queued offline actions...`);
            this.showToast('info', `Syncing ${records.length} offline actions with server...`);

            for (const record of records) {
                try {
                    let bodyData = record.body;
                    if (record.isFormData) {
                        bodyData = this.deserializeFormData(record.body);
                    } else if (typeof record.body === 'object') {
                        bodyData = JSON.stringify(record.body);
                    }

                    // Append CSRF Token dynamically if not present
                    const headers = new Headers(record.headers);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (csrfToken) {
                        headers.set('X-CSRF-TOKEN', csrfToken);
                    }

                    const response = await fetch(record.url, {
                        method: record.method,
                        headers: headers,
                        body: bodyData
                    });

                    if (response.ok || response.status === 422) { // 422 handles user input errors safely
                        // Remove successfully synced item from database
                        const delTransaction = this.db.transaction(['offline-sync-queue'], 'readwrite');
                        const delStore = delTransaction.objectStore('offline-sync-queue');
                        delStore.delete(record.id);
                        console.log(`[PWA Sync] Action ID ${record.id} processed and removed.`);
                    }
                } catch (err) {
                    console.error('[PWA Sync] Failed to sync action ID ' + record.id, err);
                }
            }

            this.showToast('success', 'Your offline updates have been successfully synchronized!');
            this.isSyncing = false;
            
            // Reload page if on dashboard to display fresh synchronized server state
            if (window.location.pathname.endsWith('/dashboard')) {
                setTimeout(() => window.location.reload(), 1500);
            }
        };
    }

    // ── Intercept Form Submissions Offline ──────────────────────────────────
    interceptFormSubmissions() {
        document.addEventListener('submit', (e) => {
            if (navigator.onLine) return; // Allow default online post

            const form = e.target;
            
            // Skip bypass elements or auth pages
            if (form.hasAttribute('data-no-offline') || 
                form.action.includes('/login') || 
                form.action.includes('/register') ||
                form.action.includes('/logout')
            ) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const formData = new FormData(form);
            const headers = {
                'X-Requested-With': 'XMLHttpRequest'
            };

            this.queueAction(form.action, form.method || 'POST', headers, formData, 'form')
                .then(() => {
                    // Reset loading button states if any
                    const btn = form.querySelector('[type="submit"]');
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('btn-loading');
                        btn.innerHTML = btn.getAttribute('data-original-text') || btn.innerHTML;
                    }
                    form.reset();
                });
        });
    }

    // ── Dashboard & Route Caching and Offline Fallback ──────────────────────
    cacheActiveDashboardState() {
        if (!navigator.onLine || !this.db) return;

        const path = window.location.pathname;
        if (path === '/' || path.includes('/login') || path.includes('/register') || path.includes('/logout')) {
            return;
        }

        // Wait 1.5 seconds after page load to serialize the active layout state stably
        setTimeout(() => {
            const content = document.querySelector('.space-y-6, main, #main-content') || document.body;
            if (content) {
                const widgets = {
                    html: content.innerHTML,
                    timestamp: Date.now()
                };

                const transaction = this.db.transaction(['dashboard-cache'], 'readwrite');
                const store = transaction.objectStore('dashboard-cache');
                store.put({ key: path, data: widgets, timestamp: Date.now() });
                console.log(`[PWA Cache] Cached route view state: ${path}`);
            }
        }, 1500);
    }

    loadCachedDashboardData() {
        if (navigator.onLine || !this.db) return;

        const path = window.location.pathname;
        if (path === '/' || path.includes('/login') || path.includes('/register') || path.includes('/logout')) {
            return;
        }

        const transaction = this.db.transaction(['dashboard-cache'], 'readonly');
        const store = transaction.objectStore('dashboard-cache');
        const request = store.get(path);

        request.onsuccess = (e) => {
            const record = e.target.result;
            if (record && record.data) {
                console.log(`[PWA Cache] Offline! Restoring cached view state for: ${path}`);
                
                const contentContainer = document.querySelector('.space-y-6, main, #main-content') || document.body;
                if (contentContainer && record.data.html) {
                    contentContainer.innerHTML = record.data.html;

                    // Insert Offline Notification Badge at the top of content container
                    const badge = document.createElement('div');
                    badge.className = 'w-full mb-4 px-4 py-3 bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs font-semibold rounded-xl flex items-center justify-between z-50 relative';
                    badge.innerHTML = `
                        <div class="flex items-center gap-2">
                            <span>⚠️</span>
                            <span>Viewing cached offline data. Some metrics or live updates may be stale.</span>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-amber-500/20 font-bold">OFFLINE</span>
                    `;
                    if (contentContainer.firstChild) {
                        contentContainer.insertBefore(badge, contentContainer.firstChild);
                    } else {
                        contentContainer.appendChild(badge);
                    }
                }
            }
        };
    }

    // ── Global Custom Toast Alerts Helper ───────────────────────────────────
    showToast(type, message) {
        if (window.toast) {
            window.toast(type, message);
        } else {
            console.log(`[PWA Toast] [${type}] ${message}`);
        }
    }

    // ── Network Status Handlers ─────────────────────────────────────────────
    registerNetworkStatusListeners() {
        window.addEventListener('online', () => {
            console.log('[PWA Network] Online reconnect detected.');
            this.processSyncQueue();
            // Show dynamic connection badge sync UI on app footer
            document.body.classList.remove('offline');
            this.showToast('success', 'Internet connection re-established! Synchronizing workspace...');
        });

        window.addEventListener('offline', () => {
            console.warn('[PWA Network] Device is currently offline.');
            document.body.classList.add('offline');
            this.showToast('error', 'Connection lost. Nabha Learning is now running in secure offline mode.');
        });

        // Initialize state
        if (!navigator.onLine) {
            document.body.classList.add('offline');
        }

        // Service Worker message listener for background sync tags
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', event => {
                if (event.data && event.data.type === 'SYNC_ACTIONS') {
                    this.processSyncQueue();
                }
            });
        }
    }
}

// Instantiate globally
window.pwaOfflineStore = new PWAOfflineStore();
