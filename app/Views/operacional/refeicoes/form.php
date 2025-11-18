<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo'); ?>

<?php
$errors   = session('errors') ?? [];
$empresas = isset($empresas) && is_array($empresas) ? $empresas : [];
$servicos = isset($servicos) && is_array($servicos) ? $servicos : [];
sort($empresas, SORT_NATURAL | SORT_FLAG_CASE);
sort($servicos, SORT_NATURAL | SORT_FLAG_CASE);

$isEdicao = !empty($item);
$padraoMes = old('mes', $isEdicao ? (int)$item['mes'] : date('n'));
$padraoAno = old('ano', $isEdicao ? (int)$item['ano'] : date('Y'));

$action = $isEdicao
  ? base_url('operacional/refeicoes/atualizar/'.(int)$item['id'])
  : base_url('operacional/refeicoes/salvar');

$empresaSelecionada = old('empresa', $isEdicao ? $item['empresa'] : '');
$servicoSelecionado = old('servico', $isEdicao ? $item['servico'] : '');
$qtdSelecionada     = old('quantidade', $isEdicao ? $item['quantidade'] : '');
$valorSelecionado   = old('valor', $isEdicao ? number_format((float)$item['valor'], 2, ',', '.') : '');
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h1 class="h3 m-0"><?= esc($title ?? ($isEdicao?'Editar Lançamento':'Lançar Refeições do Mês')); ?></h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('operacional/refeicoes'); ?>" class="btn btn-outline-secondary">Novo</a>
    <a href="<?= base_url('operacional/refeicoes/listar'); ?>" class="btn btn-outline-primary">Ver lançamentos</a>
  </div>
</div>

<?php if (session('msg')): ?>
  <div class="alert alert-success"><?= esc(session('msg')); ?></div>
<?php elseif (session('msg_error')): ?>
  <div class="alert alert-danger"><?= esc(session('msg_error')); ?></div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-body">
        <form method="post" action="<?= $action; ?>">
          <?= csrf_field(); ?>

          <!-- EMPRESA -->
          <div class="mb-3">
            <label class="form-label">Empresa *</label>
            <select name="empresa" class="form-select <?= isset($errors['empresa'])?'is-invalid':''; ?>">
              <option value="">Selecione...</option>
              <?php foreach ($empresas as $e): ?>
                <option value="<?= esc($e); ?>" <?= $empresaSelecionada === $e ? 'selected' : ''; ?>>
                  <?= esc($e); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['empresa'])): ?><div class="invalid-feedback"><?= esc($errors['empresa']); ?></div><?php endif; ?>
          </div>

          <div class="row g-3">
            <div class="col-6 col-md-4">
              <label class="form-label">Mês *</label>
              <input type="number" min="1" max="12" name="mes" value="<?= esc($padraoMes); ?>" class="form-control <?= isset($errors['mes'])?'is-invalid':''; ?>">
              <?php if(isset($errors['mes'])): ?><div class="invalid-feedback"><?= esc($errors['mes']); ?></div><?php endif; ?>
            </div>

            <div class="col-6 col-md-4">
              <label class="form-label">Ano *</label>
              <input type="number" min="2000" max="2100" name="ano" value="<?= esc($padraoAno); ?>" class="form-control <?= isset($errors['ano'])?'is-invalid':''; ?>">
              <?php if(isset($errors['ano'])): ?><div class="invalid-feedback"><?= esc($errors['ano']); ?></div><?php endif; ?>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label">Serviço *</label>
              <select name="servico" class="form-select <?= isset($errors['servico'])?'is-invalid':''; ?>">
                <option value="">Selecione...</option>
                <?php foreach ($servicos as $s): ?>
                  <option value="<?= esc($s); ?>" <?= $servicoSelecionado === $s ? 'selected' : ''; ?>>
                    <?= esc($s); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <?php if(isset($errors['servico'])): ?><div class="invalid-feedback"><?= esc($errors['servico']); ?></div><?php endif; ?>
            </div>
          </div>

          <div class="row g-3 mt-1">
            <div class="col-12 col-md-6">
              <label class="form-label">Quantidade *</label>
              <input type="number" min="0" step="1" name="quantidade" value="<?= esc($qtdSelecionada); ?>" class="form-control <?= isset($errors['quantidade'])?'is-invalid':''; ?>">
              <?php if(isset($errors['quantidade'])): ?><div class="invalid-feedback"><?= esc($errors['quantidade']); ?></div><?php endif; ?>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Valor *</label>
              <div class="input-group">
                <span class="input-group-text">R$</span>
                <input type="text" inputmode="decimal" name="valor" value="<?= esc($valorSelecionado); ?>" class="form-control <?= isset($errors['valor'])?'is-invalid':''; ?>" placeholder="0,00">
                <?php if(isset($errors['valor'])): ?><div class="invalid-feedback"><?= esc($errors['valor']); ?></div><?php endif; ?>
              </div>
              <div class="form-text">Use vírgula para decimais (ex.: 17,50).</div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit"><?= $isEdicao ? 'Salvar alterações' : 'Salvar'; ?></button>
            <a class="btn btn-outline-secondary" href="<?= base_url('operacional/refeicoes/listar'); ?>">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if (!$isEdicao): ?>
  <!-- Últimos lançamentos (somente no modo criar) -->
  <div class="col-12 col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-3">Últimos Lançamentos</h5>
        <div class="table-responsive">
          <table class="table table-striped table-hover table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Empresa</th>
                <th>Serviço</th>
                <th>Mês/Ano</th>
                <th class="text-end">Qtd</th>
                <th class="text-end">Valor</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($ultimos)): ?>
                <?php foreach ($ultimos as $r): ?>
                  <tr>
                    <td><?= esc($r['empresa']); ?></td>
                    <td><?= esc($r['servico']); ?></td>
                    <td><?= str_pad((int)$r['mes'], 2, '0', STR_PAD_LEFT) . '/' . (int)$r['ano']; ?></td>
                    <td class="text-end"><?= number_format((float)$r['quantidade'], 0, ',', '.'); ?></td>
                    <td class="text-end">R$ <?= number_format((float)$r['valor'], 2, ',', '.'); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted p-4">Sem registros ainda.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="text-muted small mt-2">* Mostrando os 12 lançamentos mais recentes.</div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?= $this->endSection(); ?>
