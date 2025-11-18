<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
$labelsJS   = json_encode($labels, JSON_UNESCAPED_UNICODE);

/* ===== Faturamento (total + por empresa) ===== */
$fatTotalJS = json_encode($faturamentoMeses, JSON_UNESCAPED_UNICODE);

// Datasets por empresa (faturamento)
$fatEmpDatasets = [];
$paletaEmpresas = [
    'rgba(54, 162, 235, 1)',
    'rgba(255, 99, 132, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(255, 159, 64, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(201, 203, 207, 1)',
    'rgba(0, 200, 83, 1)',
    'rgba(255, 205, 86, 1)',
];
$idx = 0;
foreach ($fatPorEmpresa as $empresa => $arrMeses) {
    $color = $paletaEmpresas[$idx % count($paletaEmpresas)];
    $idx++;
    $fatEmpDatasets[] = [
        'label' => $empresa,
        'data'  => array_values($arrMeses),
        'borderColor' => $color,
        'backgroundColor' => $color,
        'borderWidth' => 1.8,
        'tension' => 0.25,
        'pointRadius' => 1.5,
    ];
}

/* ===== Quantidade (VERSÃO ANTERIOR: apenas por serviço) ===== */
$qtdServDatasets = [];
$paletaServicos = [
    'rgba(99, 132, 255, .9)',
    'rgba(255, 159, 64, .9)',
    'rgba(54, 162, 235, .9)',
    'rgba(75, 192, 192, .9)',
    'rgba(255, 99, 132, .9)',
    'rgba(153, 102, 255, .9)',
    'rgba(201, 203, 207, .9)',
];
$idx = 0;
foreach ($qtdPorServico as $serv => $arrMeses) {
    $color = $paletaServicos[$idx % count($paletaServicos)];
    $idx++;
    $qtdServDatasets[] = [
        'label' => $serv,
        'data'  => array_values($arrMeses),
        'borderColor' => $color,
        'backgroundColor' => $color,
        'borderWidth' => 1.6,
        'tension' => 0.25,
        'pointRadius' => 2,
    ];
}
?>

<!-- Linha 1: cards gerais -->
<div class="row g-3 mb-3">
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-1">Refeições no mês (<?= str_pad($mes,2,'0',STR_PAD_LEFT) ?>/<?= $ano; ?>)</h5>
        <div class="display-6"><?= number_format($totMes, 0, ',', '.'); ?></div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-1">Refeições no ano (<?= $ano; ?>)</h5>
        <div class="display-6"><?= number_format($totAno, 0, ',', '.'); ?></div>
      </div>
    </div>
  </div>
</div>

<!-- Linha 2: cards por serviço -->
<div class="row g-3 mb-3">
  <div class="col-12 col-md-4">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title mb-1">Almoço</h6>
        <div class="d-flex justify-content-between">
          <div><small class="text-muted">Mês</small><div class="h4 mb-0"><?= number_format($sAlmMes, 0, ',', '.'); ?></div></div>
          <div><small class="text-muted">Ano</small><div class="h4 mb-0"><?= number_format($sAlmAno, 0, ',', '.'); ?></div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title mb-1">Jantar</h6>
        <div class="d-flex justify-content-between">
          <div><small class="text-muted">Mês</small><div class="h4 mb-0"><?= number_format($sJanMes, 0, ',', '.'); ?></div></div>
          <div><small class="text-muted">Ano</small><div class="h4 mb-0"><?= number_format($sJanAno, 0, ',', '.'); ?></div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title mb-1">Café da manhã</h6>
        <div class="d-flex justify-content-between">
          <div><small class="text-muted">Mês</small><div class="h4 mb-0"><?= number_format($sCafMes, 0, ',', '.'); ?></div></div>
          <div><small class="text-muted">Ano</small><div class="h4 mb-0"><?= number_format($sCafAno, 0, ',', '.'); ?></div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-12 col-md-4">
  <div class="card position-relative">
    <div class="card-body">
      <h6 class="card-title mb-1">CMO</h6>
      <div class="d-flex justify-content-between">
        <div>
          <small class="text-muted">Total (Mês)</small>
          <div class="h4 mb-0">R$ <?= number_format((float)$cmoTotal, 2, ',', '.') ?></div>
        </div>
        <div>
          <small class="text-muted">CMO/PR</small>
          <div class="h4 mb-0">R$ <?= number_format((float)$cmoPr, 2, ',', '.') ?></div>
        </div>
      </div>
      <div class="text-muted small mt-2">
        Base: Almoço+Jantar = <?= number_format((int)$cmoRefBase, 0, ',', '.') ?>
      </div>
      <!-- link invisível que torna o card clicável -->
      <a href="<?= base_url('operacional/cmo') ?>" class="stretched-link" aria-label="Ver histórico do CMO"></a>
    </div>
  </div>
</div>




<!-- Gráficos -->
<div class="row g-3">
  <!-- Faturamento (total + por empresa) -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Faturamento por mês (<?= $ano; ?>)</h5>
        <div class="chart-box"><canvas id="chartFaturamento"></canvas></div>
      </div>
    </div>
  </div>

  <!-- Quantidade por serviço (versão anterior) -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Quantidade por serviço por mês (<?= $ano; ?>)</h5>
        <div class="chart-box"><canvas id="chartQtdServicos"></canvas></div>
      </div>
    </div>
  </div>
</div>



<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
  const labels = <?= $labelsJS ?>;

  // ===== FATURAMENTO (total + por empresa) =====
  const fatTotal = <?= $fatTotalJS ?>;
  const fatEmpDS = <?= json_encode($fatEmpDatasets, JSON_UNESCAPED_UNICODE) ?>;

  const fatDatasets = [
    {
      label: 'Total (R$)',
      data: fatTotal,
      borderWidth: 3,
      tension: 0.2,
      pointRadius: 2
    },
    ...fatEmpDS
  ];

  const ctxFat = document.getElementById('chartFaturamento').getContext('2d');
  if (window._chartFat) window._chartFat.destroy();
  window._chartFat = new Chart(ctxFat, {
    type: 'line',
    data: { labels, datasets: fatDatasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'nearest', intersect: false },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR',{ minimumFractionDigits: 2 })
          }
        }
      },
      plugins: {
        legend: { position: 'top' },
        tooltip: {
          enabled: true,
          callbacks: {
            label: ctx => `${ctx.dataset.label}: R$ ${Number(ctx.parsed.y).toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
          }
        }
      }
    }
  });

  // ===== QUANTIDADE (por serviço – versão anterior) =====
  const dsServicos = <?= json_encode($qtdServDatasets, JSON_UNESCAPED_UNICODE) ?>;

  const ctxQtd = document.getElementById('chartQtdServicos').getContext('2d');
  if (window._chartQtd) window._chartQtd.destroy();
  window._chartQtd = new Chart(ctxQtd, {
    type: 'line',
    data: { labels, datasets: dsServicos },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'nearest', intersect: false },
      scales: { y: { beginAtZero: true } },
      plugins: {
        legend: { position: 'top' },
        tooltip: {
          enabled: true,
          callbacks: {
            label: ctx => `${ctx.dataset.label}: ${Number(ctx.parsed.y).toLocaleString('pt-BR')}`
          }
        }
      }
    }
  });
</script>

<?= $this->endSection(); ?>
