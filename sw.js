const CACHE_ESTATICO = 'gamervault-estatico-v2';
const CACHE_IMAGENES = 'gamervault-imagenes-v2';
const CACHE_API = 'gamervault-api-v2';

// Recursos estáticos puros (no páginas dinámicas PHP)
const RECURSOS_ESTATICOS = [
  'css/styles.css',
  'js/main.js',
  'assets/img/perfiles/default.jpg',
  'assets/img/invisible.png',
  'assets/img/vista.png'
];

// Evento Install: Pre-cachea recursos estáticos puros
self.addEventListener('install', (evento) => {
  evento.waitUntil(
    caches.open(CACHE_ESTATICO).then((cache) => {
      console.log('[Service Worker] Precargando recursos estáticos...');
      return cache.addAll(RECURSOS_ESTATICOS);
    }).then(() => self.skipWaiting())
  );
});

// Evento Activate: Limpia cachés antiguas e invalida versiones anteriores
self.addEventListener('activate', (evento) => {
  evento.waitUntil(
    caches.keys().then((claves) => {
      return Promise.all(
        claves.map((clave) => {
          if (clave !== CACHE_ESTATICO && clave !== CACHE_IMAGENES && clave !== CACHE_API) {
            console.log('[Service Worker] Eliminando caché antigua:', clave);
            return caches.delete(clave);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Evento Fetch: Intercepta y gestiona peticiones
self.addEventListener('fetch', (evento) => {
  const url = new URL(evento.request.url);

  if (
    url.pathname.includes('/php/') || 
    url.pathname.endsWith('.php') || 
    url.pathname.includes('.php?') || 
    url.pathname.includes('.php/') || 
    evento.request.method !== 'GET'
  ) {
    return; // Dejar pasar al servidor en tiempo real
  }

  // 1. ESTRATEGIA PARA PETICIONES A LA API (CheapShark API)
  if (url.hostname.includes('cheapshark.com') && url.pathname.includes('/api/1.0/')) {
    evento.respondWith(
      fetch(evento.request)
        .then((respuestaRed) => {
          if (respuestaRed.status === 200) {
            const respuestaClonada = respuestaRed.clone();
            caches.open(CACHE_API).then((cache) => {
              cache.put(evento.request, respuestaClonada);
            });
          }
          return respuestaRed;
        })
        .catch(() => {
          return caches.match(evento.request).then((respuestaCache) => {
            if (respuestaCache) {
              return respuestaCache;
            }
            return new Response(JSON.stringify({ error: "Offline y sin datos en caché" }), {
              headers: { 'Content-Type': 'application/json' }
            });
          });
        })
    );
    return;
  }

  // IMÁGENES (Portadas de juegos externas y avatares locales)
  if (evento.request.destination === 'image' || url.pathname.match(/\.(jpg|jpeg|png|gif|webp|svg)/i)) {
    evento.respondWith(
      caches.match(evento.request).then((respuestaCache) => {
        if (respuestaCache) {
          return respuestaCache;
        }
        return fetch(evento.request).then((respuestaRed) => {
          if (respuestaRed.status === 200 || respuestaRed.type === 'opaque') {
            const respuestaClonada = respuestaRed.clone();
            caches.open(CACHE_IMAGENES).then((cache) => {
              cache.put(evento.request, respuestaClonada);
            });
          }
          return respuestaRed;
        }).catch((err) => {
          if (url.pathname.includes('/perfiles/')) {
            return caches.match('assets/img/perfiles/default.jpg');
          }
          throw err;
        });
      })
    );
    return;
  }

  // RECURSOS LOCALES ESTÁTICOS (CSS, JS)
  evento.respondWith(
    caches.match(evento.request).then((respuestaCache) => {
      const fetchRed = fetch(evento.request).then((respuestaRed) => {
        if (respuestaRed.status === 200) {
          const respuestaClonada = respuestaRed.clone();
          caches.open(CACHE_ESTATICO).then((cache) => {
            cache.put(evento.request, respuestaClonada);
          });
        }
        return respuestaRed;
      }).catch(() => {
        // Ignorar
      });

      return respuestaCache || fetchRed;
    })
  );
});
