<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= esc($titulo ?? 'Sistema de Avaliação') ?></title>

    <!-- PWA Meta Tags -->
    <meta name="application-name" content="Avaliador Sabores">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#6f42c1">
    <meta name="description" content="Sistema de Avaliação de Cardápio e Colaboradoras">

    <!-- iOS Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Avaliador">

    <!-- Android Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">

    <!-- Manifest -->
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" href="<?= base_url('icons/icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url('icons/icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="96x96" href="<?= base_url('icons/icon-96x96.png') ?>">
    <link rel="apple-touch-icon" sizes="128x128" href="<?= base_url('icons/icon-128x128.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url('icons/icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('icons/icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="192x192" href="<?= base_url('icons/icon-192x192.png') ?>">
    <link rel="apple-touch-icon" sizes="384x384" href="<?= base_url('icons/icon-384x384.png') ?>">
    <link rel="apple-touch-icon" sizes="512x512" href="<?= base_url('icons/icon-512x512.png') ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('icons/icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('icons/icon-512x512.png') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* PWA Fullscreen Styles */
        :root {
            --safe-area-inset-top: env(safe-area-inset-top, 0px);
            --safe-area-inset-right: env(safe-area-inset-right, 0px);
            --safe-area-inset-bottom: env(safe-area-inset-bottom, 0px);
            --safe-area-inset-left: env(safe-area-inset-left, 0px);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            padding-top: var(--safe-area-inset-top);
            padding-right: var(--safe-area-inset-right);
            padding-bottom: var(--safe-area-inset-bottom);
            padding-left: var(--safe-area-inset-left);
            overflow-x: hidden;
            -webkit-user-select: none;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
        }

        html {
            height: -webkit-fill-available;
        }

        /* Impede zoom duplo toque */
        * {
            touch-action: manipulation;
        }

        /* Botão logout discreto */
        .btn-logout-discreto {
            position: fixed;
            top: calc(10px + var(--safe-area-inset-top));
            right: calc(10px + var(--safe-area-inset-right));
            background: rgba(0, 0, 0, 0.1);
            color: #666;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s;
            z-index: 9999;
            cursor: pointer;
        }

        .btn-logout-discreto:hover {
            background: rgba(0, 0, 0, 0.2);
            color: #333;
        }

        /* Estilos específicos quando instalado como PWA */
        @media (display-mode: standalone) {
            body {
                overflow: hidden;
            }

            /* Oculta scrollbar em modo standalone */
            ::-webkit-scrollbar {
                display: none;
            }

            /* Remove seleção de texto em modo app */
            body.pwa-installed {
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }
        }

        /* Para iOS em modo app */
        @supports (-webkit-touch-callout: none) {
            body {
                min-height: -webkit-fill-available;
            }
        }
    </style>
</head>
<body>
    <!-- Botão de Sair Discreto -->
    <a href="<?= base_url('logout') ?>" class="btn-logout-discreto" title="Sair">
        <i class="fas fa-sign-out-alt"></i> Sair
    </a>

    <!-- Conteúdo Principal -->
    <main class="container-fluid">
        <!-- Mensagens Flash -->
        <?php if (session()->getFlashdata('sucesso')): ?>
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('sucesso') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('erro')): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('erro') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Conteúdo da Página -->
        <?= $this->renderSection('content') ?>
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-hide alerts após 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Registrar Service Worker para PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
                    .then(function(registration) {
                        console.log('Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch(function(error) {
                        console.log('Falha ao registrar Service Worker:', error);
                    });
            });
        }

        // Detectar quando o app está em modo standalone (instalado)
        window.addEventListener('DOMContentLoaded', function() {
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches ||
                                 window.navigator.standalone === true;

            if (isStandalone) {
                console.log('App rodando em modo standalone (instalado)');
                document.body.classList.add('pwa-installed');
            }
        });

        // Evento de instalação do PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            deferredPrompt = e;
            console.log('PWA pode ser instalado');
        });

        // Detectar quando o PWA foi instalado
        window.addEventListener('appinstalled', function() {
            console.log('PWA instalado com sucesso!');
            deferredPrompt = null;
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
