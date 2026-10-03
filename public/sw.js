// MedBando Service Worker
const CACHE_NAME = 'medbando-cache-v1';
const PRECACHE_ASSETS = [
  '/',
  '/manifest.json',
  '/img/icon-192.png',
  '/img/icon-512.png',
  '/favicon.png',
  '/apple-touch-icon.png'
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PRECACHE_ASSETS).catch((err) => {
        console.warn('[MedBando SW] Pre-cache warning:', err);
      });
    })
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((name) => {
          if (name !== CACHE_NAME) {
            return caches.delete(name);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Network-first caching strategy for dynamic Laravel app
self.addEventListener('fetch', (event) => {
  // Only handle GET requests and http/https
  if (event.request.method !== 'GET' || !event.request.url.startsWith('http')) {
    return;
  }

  // Never cache API, status-check or csrf-sensitive polling
  const url = new URL(event.request.url);
  if (url.pathname.includes('/api/') || url.pathname.includes('/check-status/') || url.pathname.includes('/live_sessions')) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            // Cache static assets (images, css, js)
            if (url.pathname.match(/\.(png|jpg|jpeg|svg|css|js|woff2|woff)$/)) {
              cache.put(event.request, responseToCache);
            }
          });
        }
        return networkResponse;
      })
      .catch(() => {
        return caches.match(event.request).then((cachedResponse) => {
          if (cachedResponse) {
            return cachedResponse;
          }
          // If offline and navigating to a page, return a basic offline notice if desired
          return new Response(
            `<!DOCTYPE html>
            <html lang="sw">
            <head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
            <title>MedBando - Hauna Mtandao</title>
            <style>
              body { background: #07162A; color: white; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; padding: 20px; }
              .card { background: rgba(255,255,255,0.05); padding: 30px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); max-width: 400px; }
              h1 { font-size: 1.4rem; color: #FF3300; margin-bottom: 10px; }
              p { color: #94A3B8; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; }
              button { background: #FF3300; color: white; border: none; padding: 10px 24px; border-radius: 50px; font-weight: bold; cursor: pointer; font-size: 0.95rem; }
            </style>
            </head>
            <body>
              <div class="card">
                <h1>MedBando — Hauna Mtandao</h1>
                <p>Tafadhali kagua muunganisho wako wa intaneti kisha ujaribu tena.</p>
                <button onclick="window.location.reload()">Jaribu Tena</button>
              </div>
            </body>
            </html>`,
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        });
      })
  );
});
