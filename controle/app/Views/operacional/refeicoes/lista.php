<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
$empresas = isset($empresas) ? $empresas : [];
sort($empresas, SORT_NATURAL | SORT_FLAG_CASE);

$f_empresa = $f_empresa ?? '';
$f_mes     = $f_mes ?? '';
$f_ano     = $f_ano ?? '';
$per_page  = (int)($per_page ?? 20);
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 m-0"><?= esc($title ?? 'Lançamentos'); ?></h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('operacional/refeicoes'); ?>" class="btn btn-primary">Novo lançamento</a>
  </div>
</div>

<?php if (session('msg')): ?>
  <div class="alert alert-success"><?= esc(session('msg')); ?></div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2 align-items-end" method="get" action="<?= base_url('operacional/refeicoes/listar'); ?>">
      <div class="col-12 col-md-4">
        <label class="form-label form-label-sm">Empresa</label>
        <select name="f_empresa" class="form-select form-select-sm">
          <option value="">Todas</option>
          <?php foreach ($empresas as $e): ?>
            <option value="<?= esc($e); ?>" <?= $f_empresa === $e ? 'selected' : ''; ?>><?= esc($e); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Mês</label>
        <input type="number" min="1" max="12" name="f_mes" value="<?= esc($f_mes); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Ano</label>
        <input type="number" min="2000" max="2100" name="f_ano" value="<?= esc($f_ano); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Por página</label>
        <select name="per_page" class="form-select form-select-sm">
          <?php foreach ([10,20,50,100,200] as $n): ?>
            <option value="<?= $n; ?>" <?= $per_page===$n ? 'selected' : ''; ?>><?= $n; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-sm btn-outline-primary" type="submit">Filtrar</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-compact table-hover table-nowrap align-middle mb-0">
        <thead>
          <tr>
            <th style="width:70px;">#</th>
            <th>Empresa</th>
            <th>Serviço</th>
            <th>Mês/Ano</th>
            <th class="text-end">Qtd</th>
            <th class="text-end">Valor</th>
            <th style="width:180px;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($registros)): ?>
            <?php foreach ($registros as $r): ?>
              <tr>
                <td><?= (int)$r['id']; ?></td>
                <td class="text-truncate" style="max-width:240px;"><?= esc($r['empresa']); ?></td>
                <td><?= esc($r['servico']); ?></td>
                <td><?= str_pad((int)$r['mes'], 2, '0', STR_PAD_LEFT) . '/' . (int)$r['ano']; ?></td>
                <td class="text-end"><?= number_format((float)$r['quantidade'], 0, ',', '.'); ?></td>
                <td class="text-end">R$ <?= number_format((float)$r['valor'], 2, ',', '.'); ?></td>
                <td>
                  <a href="<?= base_url('operacional/refeicoes/editar/'.(int)$r['id']); ?>" class="btn btn-sm btn-outline-secondary">Editar</a>

                  <form action="<?= base_url('operacional/refeicoes/excluir/'.(int)$r['id']); ?>"
                        method="post" class="d-inline"
                        onsubmit="return confirm('Excluir este lançamento?');">
                    <?= csrf_field(); ?>
                    <!-- preserva filtros ao excluir -->
                    <input type="hidden" name="f_empresa" value="<?= esc($f_empresa); ?>">
                    <input type="hidden" name="f_mes" value="<?= esc($f_mes); ?>">
                    <input type="hidden" name="f_ano" value="<?= esc($f_ano); ?>">
                    <input type="hidden" name="per_page" value="<?= esc($per_page); ?>">
                    <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center text-muted p-4">Nenhum registro encontrado.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if (isset($pager)): ?>
    <div class="card-footer">
      <?= $pager->links('refeicoes', 'default_full'); ?>
    </div>
  <?php endif; ?>
</div>

<?= $this->endSection(); ?>
