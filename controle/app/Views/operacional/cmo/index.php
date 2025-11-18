<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
// --- Fallbacks e saneamento ---
$mesNome     = $mesNome     ?? '';
$ano         = $ano         ?? '';
$total       = $total       ?? 0;

// labels devem ser array simples
$labels      = array_values(($labels ?? []));

// Converta tudo para número (evita "5,20" virar string)
$cmoMesesRaw   = $cmoMeses   ?? [];
$cmoPrMesesRaw = $cmoPrMeses ?? [];

$cmoMesesSan   = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $cmoMesesRaw);
$cmoPrMesesSan = array_map(static fn($v) => is_numeric(str_replace(',', '.', (string)$v)) ? (float)str_replace(',', '.', (string)$v) : 0.0, $cmoPrMesesRaw);

$temCmo   = count($labels) && count($cmoMesesSan);
$temCmoPr = count($labels) && count($cmoPrMesesSan);

// Helper BRL para o card
$fmt = function($v){ return 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.'); };
?>

<div class="row g-3">
  <div class="col-12 col-md-6 col-xl-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="text-muted small">CMO - Folha (<?= esc($mesNome) ?>/<?= esc($ano) ?>)</div>
        <div class="display-6 fw-semibold mt-1"><?= $fmt($total) ?></div>
        <div class="text-muted mt-2">
          Somatório de despesas cadastradas com <strong>Tipo = Folha</strong> no mês atual.
        </div>
      </div>
    </div>
  </div>
</div>

<h3 class="mb-3 mt-4">CMO – Histórico (<?= esc($ano) ?>)</h3>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">CMO (Total Folha por mês)</h6>
        <?php if ($temCmo): ?>
          <div style="height:240px"><canvas id="chartCmo"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="card" style="height:310px;">
      <div class="card-body">
        <h6 class="card-title mb-1">CMO PR (por refeição)</h6>
        <?php if ($temCmoPr): ?>
          <div style="height:240px"><canvas id="chartCmoPr"></canvas></div>
        <?php else: ?>
          <div class="text-muted small mt-3">Sem dados para exibir.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Verifica se a seção scripts está sendo renderizada
  console.log('Seção <scripts> da view CMO carregada');

  // Dados vindos do PHP (numéricos)
  const labels     = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
  const cmoData    = <?= json_encode($cmoMesesSan, JSON_NUMERIC_CHECK) ?>;
  const cmoPrData  = <?= json_encode($cmoPrMesesSan, JSON_NUMERIC_CHECK) ?>;

  // Debug rápido no console
  console.table([{labels_len: labels.length, cmo_len: cmoData.length, cmoPr_len: cmoPrData.length}]);

  // Formatador BRL
  const BRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2 });

  function mountBar(id, data, label) {
    const el = document.getElementById(id);
    if (!el) return;
    const ctx = el.getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets: [{ label, data }] },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
          tooltip: { callbacks: { label: (ctx) => BRL.format(Number(ctx.parsed.y ?? 0)) } },
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, ticks: { callback: (v) => BRL.format(Number(v)) } }
        }
      }
    });
  }

  function mountLine(id, data, label) {
    const el = document.getElementById(id);
    if (!el) return;
    const ctx = el.getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: { labels, datasets: [{ label, data, tension: 0.3, fill: false }] },
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

  if (labels.length && cmoData.length)  mountBar('chartCmo',   cmoData,  'CMO (R$ por mês)');
  if (labels.length && cmoPrData.length) mountLine('chartCmoPr', cmoPrData, 'CMO PR (R$ por refeição)');
</script>
<?= $this->endSection(); ?>
