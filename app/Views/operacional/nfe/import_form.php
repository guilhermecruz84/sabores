<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo') ?>

<?php
function dtBR($s) {
    if (!$s) return '';
    $dt = \DateTime::createFromFormat('Y-m-d H:i:s', (string)$s);
    if (!$dt) {
        try { $dt = new \DateTime((string)$s); } catch (\Throwable $e) { return (string)$s; }
    }
    return $dt->format('d/m/Y H:i');
}
function competenciaBR($comp) {
    if (preg_match('/^(\d{4})-(\d{2})$/', (string)$comp, $m)) {
        return "{$m[2]}-{$m[1]}"; // MM-YYYY
    }
    return (string)$comp;
}
function badgeStatus($status, $id) {
    $status = strtoupper(trim((string)$status));
    // Se NÃO finalizado, o "status" vira botão/link pra revisão
    if (in_array($status, ['PENDENTE', 'PARCIAL'], true)) {
        $class = $status === 'PARCIAL'
            ? 'btn btn-sm btn-warning text-dark px-3 py-2 text-nowrap'
            : 'btn btn-sm btn-secondary px-3 py-2 text-nowrap';
        $url = base_url('operacional/nfe/review/'.(int)$id);
        return '<a href="'.$url.'" class="'.$class.'" title="Revisar e finalizar lote #'.$id.'">'.$status.'</a>';
    }
    // FINALIZADO (ou outro) fica badge estático
    $class = $status === 'FINALIZADO'
        ? 'badge bg-success px-3 py-2'
        : 'badge bg-light text-dark px-3 py-2';
    return '<span class="'.$class.'">'.$status.'</span>';
}
?>

<div class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title mb-0"><?= esc($title ?? 'Importar NF-e (XML)') ?></h5>
      </div>
      <div class="card-body">
        <?php $session = session(); ?>
        <?php if ($session->getFlashdata('erro')): ?>
          <div class="alert alert-danger"><?= esc($session->getFlashdata('erro')) ?></div>
        <?php endif; ?>
        <?php if ($session->getFlashdata('ok')): ?>
          <div class="alert alert-success"><?= esc($session->getFlashdata('ok')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('operacional/nfe/upload') ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Arquivos XML</label>
            <input type="file" class="form-control" name="xmls[]" accept=".xml" multiple required>
            <div class="form-text">Selecione um ou mais arquivos .xml de NF-e/NFC-e.</div>
          </div>
          <button type="submit" class="btn btn-primary">
            <i data-feather="upload-cloud" class="me-1"></i> Importar
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Últimos lotes importados</h5>
        <a href="<?= base_url('nfe') ?>" class="btn btn-sm btn-outline-secondary text-nowrap">
          <i data-feather="refresh-ccw" class="me-1"></i> Atualizar
        </a>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-compact table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="text-nowrap" style="width:50px">#</th>
                <th class="text-nowrap" style="width:130px">Criado em</th>
                <th class="text-nowrap text-center" style="width:60px">XMLs</th>
                <th class="text-nowrap text-center" style="width:60px">Itens</th>
                <th class="text-nowrap" style="width:100px">Competência</th>
                <th style="width:25%">Empresa</th>
                <th style="width:20%">Serviço</th>
                <th class="text-nowrap" style="width:120px">Status</th>
                <th class="text-end text-nowrap" style="width:100px">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($imports) && is_array($imports)): ?>
                <?php foreach ($imports as $imp): ?>
                  <?php
                    $id       = $imp['id'] ?? '';
                    $criado   = dtBR($imp['created_at'] ?? '');
                    $xmls     = (int)($imp['total_xmls'] ?? 0);
                    $itens    = (int)($imp['total_itens'] ?? 0);
                    $comp     = competenciaBR($imp['competencia'] ?? '');
                    $empresa  = $imp['empresa_nome'] ?? '';
                    $servico  = $imp['servico_nome'] ?? '';
                    $status   = $imp['status'] ?? '';
                  ?>
                  <tr>
                    <td class="text-nowrap"><?= esc($id) ?></td>
                    <td class="text-nowrap small"><?= esc($criado) ?></td>
                    <td class="text-nowrap text-center"><?= esc($xmls) ?></td>
                    <td class="text-nowrap text-center"><?= esc($itens) ?></td>
                    <td class="text-nowrap"><?= esc($comp) ?></td>
                    <td><div class="text-truncate" style="max-width: 100%" title="<?= esc($empresa) ?>"><?= esc($empresa) ?></div></td>
                    <td><div class="text-truncate" style="max-width: 100%" title="<?= esc($servico) ?>"><?= esc($servico) ?></div></td>
                    <td class="text-nowrap"><?= badgeStatus($status, $id) ?></td>
                    <td class="text-end">
                      <a href="<?= base_url('operacional/nfe/review/'.($id ?: 0)) ?>" class="btn btn-sm btn-outline-primary text-nowrap">
                        <i data-feather="check-square" class="me-1"></i> Revisar
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted py-3">Sem importações ainda.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
  if (window.feather && typeof window.feather.replace === 'function') {
    window.feather.replace();
  }
</script>
<?= $this->endSection() ?>
