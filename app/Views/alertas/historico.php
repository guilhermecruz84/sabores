<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-history me-2"></i>
        Histórico de Alertas
    </h2>
    <a href="<?= base_url('alertas') ?>" class="btn btn-danger">
        <i class="fas fa-bell me-2"></i>
        Alertas Pendentes
    </a>
</div>

<div class="card">
    <div class="card-header">
        <strong><i class="fas fa-list me-2"></i>Todos os Alertas</strong>
    </div>
    <div class="card-body">
        <?php if (!empty($alertas)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="10%">Data</th>
                        <th width="15%">Empresa</th>
                        <th width="12%">Operador</th>
                        <th width="30%">Item</th>
                        <th width="10%">Status</th>
                        <th width="13%">Concluído Por</th>
                        <th width="10%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alertas as $alerta): ?>
                    <tr class="<?= $alerta->status == 'concluido' ? 'table-secondary' : 'table-danger' ?>">
                        <td>
                            <small><?= date('d/m/Y', strtotime($alerta->data_ocorrencia)) ?></small>
                        </td>
                        <td><?= esc($alerta->empresa_nome ?? 'N/A') ?></td>
                        <td><?= esc($alerta->operador_nome ?? 'N/A') ?></td>
                        <td>
                            <strong><?= esc($alerta->descricao_item) ?></strong>
                            <?php if (!empty($alerta->observacao)): ?>
                                <br><small class="text-muted"><em><?= esc(substr($alerta->observacao, 0, 50)) ?><?= strlen($alerta->observacao) > 50 ? '...' : '' ?></em></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($alerta->status == 'pendente'): ?>
                                <span class="badge bg-danger">Pendente</span>
                            <?php else: ?>
                                <span class="badge bg-success">Concluído</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($alerta->status == 'concluido'): ?>
                                <small>
                                    <?= esc($alerta->concluido_por_nome ?? 'N/A') ?><br>
                                    <?= date('d/m/Y H:i', strtotime($alerta->concluido_em)) ?>
                                </small>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalhes"
                                    onclick="mostrarDetalhes(<?= htmlspecialchars(json_encode($alerta)) ?>)"
                                    title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-center text-muted py-4">Nenhum alerta registrado.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detalhes do Alerta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Empresa:</strong>
                        <p id="detalheEmpresa" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Operador:</strong>
                        <p id="detalheOperador" class="mb-0"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Data da Ocorrência:</strong>
                        <p id="detalheData" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <p id="detalheStatus" class="mb-0"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Item Não Conforme:</strong>
                    <p id="detalheItem" class="mb-0"></p>
                </div>
                <div class="mb-3">
                    <strong>Observação:</strong>
                    <div id="detalheObservacao" class="border p-3 rounded bg-light"></div>
                </div>
                <div id="detalheConclusao" style="display: none;">
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Concluído Por:</strong>
                            <p id="detalheConcluidoPor" class="mb-0"></p>
                        </div>
                        <div class="col-md-6">
                            <strong>Concluído Em:</strong>
                            <p id="detalheConcluidoEm" class="mb-0"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function mostrarDetalhes(alerta) {
    document.getElementById('detalheEmpresa').textContent = alerta.empresa_nome || 'N/A';
    document.getElementById('detalheOperador').textContent = alerta.operador_nome || 'N/A';
    document.getElementById('detalheData').textContent = formatarData(alerta.data_ocorrencia);
    document.getElementById('detalheItem').textContent = alerta.descricao_item;

    const statusEl = document.getElementById('detalheStatus');
    if (alerta.status == 'pendente') {
        statusEl.innerHTML = '<span class="badge bg-danger">Pendente</span>';
    } else {
        statusEl.innerHTML = '<span class="badge bg-success">Concluído</span>';
    }

    const observacaoDiv = document.getElementById('detalheObservacao');
    if (alerta.observacao && alerta.observacao.trim() !== '') {
        observacaoDiv.innerHTML = '<i class="fas fa-comment me-2"></i>' + alerta.observacao;
    } else {
        observacaoDiv.innerHTML = '<em class="text-muted">Nenhuma observação registrada.</em>';
    }

    const conclusaoDiv = document.getElementById('detalheConclusao');
    if (alerta.status == 'concluido') {
        document.getElementById('detalheConcluidoPor').textContent = alerta.concluido_por_nome || 'N/A';
        document.getElementById('detalheConcluidoEm').textContent = formatarDataHora(alerta.concluido_em);
        conclusaoDiv.style.display = 'block';
    } else {
        conclusaoDiv.style.display = 'none';
    }
}

function formatarData(data) {
    const partes = data.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function formatarDataHora(dataHora) {
    const d = new Date(dataHora);
    const dia = String(d.getDate()).padStart(2, '0');
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const ano = d.getFullYear();
    const hora = String(d.getHours()).padStart(2, '0');
    const min = String(d.getMinutes()).padStart(2, '0');
    return dia + '/' + mes + '/' + ano + ' ' + hora + ':' + min;
}
</script>
<?= $this->endSection() ?>
