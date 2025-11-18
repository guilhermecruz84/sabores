<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo ?? 'Sistema de Chamados') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #004E89;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --dark-color: #1A1A2E;
            --light-bg: #F8F9FA;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
        }

        /* Mobile Menu */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 56px;
                left: -100%;
                width: 80%;
                max-width: 300px;
                height: calc(100vh - 56px);
                z-index: 1000;
                transition: left 0.3s ease;
                overflow-y: auto;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 56px;
                left: 0;
                width: 100%;
                height: calc(100vh - 56px);
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .sidebar-overlay.show {
                display: block;
            }

            .mobile-menu-toggle {
                display: inline-block !important;
            }
        }

        @media (min-width: 768px) {
            .mobile-menu-toggle {
                display: none !important;
            }
        }

        .sidebar .nav-link {
            color: #6c757d;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            transform: translateX(5px);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
        }

        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .stat-icon {
            font-size: 3rem;
            opacity: 0.2;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <?php if (isset($usuarioLogado)): ?>
            <button class="btn btn-link text-white mobile-menu-toggle me-2" type="button" id="sidebarToggle" style="display: none;">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            <?php endif; ?>

            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <i class="fas fa-utensils me-2"></i>
                Sabores Refeitório
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($usuarioLogado)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= esc($usuarioLogado->nome) ?>
                                <span class="badge bg-light text-dark ms-1">
                                    <?php
                                    $tipoLabels = [
                                        'admin' => 'Admin',
                                        'atendente' => 'Administrativo',
                                        'cliente' => 'Cliente',
                                        'operador' => 'Operador',
                                        'avaliador' => 'Avaliador'
                                    ];
                                    echo $tipoLabels[$usuarioLogado->tipo] ?? ucfirst($usuarioLogado->tipo);
                                    ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= base_url('perfil') ?>"><i class="fas fa-user me-2"></i>Meu Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php if (isset($usuarioLogado)): ?>
            <nav class="col-md-2 sidebar px-0" id="sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <!-- Dashboard - Admin, Atendente, Cliente (NÃO Operador) -->
                        <?php if ($usuarioLogado->tipo !== 'operador'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Chamados - Admin, Atendente, Cliente (NÃO Operador) -->
                        <?php if ($usuarioLogado->tipo !== 'operador'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'chamados') !== false ? 'active' : '' ?>" href="<?= base_url('chamados') ?>">
                                <i class="fas fa-ticket-alt"></i>
                                Chamados
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Avaliações - Cliente -->
                        <?php if ($usuarioLogado->tipo === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliacoes') !== false && strpos(uri_string(), 'colaboradora') === false ? 'active' : '' ?>" href="<?= base_url('avaliacoes') ?>">
                                <i class="fas fa-star"></i>
                                Avaliar Cardápio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliacao-colaboradora-cliente') !== false ? 'active' : '' ?>" href="<?= base_url('avaliacao-colaboradora-cliente') ?>">
                                <i class="fas fa-user-check"></i>
                                Avaliação Colaboradora
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Avaliador - Menu simplificado para tablet -->
                        <?php if ($usuarioLogado->tipo === 'avaliador'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliador') !== false ? 'active' : '' ?>" href="<?= base_url('avaliador') ?>">
                                <i class="fas fa-star"></i>
                                Sistema de Avaliação
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Checklists - Operador -->
                        <?php if ($usuarioLogado->tipo === 'operador'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'checklists') !== false ? 'active' : '' ?>" href="<?= base_url('checklists') ?>">
                                <i class="fas fa-clipboard-check"></i>
                                Meus Checklists
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Checklists - Admin/Atendente -->
                        <?php if ($usuarioLogado->tipo === 'admin' || $usuarioLogado->tipo === 'atendente'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'checklists/relatorio') !== false ? 'active' : '' ?>" href="<?= base_url('checklists/relatorio') ?>">
                                <i class="fas fa-clipboard-check"></i>
                                Relatório Checklists
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Alertas - Admin/Atendente -->
                        <?php if ($usuarioLogado->tipo === 'admin' || $usuarioLogado->tipo === 'atendente'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'alertas') !== false ? 'active' : '' ?>" href="<?= base_url('alertas') ?>">
                                <i class="fas fa-exclamation-triangle"></i>
                                Alertas
                                <span class="badge bg-danger ms-2" id="badgeAlertasPendentes" style="display: none;">0</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Avaliações - Admin/Atendente -->
                        <?php if ($usuarioLogado->tipo === 'admin' || $usuarioLogado->tipo === 'atendente'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliacoes') !== false && strpos(uri_string(), 'colaboradora') === false ? 'active' : '' ?>" href="<?= base_url('avaliacoes/dashboard') ?>">
                                <i class="fas fa-star"></i>
                                Avaliações Cardápio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliacao-colaboradora-cliente') !== false ? 'active' : '' ?>" href="<?= base_url('avaliacao-colaboradora-cliente/dashboard') ?>">
                                <i class="fas fa-user-check"></i>
                                Avaliações Colaboradora
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Itens do Checklist - Apenas Admin -->
                        <?php if ($usuarioLogado->tipo === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'checklists/itens') !== false ? 'active' : '' ?>" href="<?= base_url('checklists/itens') ?>">
                                <i class="fas fa-tasks"></i>
                                Itens do Checklist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'checklists/configurar-dias') !== false ? 'active' : '' ?>" href="<?= base_url('checklists/configurar-dias') ?>">
                                <i class="fas fa-calendar-alt"></i>
                                Dias da Semana
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'avaliacoes/gerenciar-cardapios') !== false ? 'active' : '' ?>" href="<?= base_url('avaliacoes/gerenciar-cardapios') ?>">
                                <i class="fas fa-utensils"></i>
                                Gerenciar Cardápios
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Usuários - Apenas Admin (NÃO Atendente) -->
                        <?php if ($usuarioLogado->tipo === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'usuarios') !== false ? 'active' : '' ?>" href="<?= base_url('usuarios') ?>">
                                <i class="fas fa-users"></i>
                                Usuários
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Empresas - Apenas Admin (NÃO Atendente) -->
                        <?php if ($usuarioLogado->tipo === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'empresas') !== false ? 'active' : '' ?>" href="<?= base_url('empresas') ?>">
                                <i class="fas fa-building"></i>
                                Empresas
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Módulo Operacional - Apenas Admin -->
                        <?php if ($usuarioLogado->tipo === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'operacional') !== false ? 'active' : '' ?>" href="<?= base_url('operacional/dashboard') ?>">
                                <i class="fas fa-chart-line"></i>
                                Operacional
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>

            <!-- Main Content -->
            <main class="col-md-<?= isset($usuarioLogado) ? '10' : '12' ?> ms-sm-auto px-md-4">
                <div class="py-4">
                    <!-- Mensagens Flash -->
                    <?php if (session()->getFlashdata('sucesso')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('sucesso') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('erro')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('erro') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('erros')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                                    <li><?= $erro ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Conteúdo da Página -->
                    <?= $this->renderSection('content') ?>
                </div>
            </main>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

    <!-- Custom JS -->
    <script src="<?= base_url('js/app.js') ?>"></script>

    <script>
        // Configuração padrão do DataTables
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 25,
            responsive: true
        });

        // Auto-hide alerts após 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Mobile Menu Toggle
        $(document).ready(function() {
            const sidebarToggle = $('#sidebarToggle');
            const sidebar = $('#sidebar');
            const overlay = $('#sidebarOverlay');

            // Abrir menu mobile
            sidebarToggle.on('click', function() {
                sidebar.toggleClass('show');
                overlay.toggleClass('show');
            });

            // Fechar menu ao clicar no overlay
            overlay.on('click', function() {
                sidebar.removeClass('show');
                overlay.removeClass('show');
            });

            // Fechar menu ao clicar em um link (mobile)
            if ($(window).width() < 768) {
                sidebar.find('.nav-link').on('click', function() {
                    sidebar.removeClass('show');
                    overlay.removeClass('show');
                });
            }
        });

        // Atualizar badge de alertas pendentes (apenas para Admin/Administrativo)
        <?php if (isset($usuarioLogado) && ($usuarioLogado->tipo === 'admin' || $usuarioLogado->tipo === 'atendente')): ?>
        function atualizarBadgeAlertas() {
            $.ajax({
                url: '<?= base_url('alertas/contar-pendentes') ?>',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    const badge = $('#badgeAlertasPendentes');
                    if (data.count && data.count > 0) {
                        badge.text(data.count).show();
                    } else {
                        badge.hide();
                    }
                },
                error: function() {
                    console.log('Erro ao carregar contagem de alertas');
                }
            });
        }

        // Atualizar ao carregar a página
        $(document).ready(function() {
            atualizarBadgeAlertas();
            // Atualizar a cada 2 minutos
            setInterval(atualizarBadgeAlertas, 120000);
        });
        <?php endif; ?>
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
