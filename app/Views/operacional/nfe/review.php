<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo') ?>
<?php $session = session(); ?>

<?php
function competenciaBR($comp) {
    if (preg_match('/^(\d{4})-(\d{2})$/', $comp, $m)) {
        return "{$m[2]}-{$m[1]}"; // MM-YYYY
    }
    return $comp;
}
?>

<div class="row">
  <div class="col-12">
    <?php if ($session->getFlashdata('erro')): ?>
      <div class="alert alert-danger"><?= esc($session->getFlashdata('erro')) ?></div>
    <?php endif; ?>
    <?php if ($session->getFlashdata('ok')): ?>
      <div class="alert alert-success"><?= esc($session->getFlashdata('ok')) ?></div>
    <?php endif; ?>
  </div>

  <div class="col-12 col-xl-10">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="card-title mb-0">Associar Lote #<?= esc($import['id'] ?? '') ?></h5>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-3">Criado em</dt>
          <dd class="col-sm-9"><?= esc($import['created_at'] ?? '') ?></dd>

          <dt class="col-sm-3">XMLs</dt>
          <dd class="col-sm-9"><?= esc($import['total_xmls'] ?? 0) ?></dd>

          <dt class="col-sm-3">Itens</dt>
          <dd class="col-sm-9"><?= esc($import['total_itens'] ?? 0) ?></dd>

          <dt class="col-sm-3">Status</dt>
          <dd class="col-sm-9"><?= esc($import['status'] ?? '') ?></dd>
        </dl>
      </div>
    </div>

    <form method="post" action="<?= base_url('operacional/nfe/finalize/'.($import['id'] ?? '')) ?>">
      <?= csrf_field() ?>

      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title mb-0">Cabeçalho</h5>
        </div>
        <div class="card-body">
          <div class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Competência (MM-YYYY)</label>
              <?php
                $compAtual = old('competencia', $import['competencia'] ?? date('Y-m'));
                $compBR = competenciaBR($compAtual);
              ?>
              <input type="text" class="form-control" name="competencia"
                     value="<?= esc($compBR) ?>" placeholder="MM-YYYY" maxlength="7" required
                     pattern="\d{2}-\d{4}" title="Informe no formato MM-YYYY">
            </div>
            <div class="col-md-8">
              <label class="form-label">Empresa</label>
              <select class="form-select" name="empresa" required>
                <option value="">Selecione...</option>
                <?php if (!empty($empresas) && is_array($empresas)): ?>
                  <?php foreach ($empresas as $e): ?>
                    <?php
                      $empresaNome = $e['nome'] ?? ($e['titulo'] ?? null);
                      $sel = (old('empresa', $import['empresa_nome'] ?? '') === $empresaNome) ? 'selected' : '';
                    ?>
                    <option value="<?= esc($empresaNome) ?>" <?= $sel ?>><?= esc($empresaNome) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- ITENS DO LOTE -->
      <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title mb-0">Itens do lote</h5>
          <small class="text-muted">Selecione o serviço para cada item</small>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-compact table-hover mb-0 align-middle">
              <thead class="table-light">
                <tr>
                  <th class="text-nowrap col-id">Doc</th>
                  <th class="text-nowrap col-number"># Item</th>
                  <th>Produto</th>
                  <th class="text-end text-nowrap col-number">Qtd</th>
                  <th class="text-end text-nowrap col-number">Valor (R$)</th>
                  <th style="min-width:200px">Serviço</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($items) && is_array($items)): ?>
                  <?php foreach ($items as $it): ?>
                    <?php
                      $iid   = $it['id'] ?? 0;
                      $qCom  = (float)($it['qCom'] ?? 0);
                      $vItem = isset($it['vItem']) && $it['vItem'] !== null ? (float)$it['vItem'] : (float)($it['vProd'] ?? 0);
                    ?>
                    <tr>
                      <td class="text-nowrap"><?= esc($it['doc_id'] ?? '') ?></td>
                      <td class="text-nowrap"><?= esc($it['nItem'] ?? '') ?></td>
                      <td class="text-break" style="max-width: 420px">
                        <div class="fw-semibold"><?= esc($it['xProd'] ?? '') ?></div>
                        <div class="text-muted small"><?= esc($it['cProd'] ?? '') ?></div>
                      </td>
                      <td class="text-end"><?= esc(number_format($qCom, 2, ',', '.')) ?></td>
                      <td class="text-end"><?= esc(number_format($vItem, 2, ',', '.')) ?></td>
                      <td>
                        <select class="form-select form-select-sm" name="itens_servico[<?= (int)$iid ?>]">
                          <option value="">Selecione...</option>
                          <?php if (!empty($servicos) && is_array($servicos)): ?>
                            <?php foreach ($servicos as $s): ?>
                              <?php $sid = (int)($s['id'] ?? 0); $snome = $s['nome'] ?? ($s['titulo'] ?? ''); ?>
                              <option value="<?= $sid ?>"><?= esc($snome) ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-center text-muted py-3">Nenhum item neste lote.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">
          <i data-feather="save" class="me-1"></i> Salvar e lançar em Refeições
        </button>
        <a href="<?= base_url('nfe') ?>" class="btn btn-outline-secondary">
          <i data-feather="arrow-left" class="me-1"></i> Voltar
        </a>
      </div>
    </form>

    <!-- Documentos -->
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Documentos do lote</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-compact table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th class="col-id">#</th>
                <th>Chave</th>
                <th class="col-number">Número/Série</th>
                <th class="col-date">Emissão</th>
                <th>Emitente</th>
                <th>Destinatário</th>
                <th class="text-end col-number">vNF</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($docs) && is_array($docs)): ?>
                <?php foreach ($docs as $doc): ?>
                  <tr>
                    <td><?= esc($doc['id'] ?? '') ?></td>
                    <td class="text-break" style="max-width: 320px"><?= esc($doc['chave'] ?? '') ?></td>
                    <td><?= esc(($doc['numero'] ?? '').' / '.($doc['serie'] ?? '')) ?></td>
                    <td><?= esc($doc['dhEmi'] ?? '') ?></td>
                    <td><?= esc(($doc['emit_nome'] ?? '').' ('.($doc['emit_cnpj'] ?? '').')') ?></td>
                    <td><?= esc(($doc['dest_nome'] ?? '').' ('.($doc['dest_cnpjcpf'] ?? '').')') ?></td>
                    <td class="text-end"><?= esc(number_format((float)($doc['vNF'] ?? 0), 2, ',', '.')) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Nenhum documento neste lote.</td></tr>
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
  // Máscara leve para campo de competência (MM-YYYY)
  document.querySelectorAll('input[name="competencia"]').forEach(input => {
    input.addEventListener('input', e => {
      let v = e.target.value.replace(/\D/g, '').slice(0,6);
      if (v.length >= 3) v = v.slice(0,2) + '-' + v.slice(2);
      e.target.value = v;
    });
  });
  if (window.feather && typeof window.feather.replace === 'function') {
    window.feather.replace();
  }
</script>
<?= $this->endSection() ?>
