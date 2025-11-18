<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
// --- Fallbacks e saneamento ---
$mesNome       = $mesNome       ?? '';
$ano           = $ano           ?? '';
$total         = $total         ?? 0;
$baseMes       = $baseMes       ?? 0;
$cmvPr         = $cmvPr         ?? 0;
$verdurasTotal = $verdurasTotal ?? 0;
$verdurasPr    = $verdurasPr    ?? 0;
$carneTotal    = $carneTotal    ?? 0;
$carnePr       = $carnePr       ?? 0;

// labels devem ser array simples
$labels      = array_values(($labels ?? []));

// Converta tudo para número
$cmvMesesRaw       = $cmvMeses       ?? [];
$cmvPrMesesRaw     = $cmvPrMeses     ?? [];
$baseMesesRaw      = $baseMeses      ?? [];
$verdurasMesesRaw  = $verdurasMeses  ?? [];
$verdurasPrMesesRaw = $verdurasPrMeses ?? [];
$carneMesesRaw     = $carneMeses     ?? [];
$carnePrMesesRaw   = $carnePrMeses   ?? [];

$cmvMesesSan       = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $cmvMesesRaw);
$cmvPrMesesSan     = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $cmvPrMesesRaw);
$baseMesesSan      = array_map(static fn($v) => (int)$v, $baseMesesRaw);
$verdurasMesesSan  = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $verdurasMesesRaw);
$verdurasPrMesesSan = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $verdurasPrMesesRaw);
$carneMesesSan     = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $carneMesesRaw);
$carnePrMesesSan   = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $carnePrMesesRaw);

$temCmv        = count($labels) && count($cmvMesesSan);
$temCmvPr      = count($labels) && count($cmvPrMesesSan);
$temBase       = count($labels) && count($baseMesesSan);
$temVerduras   = count($labels) && count($verdurasMesesSan);
$temVerdurasPr = count($labels) && count($verdurasPrMesesSan);
$temCarne      = count($labels) && count($carneMesesSan);
$temCarnePr    = count($labels) && count($carnePrMesesSan);

// Helper BRL para o card
$fmt = function($v){ return 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.'); };
?>

<div class="row g-3">
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm border-left-primary">
      <div class="card-body">
        <div class="text-muted small">CMV - Insumos (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= $fmt($total) ?></div>
        <div class="text-muted mt-2">
          <small>Total de despesas com <strong>Tipo = Insumos</strong> no mês.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm border-left-info">
      <div class="card-body">
        <div class="text-muted small">Base - Refeições (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= number_format($baseMes, 0, ',', '.') ?></div>
        <div class="text-muted mt-2">
          <small>Total de <strong>Almoço + Jantar</strong> no mês.</small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm border-left-success">
      <div class="card-body">
        <div class="text-muted small">CMV PR - Por Refeição (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= $fmt($cmvPr) ?></div>
        <div class="text-muted mt-2">
          <small>Insumos ÷ Refeições = <strong>Custo por refeição</strong></small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm border-left-warning">
      <div class="card-body">
        <div class="text-muted small">Verduras PR - Por Refeição (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= $fmt($verdurasPr) ?></div>
        <div class="text-muted mt-2">
          <small>Verduras ÷ Refeições = <strong>Custo por refeição</strong></small>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm border-left-danger">
      <div class="card-body">
        <div class="text-muted small">Proteínas PR - Por Refeição (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= $fmt($carnePr) ?></div>
        <div class="text-muted mt-2">
          <small>Carne ÷ Refeições = <strong>Custo por refeição</strong></small>
        </div>
      </div>
    </div>
  </div>
</div>

<h3 class="mb-3 mt-4">CMV – Histórico (<?= esc($ano) ?>)</h3>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">CMV (Total Insumos por mês)</h6>
        <?php if ($temCmv): ?>
          <div style="height:240px"><canvas id="chartCmv"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">Base (Almoço + Jantar)</h6>
        <?php if ($temBase): ?>
          <div style="height:240px"><canvas id="chartBase"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">CMV PR (por refeição)</h6>
        <?php if ($temCmvPr): ?>
          <div style="height:240px"><canvas id="chartCmvPr"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">Verduras PR (por refeição)</h6>
        <?php if ($temVerdurasPr): ?>
          <div style="height:240px"><canvas id="chartVerdurasPr"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">Proteínas PR (por refeição)</h6>
        <?php if ($temCarnePr): ?>
          <div style="height:240px"><canvas id="chartCarnePr"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df; }
.border-left-success { border-left: 4px solid #1cc88a; }
.border-left-info { border-left: 4px solid #36b9cc; }
.border-left-warning { border-left: 4px solid #f6c23e; }
.border-left-danger { border-left: 4px solid #e74a3b; }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  console.log('Seção <scripts> da view CMV carregada');

  // Dados vindos do PHP (numéricos)
  const labels         = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
  const cmvData        = <?= json_encode($cmvMesesSan, JSON_NUMERIC_CHECK) ?>;
  const cmvPrData      = <?= json_encode($cmvPrMesesSan, JSON_NUMERIC_CHECK) ?>;
  const baseData       = <?= json_encode($baseMesesSan, JSON_NUMERIC_CHECK) ?>;
  const verdurasData   = <?= json_encode($verdurasMesesSan, JSON_NUMERIC_CHECK) ?>;
  const verdurasPrData = <?= json_encode($verdurasPrMesesSan, JSON_NUMERIC_CHECK) ?>;
  const carneData      = <?= json_encode($carneMesesSan, JSON_NUMERIC_CHECK) ?>;
  const carnePrData    = <?= json_encode($carnePrMesesSan, JSON_NUMERIC_CHECK) ?>;

  console.table([{labels_len: labels.length, cmv_len: cmvData.length, cmvPr_len: cmvPrData.length, base_len: baseData.length, verduras_len: verdurasData.length, carne_len: carneData.length}]);

  // Formatador BRL
  const BRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2 });

  function mountBar(id, data, label, color) {
    const el = document.getElementById(id);
    if (!el) return;
    const ctx = el.getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label,
          data,
          backgroundColor: color || '#4e73df'
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          tooltip: {
            callbacks: {
              label: (ctx) => (label.includes('Refeição') || label.includes('Refeições')) ? ctx.parsed.y.toLocaleString('pt-BR') : BRL.format(Number(ctx.parsed.y ?? 0))
            }
          },
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (v) => (label.includes('Refeição') || label.includes('Refeições')) ? v.toLocaleString('pt-BR') : BRL.format(Number(v))
            }
          }
        }
      }
    });
  }

  function mountLine(id, data, label, color) {
    const el = document.getElementById(id);
    if (!el) return;
    const ctx = el.getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label,
          data,
          tension: 0.3,
          fill: false,
          borderColor: color || '#1cc88a',
          backgroundColor: color || '#1cc88a'
        }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          tooltip: { callbacks: { label: (ctx) => BRL.format(Number(ctx.parsed.y ?? 0)) } }
        },
        scales: {
          y: { beginAtZero: true, ticks: { callback: (v) => BRL.format(Number(v)) } }
        }
      }
    });
  }

  if (labels.length && cmvData.length)        mountBar('chartCmv',        cmvData,        'CMV (R$ por mês)', '#4e73df');
  if (labels.length && baseData.length)       mountBar('chartBase',       baseData,       'Refeições', '#36b9cc');
  if (labels.length && cmvPrData.length)      mountLine('chartCmvPr',     cmvPrData,      'CMV PR (R$ por refeição)', '#1cc88a');
  if (labels.length && verdurasPrData.length) mountLine('chartVerdurasPr', verdurasPrData, 'Verduras PR (R$ por refeição)', '#f6c23e');
  if (labels.length && carnePrData.length)    mountLine('chartCarnePr',   carnePrData,    'Proteínas PR (R$ por refeição)', '#e74a3b');
</script>
<?= $this->endSection(); ?>
