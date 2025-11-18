<?= $this->extend('layouts/operacional') ?>

<?= $this->section('conteudo') ?>

<?php
$errors  = session('errors') ?? [];
$success = session('success');
$error   = session('error');
?>

<?php if ($success): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= esc($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= esc($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <form class="row gy-2 gx-2 align-items-end" method="get" action="<?= base_url('despesas') ?>">
      <div class="col-12 col-sm-4 col-lg-3">
        <label class="form-label form-label-sm">Buscar</label>
        <input type="text" name="q" value="<?= esc($f['q']) ?>" class="form-control form-control-sm" placeholder="descrição/categoria">
      </div>
      <div class="col-6 col-sm-4 col-lg-2">
        <label class="form-label form-label-sm">De</label>
        <input type="date" name="data_ini" value="<?= esc($f['data_ini']) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-6 col-sm-4 col-lg-2">
        <label class="form-label form-label-sm">Até</label>
        <input type="date" name="data_fim" value="<?= esc($f['data_fim']) ?>" class="form-control form-control-sm">
      </div>
      <div class="col-6 col-sm-4 col-lg-2">
        <label class="form-label form-label-sm">Categoria</label>
        <input type="text" name="categoria" value="<?= esc($f['categoria']) ?>" class="form-control form-control-sm" placeholder="ex.: Alimentação">
      </div>
      <div class="col-6 col-sm-4 col-lg-2">
        <label class="form-label form-label-sm">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="" <?= ($f['status']===''?'selected':'') ?>>Todos</option>
          <option value="1" <?= ($f['status']==='1'?'selected':'') ?>>Ativos</option>
          <option value="0" <?= ($f['status']==='0'?'selected':'') ?>>Inativos</option>
        </select>
      </div>
      <div class="col-12 col-sm-4 col-lg-1 d-grid">
        <button class="btn btn-sm btn-primary">Filtrar</button>
      </div>
    </form>
  </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-2">
  <div class="h5 mb-0">Total filtrado: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></div>
  <a href="<?= base_url('despesas/novo') ?>" class="btn btn-success">+ Nova Despesa</a>
</div>

<div class="card">
  <div class="card-body table-responsive">
    <table class="table table-compact align-middle">
      <thead>
        <tr>
          <th>Data</th>
          <th>Descrição</th>
          <th>Categoria</th>
          <th class="text-end">Valor (R$)</th>
          <th>Status</th>
          <th class="text-end">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($lista)): ?>
          <tr><td colspan="6" class="text-center text-muted">Sem resultados.</td></tr>
        <?php else: foreach ($lista as $row): ?>
          <tr>
            <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
            <td><?= esc($row['descricao']) ?></td>
            <td><?= esc($row['categoria'] ?? '—') ?></td>
            <td class="text-end"><?= number_format((float)$row['valor'], 2, ',', '.') ?></td>
            <td>
              <?php if ((int)$row['status'] === 1): ?>
                <span class="badge bg-success">Ativa</span>
              <?php else: ?>
                <span class="badge bg-secondary">Inativa</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <a href="<?= base_url('despesas/editar/'.$row['id']) ?>" class="btn btn-sm btn-primary">Editar</a>

              <form action="<?= base_url('despesas/excluir/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirmarExclusao(this)">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('page_js') ?>
<script>
  function confirmarExclusao(form) {
    return confirm('Tem certeza que deseja excluir esta despesa?');
  }
</script>
<?= $this->endSection() ?>
