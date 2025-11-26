<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Sistema de Avaliação') ?></title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#f5576c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Avaliador">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('icon-192.png.php') ?>">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .btn-logout-discreto {
            position: fixed;
            top: 10px;
            right: 10px;
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
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>')
                    .then(registration => {
                        console.log('Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Falha ao registrar Service Worker:', error);
                    });
            });
        }

        // Detectar instalação do PWA
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            console.log('PWA pronto para instalação');
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA instalado com sucesso!');
            deferredPrompt = null;
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
