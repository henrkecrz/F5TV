/**
 * F5 TV — Service Worker
 * Estratégia: Cache First para assets estáticos, Network First para páginas.
 */

const CACHE_NAME    = 'f5tv-v1';
const OFFLINE_URL   = '/offline/';

const STATIC_ASSETS = [
    '/',
    '/series/',
    '/ao-vivo/',
    '/manifest.json',
];

/* ── Install: pré-cache assets críticos ── */
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(() => {});
        }).then(() => self.skipWaiting())
    );
});

/* ── Activate: limpar caches antigos ── */
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k))
            )
        ).then(() => self.clients.claim())
    );
});

/* ── Fetch: estratégia híbrida ── */
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorar: requests não-GET, admin WP, REST API, player vimeo/yt
    if (
        request.method !== 'GET' ||
        url.pathname.startsWith('/wp-admin') ||
        url.pathname.startsWith('/wp-json') ||
        url.hostname.includes('vimeo') ||
        url.hostname.includes('youtube') ||
        url.hostname.includes('googleapis')
    ) return;

    // Assets estáticos (CSS, JS, imagens, fonts) → Cache First
    const isStatic = /\.(css|js|png|jpg|jpeg|webp|svg|woff2?|ttf|ico)(\?.*)?$/.test(url.pathname);

    if (isStatic) {
        event.respondWith(
            caches.match(request).then(cached => {
                if (cached) return cached;
                return fetch(request).then(response => {
                    if (response && response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(c => c.put(request, clone));
                    }
                    return response;
                }).catch(() => cached || new Response('', { status: 408 }));
            })
        );
        return;
    }

    // Páginas HTML → Network First, fallback para cache
    event.respondWith(
        fetch(request).then(response => {
            if (response && response.status === 200) {
                const clone = response.clone();
                caches.open(CACHE_NAME).then(c => c.put(request, clone));
            }
            return response;
        }).catch(() =>
            caches.match(request).then(cached =>
                cached || caches.match('/') || new Response('<h1>F5 TV — Sem conexão</h1>', {
                    headers: { 'Content-Type': 'text/html; charset=utf-8' }
                })
            )
        )
    );
});

/* ── Push Notifications (futuro) ── */
self.addEventListener('push', event => {
    if (!event.data) return;
    const data = event.data.json();
    event.waitUntil(
        self.registration.showNotification(data.title || 'F5 TV', {
            body:  data.body  || 'Nova notificação F5 TV',
            icon:  '/wp-content/themes/f5tv-theme/assets/icons/icon-192.png',
            badge: '/wp-content/themes/f5tv-theme/assets/icons/icon-72.png',
            data:  { url: data.url || '/' },
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const url = event.notification.data?.url || '/';
    event.waitUntil(clients.openWindow(url));
});
