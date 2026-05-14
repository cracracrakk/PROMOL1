// Service Worker básico (PWA)
const CACHE_VERSION = 'spa-v1';
const STATIC_ASSETS = ['/assets/css/style.css', '/assets/js/public.js'];

self.addEventListener('install', e => {
    e.waitUntil(caches.open(CACHE_VERSION).then(c => c.addAll(STATIC_ASSETS)));
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys => Promise.all(
            keys.filter(k => k !== CACHE_VERSION).map(k => caches.delete(k))
        ))
    );
    self.clients.claim();
});

self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    e.respondWith(
        caches.match(e.request).then(r => r || fetch(e.request).then(resp => {
            // Sólo cachear estáticos
            if (e.request.url.includes('/assets/')) {
                const clone = resp.clone();
                caches.open(CACHE_VERSION).then(c => c.put(e.request, clone));
            }
            return resp;
        }).catch(() => caches.match('/')))
    );
});
