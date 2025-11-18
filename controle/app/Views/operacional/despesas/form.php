<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
// -------------------------------------------------------
// Modo (novo x edição) e defaults
// -------------------------------------------------------
$action = $action ?? base_url('operacional/despesas/salvar'); // controller define ao editar
$isEdit = !empty($item);                          // $item é objeto: id,tipo,descricao,mes,ano,valor
$tipos  = $tipos  ?? ['Folha','Verduras','Carne','Equipamentos'];

// Só para exibir valor formatado quando vier do banco
$fmtMoney = function($v) {
  if ($v === null || $v === '') return '';
  return number_format((float)$v, 2, ',', '.');
};

// Função para retornar nome do mês
$nomeMes = function($num) {
  $meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
  return $meses[(int)$num] ?? $num;
};
?>

<h3 class="mb-3"><?= $isEdit ? 'Editar Despesa' : 'Cadastro de Despesas' ?></h3>

<?php if (session()->getFlashdata('msg')): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('msg'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php elseif (session()->getFlashdata('msg_error')): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('msg_error'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-warning">
    <?php foreach ((array)$errors as $e): ?>
      <div>• <?= esc($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<form action="<?= esc($action) ?>" method="post" class="row g-3 mb-4">
  <?= function_exists('csrf_field') ? csrf_field() : '' ?>

  <div class="col-md-4">
    <label class="form-label">Tipo</label>
    <?php
      $tipoDefault = $isEdit ? ($item->tipo ?? '') : '';
      $tipoVal = old('tipo', $tipoDefault);
    ?>
    <select name="tipo" class="form-select" required>
      <option value="">Selecione...</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= esc($t) ?>" <?= ($tipoVal === $t ? 'selected' : '') ?>><?= esc($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Mês</label>
    <?php
      $meses = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
      ];
      $mesSelecionado = old('mes', $isEdit ? str_pad((int)($item->mes ?? date('m')), 2, '0', STR_PAD_LEFT) : date('m'));
    ?>
    <select name="mes" class="form-select" required>
      <?php foreach ($meses as $num => $nome): ?>
        <option value="<?= $num ?>" <?= $mesSelecionado === $num ? 'selected' : '' ?>><?= $nome ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-2">
    <label class="form-label">Ano</label>
    <input
      type="number"
      name="ano"
      class="form-control"
      required
      value="<?= old('ano', $isEdit ? ($item->ano ?? date('Y')) : date('Y')) ?>">
  </div>

  <div class="col-md-2">
    <label class="form-label">Valor (R$)</label>
    <input
      type="text"
      name="valor"
      class="form-control"
      inputmode="decimal"
      required
      value="<?= old('valor', $isEdit ? $fmtMoney($item->valor ?? '') : '') ?>">
  </div>

  <div class="col-12 d-flex gap-2">
    <button class="btn btn-primary"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
    <?php if ($isEdit): ?>
      <a href="<?= base_url('operacional/despesas') ?>" class="btn btn-outline-secondary">Cancelar</a>
    <?php endif; ?>
  </div>
</form>

<h5 class="d-flex align-items-center justify-content-between">
  <span>Últimos lançamentos</span>
  <a href="<?= base_url('operacional/despesas') ?>" class="btn btn-sm btn-light">Novo</a>
</h5>

<!-- Filtros -->
<form method="get" action="<?= base_url('operacional/despesas') ?>" class="row g-3 mb-3">
  <div class="col-md-3">
    <label class="form-label">Tipo</label>
    <select name="filtro_tipo" class="form-select">
      <option value="">Todos</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= esc($t) ?>" <?= (($filtroTipo ?? '') === $t ? 'selected' : '') ?>><?= esc($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Mês</label>
    <select name="filtro_mes" class="form-select">
      <option value="">Todos</option>
      <?php
        $mesesFiltro = [
          '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
          '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
          '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
          '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        foreach ($mesesFiltro as $num => $nome):
      ?>
        <option value="<?= $num ?>" <?= (($filtroMes ?? '') === $num ? 'selected' : '') ?>><?= $nome ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-2">
    <label class="form-label">Ano</label>
    <select name="filtro_ano" class="form-select">
      <option value="">Todos</option>
      <?php
        $anoAtual = (int)date('Y');
        for ($a = $anoAtual; $a >= $anoAtual - 3; $a--):
      ?>
        <option value="<?= $a ?>" <?= (($filtroAno ?? '') == $a ? 'selected' : '') ?>><?= $a ?></option>
      <?php endfor; ?>
    </select>
  </div>

  <div class="col-md-2 d-flex align-items-end gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-filter"></i> Filtrar
    </button>
    <a href="<?= base_url('operacional/despesas') ?>" class="btn btn-outline-secondary">
      <i class="fas fa-times"></i>
    </a>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>Tipo</th>
        <th>Mês/Ano</th>
        <th class="text-end">Valor (R$)</th>
        <th class="text-end" style="width: 160px;">Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($ultimas)): ?>
        <tr><td colspan="4" class="text-center text-muted">Sem lançamentos.</td></tr>
      <?php else: foreach ($ultimas as $u): ?>
        <tr>
          <td><?= esc($u->tipo) ?></td>
          <td><?= $nomeMes($u->mes) ?>/<?= $u->ano ?></td>
          <td class="text-end"><?= number_format((float)$u->valor, 2, ',', '.') ?></td>
          <td class="text-end">
            <div class="btn-group" role="group">
              <a href="<?= base_url('operacional/despesas/editar/'.$u->id) ?>" class="btn btn-sm btn-primary" title="Editar">
                <i class="fas fa-edit"></i>
              </a>
              <form action="<?= base_url('operacional/despesas/excluir/'.$u->id) ?>" method="post" class="d-inline-block" onsubmit="return confirmarExclusao()">
                <?= function_exists('csrf_field') ? csrf_field() : '' ?>
                <button class="btn btn-sm btn-danger" title="Excluir">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
  function confirmarExclusao() {
    return confirm('Tem certeza que deseja excluir esta despesa?');
  }
</script>
<?= $this->endSection(); ?>
