<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
        Alertas de Não Conformidades
    </h2>
    <div>
        <a href="<?= base_url('alertas/historico') ?>" class="btn btn-secondary">
            <i class="fas fa-history me-2"></i>
            Ver Histórico
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('sucesso')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?= session()->getFlashdata('sucesso') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('erro')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    <?= session()->getFlashdata('erro') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-danger text-white">
        <strong><i class="fas fa-bell me-2"></i>Alertas Pendentes de Hoje</strong>
        <?php if ($totalPendentes > 0): ?>
            <span class="badge bg-white text-danger ms-2"><?= $totalPendentes ?></span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if (!empty($alertas)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="15%">Data/Hora</th>
                        <th width="20%">Empresa</th>
                        <th width="15%">Operador</th>
                        <th width="30%">Item Não Conforme</th>
                        <th width="10%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alertas as $alerta): ?>
                    <tr>
                        <td>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($alerta->created_at)) ?>
                            </small>
                        </td>
                        <td>
                            <strong><?= esc($alerta->empresa_nome) ?></strong>
                        </td>
                        <td>
                            <?= esc($alerta->operador_nome) ?>
                        </td>
                        <td>
                            <strong class="text-danger"><?= esc($alerta->descricao_item) ?></strong>
                            <?php if (!empty($alerta->observacao)): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-comment me-1"></i>
                                    <em><?= esc($alerta->observacao) ?></em>
                                </small>
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
                            <a href="<?= base_url('alertas/concluir/' . $alerta->id) ?>"
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Tem certeza que deseja marcar este alerta como concluído?')"
                               title="Marcar como Concluído">
                                <i class="fas fa-check"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3 text-success">Nenhum alerta pendente hoje!</h4>
            <p class="text-muted">Todos os itens estão em conformidade.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detalhes do Alerta -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Detalhes do Alerta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <strong>Registrado em:</strong>
                        <p id="detalheRegistro" class="mb-0"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Item Não Conforme:</strong>
                    <p id="detalheItem" class="text-danger mb-0"></p>
                </div>
                <div class="mb-3">
                    <strong>Observação do Operador:</strong>
                    <div id="detalheObservacao" class="border p-3 rounded bg-light"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" id="btnConcluirModal" class="btn btn-success" onclick="return confirm('Tem certeza que deseja marcar este alerta como concluído?')">
                    <i class="fas fa-check me-2"></i>Marcar como Concluído
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function mostrarDetalhes(alerta) {
    document.getElementById('detalheEmpresa').textContent = alerta.empresa_nome;
    document.getElementById('detalheOperador').textContent = alerta.operador_nome;
    document.getElementById('detalheData').textContent = formatarData(alerta.data_ocorrencia);
    document.getElementById('detalheRegistro').textContent = formatarDataHora(alerta.created_at);
    document.getElementById('detalheItem').textContent = alerta.descricao_item;

    const observacaoDiv = document.getElementById('detalheObservacao');
    if (alerta.observacao && alerta.observacao.trim() !== '') {
        observacaoDiv.innerHTML = '<i class="fas fa-comment me-2"></i>' + alerta.observacao;
    } else {
        observacaoDiv.innerHTML = '<em class="text-muted">Nenhuma observação registrada.</em>';
    }

    document.getElementById('btnConcluirModal').href = '<?= base_url('alertas/concluir/') ?>' + alerta.id;
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
