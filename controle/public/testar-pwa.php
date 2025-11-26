<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste PWA - Avaliador Sabores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #f5576c; }
        .check-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 5px solid #ddd;
        }
        .check-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .check-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .check-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .icon-preview img {
            max-width: 100px;
            border: 2px solid #ddd;
            border-radius: 10px;
            margin: 5px;
        }
        button {
            padding: 12px 24px;
            background: #f5576c;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background: #d94558;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico PWA - Avaliador Sabores</h1>

        <p>Esta página verifica se o PWA está configurado corretamente.</p>

        <button onclick="runTests()">▶️ Executar Testes</button>
        <button onclick="location.reload()">🔄 Recarregar</button>

        <div id="results"></div>

        <hr style="margin: 30px 0;">

        <h2>📋 Checklist de Instalação</h2>
        <div class="check-item">
            <strong>1. HTTPS Ativo?</strong><br>
            <span id="httpsCheck">Verificando...</span>
        </div>
        <div class="check-item">
            <strong>2. Service Worker Registrado?</strong><br>
            <span id="swCheck">Verificando...</span>
        </div>
        <div class="check-item">
            <strong>3. Manifest.json Carregado?</strong><br>
            <span id="manifestCheck">Verificando...</span>
        </div>
        <div class="check-item">
            <strong>4. Ícones Disponíveis?</strong><br>
            <span id="iconsCheck">Verificando...</span>
            <div id="iconPreviews" class="icon-preview"></div>
        </div>

        <hr style="margin: 30px 0;">

        <h2>🛠️ Soluções</h2>
        <div class="check-item warning">
            <strong>Se o app abre como página web:</strong>
            <ol>
                <li>Desinstale o app atual do tablet (pressione e segure → Remover)</li>
                <li>Limpe o cache do navegador</li>
                <li>Feche completamente o navegador</li>
                <li>Abra novamente: <code>https://saboresemmovimento.com.br/controle/avaliador</code></li>
                <li>Aguarde 2-3 segundos para o PWA ser detectado</li>
                <li>Instale novamente usando "Adicionar à tela inicial"</li>
            </ol>
        </div>
    </div>

    <script>
        // Verificar HTTPS
        document.getElementById('httpsCheck').innerHTML =
            location.protocol === 'https:'
                ? '✅ HTTPS ativo'
                : '❌ HTTPS inativo (obrigatório para PWA)';

        // Verificar Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistration()
                .then(registration => {
                    if (registration) {
                        document.getElementById('swCheck').innerHTML =
                            `✅ Service Worker registrado<br><small>Scope: ${registration.scope}</small>`;
                    } else {
                        document.getElementById('swCheck').innerHTML =
                            '❌ Service Worker não registrado';
                    }
                });
        } else {
            document.getElementById('swCheck').innerHTML =
                '❌ Service Worker não suportado neste navegador';
        }

        // Verificar Manifest
        fetch('/controle/manifest.json')
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Manifest não encontrado');
            })
            .then(manifest => {
                document.getElementById('manifestCheck').innerHTML =
                    `✅ Manifest carregado<br><small>Nome: ${manifest.name}<br>Start URL: ${manifest.start_url}</small>`;

                // Verificar ícones
                checkIcons(manifest.icons);
            })
            .catch(error => {
                document.getElementById('manifestCheck').innerHTML =
                    `❌ Erro ao carregar manifest: ${error.message}`;
            });

        // Verificar ícones
        function checkIcons(icons) {
            if (!icons || icons.length === 0) {
                document.getElementById('iconsCheck').innerHTML = '❌ Nenhum ícone configurado';
                return;
            }

            let iconsHTML = `✅ ${icons.length} ícone(s) configurado(s)<br>`;
            let previewHTML = '';

            icons.forEach(icon => {
                // Tentar carregar cada ícone
                const img = new Image();
                img.src = icon.src;
                img.onerror = () => {
                    iconsHTML += `<br>❌ Ícone ${icon.sizes} não encontrado: ${icon.src}`;
                };
                img.onload = () => {
                    iconsHTML += `<br>✅ Ícone ${icon.sizes} OK`;
                    previewHTML += `<img src="${icon.src}" alt="${icon.sizes}">`;
                    document.getElementById('iconPreviews').innerHTML = previewHTML;
                };
            });

            document.getElementById('iconsCheck').innerHTML = iconsHTML;
        }

        // Executar testes completos
        function runTests() {
            const results = document.getElementById('results');
            results.innerHTML = '<h2>🧪 Resultados dos Testes</h2>';

            // Teste 1: Display mode
            const displayMode = window.matchMedia('(display-mode: standalone)').matches
                ? 'standalone'
                : window.matchMedia('(display-mode: fullscreen)').matches
                    ? 'fullscreen'
                    : 'browser';

            results.innerHTML += `
                <div class="check-item ${displayMode !== 'browser' ? 'success' : 'warning'}">
                    <strong>Display Mode:</strong> ${displayMode}<br>
                    ${displayMode === 'browser' ? '⚠️ App está rodando no navegador, não como PWA instalado' : '✅ App está rodando como PWA'}
                </div>
            `;

            // Teste 2: Verificar se está instalado
            window.addEventListener('beforeinstallprompt', (e) => {
                results.innerHTML += `
                    <div class="check-item warning">
                        <strong>PWA não instalado</strong><br>
                        O navegador detectou que o app pode ser instalado.
                    </div>
                `;
            });

            window.addEventListener('appinstalled', () => {
                results.innerHTML += `
                    <div class="check-item success">
                        ✅ PWA foi instalado com sucesso!
                    </div>
                `;
            });

            // Teste 3: Informações do navegador
            results.innerHTML += `
                <div class="check-item">
                    <strong>Navegador:</strong> ${navigator.userAgent}<br>
                    <strong>Plataforma:</strong> ${navigator.platform}<br>
                    <strong>Online:</strong> ${navigator.onLine ? 'Sim' : 'Não'}
                </div>
            `;

            // Teste 4: Verificar se manifest está linkado
            const manifestLink = document.querySelector('link[rel="manifest"]');
            if (manifestLink) {
                results.innerHTML += `
                    <div class="check-item success">
                        ✅ Tag manifest encontrada: <code>${manifestLink.href}</code>
                    </div>
                `;
            } else {
                results.innerHTML += `
                    <div class="check-item error">
                        ❌ Tag manifest NÃO encontrada no HTML
                    </div>
                `;
            }
        }
    </script>
</body>
</html>
