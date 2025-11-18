<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-history me-2"></i>
        Histórico de Avaliações
    </h2>
    <a href="<?= base_url('avaliacoes/dashboard') ?>" class="btn btn-secondary">
        <i class="fas fa-chart-bar me-2"></i>
        Ver Dashboard
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Empresa</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Todas as empresas</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa->id ?>"
                                    <?= $filtros['empresa_id'] == $empresa->id ? 'selected' : '' ?>>
                                <?= esc($empresa->nome_fantasia) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control"
                           value="<?= $filtros['data_inicio'] ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control"
                           value="<?= $filtros['data_fim'] ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($avaliacoes)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Nenhuma avaliação encontrada para os filtros selecionados.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabelaAvaliacoes">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Empresa</th>
                            <th>Cliente</th>
                            <th>Avaliação</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $avaliacaoLabels = [
                            'otimo' => ['texto' => '⭐⭐⭐⭐ Ótimo', 'cor' => 'success'],
                            'bom' => ['texto' => '⭐⭐⭐ Bom', 'cor' => 'primary'],
                            'regular' => ['texto' => '⭐⭐ Regular', 'cor' => 'warning'],
                            'ruim' => ['texto' => '⭐ Ruim', 'cor' => 'danger']
                        ];
                        ?>
                        <?php foreach ($avaliacoes as $avaliacao): ?>
                        <tr>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($avaliacao->data)) ?></strong>
                            </td>
                            <td><?= esc($avaliacao->empresa_nome) ?></td>
                            <td><?= esc($avaliacao->cliente_nome) ?></td>
                            <td>
                                <span class="badge bg-<?= $avaliacaoLabels[$avaliacao->avaliacao]['cor'] ?>">
                                    <?= $avaliacaoLabels[$avaliacao->avaliacao]['texto'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($avaliacao->motivo): ?>
                                    <button type="button" class="btn btn-sm btn-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalMotivo"
                                            onclick="mostrarMotivo('<?= esc($avaliacao->cliente_nome) ?>', '<?= date('d/m/Y', strtotime($avaliacao->data)) ?>', '<?= addslashes($avaliacao->motivo) ?>')">
                                        <i class="fas fa-comment"></i> Ver Motivo
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Ver Motivo -->
<div class="modal fade" id="modalMotivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalMotivoTitle">Motivo da Avaliação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Cliente:</strong> <span id="motivoCliente"></span></p>
                <p><strong>Data:</strong> <span id="motivoData"></span></p>
                <hr>
                <p id="motivoTexto"></p>
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
$(document).ready(function() {
    <?php if (!empty($avaliacoes)): ?>
    $('#tabelaAvaliacoes').DataTable({
        order: [[0, 'desc']], // Ordena por data decrescente
        pageLength: 25,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });
    <?php endif; ?>
});

function mostrarMotivo(cliente, data, motivo) {
    $('#motivoCliente').text(cliente);
    $('#motivoData').text(data);
    $('#motivoTexto').text(motivo);
}
</script>
<?= $this->endSection() ?>
