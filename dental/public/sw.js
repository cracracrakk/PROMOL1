// DentalCore service worker
const CACHE = 'dentalcore-v1';
const STATIC = [
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/odontogram.js',
    '/manifest.json',
];

self.addEventListener('install', (e) => {
    e.waitUntil(caches.open(CACHE).then((c) => c.addAll(STATIC)));
    self.skipWaiting();
});

self.addEventListener('activate', (e) => {
    e.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (e) => {
    const url = new URL(e.request.url);
    if (e.request.method !== 'GET') return;
    // Network-first para /admin y API, cache-first para assets
    if (url.pathname.startsWith('/assets/')) {
        e.respondWith(
            caches.match(e.request).then((c) => c || fetch(e.request).then((r) => {
                const copy = r.clone();
                caches.open(CACHE).then((cc) => cc.put(e.request, copy));
                return r;
            }))
        );
    } else {
        e.respondWith(
            fetch(e.request).catch(() => caches.match(e.request))
        );
    }
});
