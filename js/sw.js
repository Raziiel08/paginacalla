const CACHE_ESTATICO = 'gamervault-estatico-v1';
const CACHE_IMAGENES = 'gamervault-imagenes-v1';
const CACHE_API = 'gamervault-api-v1';

// Recursos esenciales a precargar durante la instalación
const RECURSOS_ESTATICOS = [
  'index.php',
  'resultados.php',
  'juego.php',
  'perfil.php',
  'auth.php',
  'css/styles.css',
  'js/main.js',
  'assets/img/perfiles/default.jpg',
  'assets/img/invisible.png',
  'assets/img/vista.png'
];

// Evento Install: Pre-cachea recursos estáticos esenciales
self.addEventListener('install', (evento) => {
  evento.waitUntil(
    caches.open(CACHE_ESTATICO).then((cache) => {
      console.log('[Service Worker] Precargando recursos estáticos...');
      return cache.addAll(RECURSOS_ESTATICOS);
    }).then(() => self.skipWaiting())
  );
});

// Evento Activate: Limpia cachés antiguas
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

  // Evitar interceptar llamadas a php locales para autenticación o base de datos que requieren ejecución en tiempo real
  if (url.pathname.includes('/php/') || evento.request.method !== 'GET') {
    return; // Dejar pasar al servidor directo
  }

  // 1. ESTRATEGIA PARA PETICIONES A LA API (CheapShark API)
  if (url.hostname.includes('cheapshark.com') && url.pathname.includes('/api/1.0/')) {
    evento.respondWith(
      fetch(evento.request)
        .then((respuestaRed) => {
          // Si la respuesta es exitosa, guardar una copia en el caché de la API
          if (respuestaRed.status === 200) {
            const respuestaClonada = respuestaRed.clone();
            caches.open(CACHE_API).then((cache) => {
              cache.put(evento.request, respuestaClonada);
            });
          }
          return respuestaRed;
        })
        .catch(() => {
          // Si falla la red (offline), buscar en el caché
          return caches.match(evento.request).then((respuestaCache) => {
            if (respuestaCache) {
              return respuestaCache;
            }
            // Retornar un error JSON básico estructurado si no está en caché y está offline
            return new Response(JSON.stringify({ error: "Offline y sin datos en caché" }), {
              headers: { 'Content-Type': 'application/json' }
            });
          });
        })
    );
    return;
  }

  // 2. ESTRATEGIA PARA IMÁGENES (Portadas de juegos externas y avatares locales)
  if (evento.request.destination === 'image' || url.pathname.match(/\.(jpg|jpeg|png|gif|webp|svg)/i)) {
    evento.respondWith(
      caches.match(evento.request).then((respuestaCache) => {
        if (respuestaCache) {
          // Si está en caché, servir inmediatamente
          return respuestaCache;
        }
        // Si no está, buscar en red, cachear y retornar
        return fetch(evento.request).then((respuestaRed) => {
          if (respuestaRed.status === 200 || respuestaRed.type === 'opaque') {
            const respuestaClonada = respuestaRed.clone();
            caches.open(CACHE_IMAGENES).then((cache) => {
              cache.put(evento.request, respuestaClonada);
            });
          }
          return respuestaRed;
        }).catch((err) => {
          // Fallback en caso de que esté offline y no haya imagen en caché (retornar default local si es avatar)
          if (url.pathname.includes('/perfiles/')) {
            return caches.match('assets/img/perfiles/default.jpg');
          }
          // De lo contrario, retornar error o dejar fallar
          throw err;
        });
      })
    );
    return;
  }

  // 3. ESTRATEGIA PARA RECURSOS LOCALES (HTML/PHP, CSS, JS) - Stale-While-Revalidate
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
        // Ignorar errores de red en segundo plano
      });

      // Retorna el caché inmediatamente si existe, si no espera la respuesta de la red
      return respuestaCache || fetchRed;
    })
  );
});
