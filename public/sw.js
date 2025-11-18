const CACHE_NAME = 'avaliador-sabores-v2';
const urlsToCache = [
  '/public/avaliador',
  '/public/avaliador/avaliar-cardapio',
  '/public/avaliador/avaliar-colaboradora',
  '/public/avaliador/obrigado',
  '/public/login',
  '/public/manifest.json',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://code.jquery.com/jquery-3.7.0.min.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  console.log('[Service Worker] Instalando...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[Service Worker] Cache aberto');
        return cache.addAll(urlsToCache.map(url => {
          return new Request(url, {mode: 'no-cors'});
        })).catch(err => {
          console.log('[Service Worker] Erro ao cachear:', err);
        });
      })
  );
  self.skipWaiting();
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
  console.log('[Service Worker] Ativando...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[Service Worker] Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim();
});

// Interceptação de requisições
self.addEventListener('fetch', event => {
  // Ignora requisições POST (formulários)
  if (event.request.method !== 'GET') {
    return;
  }

  event.respondWith(
    caches.match(event.request)
      .then(response => {
        // Retorna do cache se existir
        if (response) {
          // Busca atualização em background
          fetch(event.request).then(newResponse => {
            if (newResponse && newResponse.status === 200) {
              caches.open(CACHE_NAME).then(cache => {
                cache.put(event.request, newResponse);
              });
            }
          }).catch(() => {
            // Falha silenciosa se offline
          });
          return response;
        }

        // Se não estiver no cache, busca da rede
        return fetch(event.request).then(newResponse => {
          // Não cachear respostas inválidas
          if (!newResponse || newResponse.status !== 200 || newResponse.type === 'error') {
            return newResponse;
          }

          // Clona a resposta
          const responseToCache = newResponse.clone();

          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });

          return newResponse;
        }).catch(() => {
          // Se falhar e for navegação, retorna página offline
          if (event.request.mode === 'navigate') {
            return caches.match('/public/avaliador');
          }
        });
      })
  );
});

// Sincronização em background (para salvar avaliações offline)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-avaliacoes') {
    console.log('[Service Worker] Sincronizando avaliações...');
    event.waitUntil(syncAvaliacoes());
  }
});

async function syncAvaliacoes() {
  // Lógica para enviar avaliações pendentes quando voltar online
  const cache = await caches.open(CACHE_NAME);
  // Implementar sincronização conforme necessário
}

// Notificações Push (opcional)
self.addEventListener('push', event => {
  const data = event.data ? event.data.json() : {};
  const title = data.title || 'Avaliador Sabores';
  const options = {
    body: data.body || 'Nova notificação',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: data.url || '/avaliador'
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Clique em notificação
self.addEventListener('notificationclick', event => {
  event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data)
  );
});
