const STATIC_CACHE = 'sidedikk-static-v20260725-1';
const PAGE_CACHE = 'sidedikk-pages-v20260725-1';
const STATIC_ASSETS = [
    '/offline.html',
    '/brand/icon-192.png',
    '/brand/icon-512.png',
    '/favicon.ico',
];

const SENSITIVE_PREFIXES = [
    '/dashboard',
    '/admin',
    '/screenings',
    '/history',
    '/profile',
    '/education',
    '/login',
    '/register',
    '/forgot-password',
    '/reset-password',
    '/verify-email',
    '/confirm-password',
];

function isSensitivePath(pathname) {
    return SENSITIVE_PREFIXES.some((prefix) => pathname === prefix || pathname.startsWith(`${prefix}/`));
}

function isStaticAsset(pathname) {
    return pathname.startsWith('/build/assets/') || pathname.startsWith('/brand/');
}

async function cacheFirst(request) {
    const cache = await caches.open(STATIC_CACHE);
    const cached = await cache.match(request);

    if (cached) {
        return cached;
    }

    const response = await fetch(request);

    if (response.ok) {
        cache.put(request, response.clone());
    }

    return response;
}

async function networkFirst(request) {
    const cache = await caches.open(PAGE_CACHE);

    try {
        const response = await fetch(request);

        if (response.ok && !response.headers.get('Cache-Control')?.includes('no-store')) {
            cache.put(request, response.clone());
        }

        return response;
    } catch (error) {
        const cached = await cache.match(request);

        if (cached) {
            return cached;
        }

        if (request.mode === 'navigate') {
            return (await caches.open(STATIC_CACHE)).match('/offline.html');
        }

        throw error;
    }
}

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        const cacheNames = await caches.keys();

        await Promise.all(
            cacheNames
                .filter((cacheName) => ![STATIC_CACHE, PAGE_CACHE].includes(cacheName))
                .map((cacheName) => caches.delete(cacheName))
        );

        await self.clients.claim();
    })());
});

self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    if (request.method !== 'GET') {
        return;
    }

    if (url.origin !== self.location.origin) {
        return;
    }

    if (isSensitivePath(url.pathname)) {
        event.respondWith(fetch(request));
        return;
    }

    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    event.respondWith(networkFirst(request));
});
