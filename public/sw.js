const CACHE_NAME = "jf-pwa-v1";
const OFFLINE_URL = "/offline";

self.addEventListener("install", (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) =>
      cache.addAll([
        OFFLINE_URL,
        "/",
        "/manifest.webmanifest",
      ])
    )
  );
  self.skipWaiting();
});

self.addEventListener("activate", (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((key) => key !== CACHE_NAME && caches.delete(key)))
    )
  );
  self.clients.claim();
});

self.addEventListener("fetch", (event) => {
  const req = event.request;

  // só GET
  if (req.method !== "GET") return;

  event.respondWith(
    fetch(req)
      .then((res) => {
        // cacheia páginas/arquivos básicos quando online
        const resClone = res.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(req, resClone));
        return res;
      })
      .catch(async () => {
        // se falhar (offline), tenta cache
        const cached = await caches.match(req);
        return cached || (await caches.match(OFFLINE_URL));
      })
  );
});
