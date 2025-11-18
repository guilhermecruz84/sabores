<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title><?= esc($title ?? 'Dashboard'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" href="<?= base_url('assets/appstack/img/favicon.ico'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/appstack/css/app.css'); ?>">

  <style>
    /* Sidebar mais fina */
    .sidebar { width: 200px; }
    .sidebar .sidebar-content { width: 200px; }
    .sidebar .sidebar-brand { text-align:center; padding:16px 8px; }
    .sidebar .sidebar-brand img { max-width: 110px; height:auto; display:block; margin:0 auto; }

    /* Itens do menu mais compactos */
    .sidebar .sidebar-nav .sidebar-item > a { padding: 6px 12px; gap: 8px; font-size: 0.9rem; }
    .sidebar .sidebar-header { font-size: .72rem; letter-spacing:.07em; opacity:.7; padding: 8px 12px 4px; }

    /* Menu colapsado */
    .sidebar.collapsed { width: 64px; }
    .sidebar.collapsed .sidebar-content { width: 64px; }
    .sidebar.collapsed .sidebar-brand img { max-width: 28px; }
    .sidebar.collapsed .sidebar-nav .sidebar-item > a span { display:none; }

    /* Ícones menores */
    .sidebar .sidebar-nav i[data-feather] {
      width: 16px !important;
      height: 16px !important;
    }

    /* Chart container com altura fixa */
    .chart-box {
      position: relative;
      height: 320px; /* ajuste conforme preferir */
    }
  </style>
</head>
<body>
<div class="wrapper">

  <!-- SIDEBAR -->
  <nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">

      <!-- LOGO -->
      <a class="sidebar-brand" href="<?= base_url('/'); ?>">
        <img src="<?= base_url('assets/appstack/img/logo-sm.png'); ?>" alt="Logo">
      </a>

      <ul class="sidebar-nav">
        <li class="sidebar-header">Menu</li>

        <!-- Dashboard -->
        <li class="sidebar-item <?= (strpos(uri_string(), 'dashboard') === 0 || uri_string() === '') ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/dashboard'); ?>">
            <i data-feather="bar-chart-2"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li class="sidebar-header">Cadastro</li>
        <li class="sidebar-item <?= (uri_string() === 'refeicoes' || uri_string() === 'refeicoes/') ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/refeicoes'); ?>">
            <i data-feather="clipboard"></i>
            <span>Serviços</span>
          </a>
        </li>

        <li class="sidebar-item <?= (strpos(uri_string(), 'refeicoes/listar') === 0) ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/refeicoes/listar'); ?>">
            <i data-feather="list"></i>
            <span>Lançamentos</span>
          </a>
        </li>

        <li class="sidebar-header">Financeiro</li>
        <li class="sidebar-item <?= (strpos(uri_string(), 'despesas') === 0) ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/despesas'); ?>">
            <i data-feather="dollar-sign"></i>
            <span>Despesas</span>
          </a>
        </li>

        <!-- Fiscal -->
        <li class="sidebar-header">Fiscal</li>
        <li class="sidebar-item <?= (strpos(uri_string(), 'nfe') === 0) ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/nfe'); ?>">
            <i data-feather="file-plus"></i>
            <span>Importar XML NF-e</span>
          </a>
        </li>

        <!-- Qualidade -->
        <li class="sidebar-header">Qualidade</li>
        <li class="sidebar-item <?= (strpos(uri_string(), 'ocorrencias') === 0) ? 'active' : ''; ?>">
          <a class="sidebar-link" href="<?= base_url('operacional/ocorrencias'); ?>">
            <i data-feather="file-text"></i>
            <span>Ocorrências</span>
          </a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /SIDEBAR -->

  <div class="main">
    <!-- TOPBAR -->
    <nav class="navbar navbar-expand navbar-dark navbar-bg">
      <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
      </a>
      <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align ms-auto">
          <li class="nav-item"><span class="nav-link text-light">Olá, Guilherme</span></li>
        </ul>
      </div>
    </nav>

    <!-- CONTEÚDO -->
    <main class="content">
      <div class="container-fluid p-0">
        <?= $this->renderSection('conteudo'); ?>
      </div>
    </main>

    <footer class="footer">
      <div class="container-fluid">
        <div class="row text-muted">
          <div class="col-12 text-end">
            <p class="mb-0">© <?= date('Y'); ?> Sabores em Movimento</p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<!-- JS -->
<script src="<?= base_url('assets/appstack/js/app.js'); ?>"></script>
<script src="<?= base_url('assets/appstack/js/settings.js'); ?>"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script>feather.replace();</script>

</body>
<?= $this->renderSection('scripts'); ?>
</html>
