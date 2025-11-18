<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Operacional - Sabores') ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <!-- AppStack CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/appstack/css/app.css'); ?>">

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
            width: 260px;
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

        .sidebar-header {
            padding: 15px 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 0.05em;
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

        /* Chart container */
        .chart-box {
            position: relative;
            height: 320px;
        }

        .content-wrapper {
            margin-left: 260px;
            padding: 15px;
            min-height: calc(100vh - 56px);
        }

        /* Responsivo */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -260px;
                z-index: 1000;
                transition: left 0.3s;
            }

            .sidebar.show {
                left: 0;
            }

            .content-wrapper {
                margin-left: 0;
            }
        }

        /* Cards de estatísticas melhorados */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-left: 5px solid var(--primary-color);
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stats-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        .stats-card .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Form controls melhorados */
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.15);
        }

        /* Tables responsivas */
        .table-responsive {
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }

        .table tbody tr {
            transition: background-color 0.2s;
        }

        .table tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.05);
        }

        /* Tabelas mais compactas */
        .table-compact {
            font-size: 0.875rem;
        }

        .table-compact th,
        .table-compact td {
            padding: 8px 10px;
            vertical-align: middle;
        }

        .table-compact .btn {
            padding: 4px 10px;
            font-size: 0.8rem;
        }

        .table-compact .badge {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        /* Colunas com largura fixa */
        .table .col-id { width: 60px; }
        .table .col-date { width: 140px; }
        .table .col-number { width: 100px; }
        .table .col-actions { width: 160px; }
        .table .col-status { width: 120px; }

        /* Truncar texto longo */
        .text-truncate-custom {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        /* Melhorias no card */
        .card {
            margin-bottom: 15px;
        }

        .card-body {
            padding: 0.875rem;
        }

        /* Form controls menores */
        .form-select-sm, .form-control-sm {
            padding: 4px 8px;
            font-size: 0.875rem;
        }

        .form-label-sm {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        /* Container mais estreito para melhor aproveitamento */
        .content-wrapper .container-fluid {
            max-width: 100%;
            padding-right: 0;
            padding-left: 0;
        }

        /* Remove margens extras das rows */
        .content-wrapper .row {
            margin-right: 0;
            margin-left: 0;
        }

        .content-wrapper .row > * {
            padding-right: 0;
            padding-left: 0;
        }

        /* Scroll horizontal em tabelas grandes */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (max-width: 1200px) {
            .table-compact {
                font-size: 0.8rem;
            }

            .text-truncate-custom {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('operacional/dashboard') ?>">
                <i class="fas fa-utensils me-2"></i>
                Sabores - Operacional
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
                                <span class="badge bg-light text-dark ms-1">Admin</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>"><i class="fas fa-arrow-left me-2"></i>Sistema Principal</a></li>
                                <li><hr class="dropdown-divider"></li>
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

    <div class="d-flex">
        <!-- Sidebar -->
        <?php if (isset($usuarioLogado) && $usuarioLogado->tipo === 'admin'): ?>
        <nav class="sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="sidebar-header">Menu Operacional</li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/dashboard') !== false || uri_string() === 'operacional') ? 'active' : '' ?>"
                           href="<?= base_url('operacional/dashboard') ?>">
                            <i class="fas fa-chart-bar"></i>
                            Dashboard
                        </a>
                    </li>

                    <li class="sidebar-header">Cadastro</li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/refeicoes') !== false && strpos(uri_string(), 'listar') === false) ? 'active' : '' ?>"
                           href="<?= base_url('operacional/refeicoes') ?>">
                            <i class="fas fa-clipboard"></i>
                            Serviços
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/refeicoes/listar') !== false) ? 'active' : '' ?>"
                           href="<?= base_url('operacional/refeicoes/listar') ?>">
                            <i class="fas fa-list"></i>
                            Lançamentos
                        </a>
                    </li>

                    <li class="sidebar-header">Financeiro</li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/despesas') !== false) ? 'active' : '' ?>"
                           href="<?= base_url('operacional/despesas') ?>">
                            <i class="fas fa-dollar-sign"></i>
                            Despesas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/cmo') !== false) ? 'active' : '' ?>"
                           href="<?= base_url('operacional/cmo') ?>">
                            <i class="fas fa-calculator"></i>
                            CMO
                        </a>
                    </li>

                    <li class="sidebar-header">Fiscal</li>

                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(uri_string(), 'operacional/nfe') !== false) ? 'active' : '' ?>"
                           href="<?= base_url('operacional/nfe') ?>">
                            <i class="fas fa-file-invoice"></i>
                            Importar XML NF-e
                        </a>
                    </li>

                    <li class="sidebar-header">Sistema</li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('dashboard') ?>">
                            <i class="fas fa-arrow-left"></i>
                            Voltar ao Principal
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="content-wrapper flex-fill">
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
            <?= $this->renderSection('conteudo') ?>
        </main>
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

    <!-- AppStack JS -->
    <script src="<?= base_url('assets/appstack/js/app.js'); ?>"></script>

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    <script>feather.replace();</script>

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
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
