<?= $this->extend('layouts/operacional'); ?>
<?= $this->section('conteudo') ?>
<?php
$session = session();

function competenciaBR($comp) {
    if (preg_match('/^(\d{4})-(\d{2})$/', $comp, $m)) {
        return "{$m[2]}-{$m[1]}"; // MM-YYYY
    }
    return $comp;
}

function formatarDataEmissao($data) {
    if (empty($data)) return '-';

    // Se vier no formato ISO: 2025-11-14T15:30:00-03:00 ou 2025-11-14 15:30:00
    try {
        $dt = new DateTime($data);
        return $dt->format('d/m/Y \à\s H:i');
    } catch (Exception $e) {
        return $data; // Retorna original se não conseguir converter
    }
}

// Array de meses para os dropdowns
$meses = [
    '01' => 'Janeiro',
    '02' => 'Fevereiro',
    '03' => 'Março',
    '04' => 'Abril',
    '05' => 'Maio',
    '06' => 'Junho',
    '07' => 'Julho',
    '08' => 'Agosto',
    '09' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
];

// Mês e ano padrão (atual)
$mesPadrao = date('m');
$anoPadrao = date('Y');
$anoAtual = (int)date('Y');
?>

<div class="row">
  <div class="col-12">
    <?php if ($session->getFlashdata('erro')): ?>
      <div class="alert alert-danger"><?= esc($session->getFlashdata('erro')) ?></div>
    <?php endif; ?>
    <?php if ($session->getFlashdata('ok')): ?>
      <div class="alert alert-success"><?= esc($session->getFlashdata('ok')) ?></div>
    <?php endif; ?>

    <?php
    // Exibir XMLs ignorados (duplicados)
    $ignorados = $session->getFlashdata('info_ignorados');
    if (!empty($ignorados) && is_array($ignorados)):
    ?>
      <div class="alert alert-warning">
        <h6 class="alert-heading mb-2">⚠️ XMLs Ignorados (já importados anteriormente):</h6>
        <ul class="mb-0">
          <?php foreach ($ignorados as $xml): ?>
            <li><small><?= esc($xml) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php
    // Exibir XMLs com erro
    $erros = $session->getFlashdata('info_erros');
    if (!empty($erros) && is_array($erros)):
    ?>
      <div class="alert alert-danger">
        <h6 class="alert-heading mb-2">❌ XMLs com Erro:</h6>
        <ul class="mb-0">
          <?php foreach ($erros as $erro): ?>
            <li><small><?= esc($erro) ?></small></li>
          <?php endforeach; ?>
        </ul>
      </div>
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
          <div class="row g-3">
            <div class="col-md-12">
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
                  <th class="text-nowrap col-date">Emissão NF</th>
                  <th class="text-nowrap col-number"># Item</th>
                  <th>Produto</th>
                  <th class="text-end text-nowrap col-number">Qtd</th>
                  <th class="text-end text-nowrap col-number">Valor (R$)</th>
                  <th style="min-width:130px">Mês</th>
                  <th style="min-width:100px">Ano</th>
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
                      <td class="text-nowrap small"><?= formatarDataEmissao($it['dhEmi'] ?? '') ?></td>
                      <td class="text-nowrap"><?= esc($it['nItem'] ?? '') ?></td>
                      <td class="text-break" style="max-width: 420px">
                        <div class="fw-semibold"><?= esc($it['xProd'] ?? '') ?></div>
                        <div class="text-muted small"><?= esc($it['cProd'] ?? '') ?></div>
                      </td>
                      <td class="text-end"><?= esc(number_format($qCom, 2, ',', '.')) ?></td>
                      <td class="text-end"><?= esc(number_format($vItem, 2, ',', '.')) ?></td>
                      <td>
                        <select class="form-select form-select-sm" name="itens_mes[<?= (int)$iid ?>]" required>
                          <?php foreach ($meses as $num => $nome): ?>
                            <option value="<?= $num ?>" <?= $mesPadrao === $num ? 'selected' : '' ?>><?= $nome ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                      <td>
                        <select class="form-select form-select-sm" name="itens_ano[<?= (int)$iid ?>]" required>
                          <?php for ($ano = $anoAtual - 2; $ano <= $anoAtual + 1; $ano++): ?>
                            <option value="<?= $ano ?>" <?= $anoPadrao == $ano ? 'selected' : '' ?>><?= $ano ?></option>
                          <?php endfor; ?>
                        </select>
                      </td>
                      <td>
                        <?php
                          // Verificar se existe associação salva para este produto
                          $cProd = $it['cProd'] ?? '';
                          $servicoSalvo = !empty($cProd) && isset($associacoes[$cProd]) ? $associacoes[$cProd] : 0;
                        ?>
                        <select class="form-select form-select-sm" name="itens_servico[<?= (int)$iid ?>]">
                          <option value="">Selecione...</option>
                          <?php if (!empty($servicos) && is_array($servicos)): ?>
                            <?php foreach ($servicos as $s): ?>
                              <?php
                                $sid = (int)($s['id'] ?? 0);
                                $snome = $s['nome'] ?? ($s['titulo'] ?? '');
                                $selected = ($servicoSalvo > 0 && $sid === $servicoSalvo) ? 'selected' : '';
                              ?>
                              <option value="<?= $sid ?>" <?= $selected ?>><?= esc($snome) ?></option>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </select>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="9" class="text-center text-muted py-3">Nenhum item neste lote.</td></tr>
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
        <a href="<?= base_url('operacional/nfe') ?>" class="btn btn-outline-secondary">
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
                    <td class="text-nowrap"><?= formatarDataEmissao($doc['dhEmi'] ?? '') ?></td>
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
  // Ícones Feather
  if (window.feather && typeof window.feather.replace === 'function') {
    window.feather.replace();
  }
</script>
<?= $this->endSection() ?>
