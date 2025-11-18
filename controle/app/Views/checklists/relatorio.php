<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="mb-0">
        <i class="fas fa-chart-bar me-2"></i>
        Relatório de Checklists
    </h2>
    <p class="text-muted">Acompanhe os checklists de todas as empresas</p>
</div>

<!-- Estatísticas -->
<?php if ($estatisticas): ?>
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="mb-0"><?= $estatisticas->total ?></h3>
                <p class="text-muted mb-0">Total de Checklists</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-success"><?= $estatisticas->finalizados ?></h3>
                <p class="text-muted mb-0">Finalizados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-primary"><?= $estatisticas->aberturas ?></h3>
                <p class="text-muted mb-0">Aberturas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="mb-0 text-warning"><?= $estatisticas->encerramentos ?></h3>
                <p class="text-muted mb-0">Encerramentos</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Itens Mais Frequentes -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <i class="fas fa-plus-circle me-2"></i>
                <strong>Top 5 Sobras Mais Frequentes</strong>
            </div>
            <div class="card-body">
                <?php if (!empty($sobrasFrequentes)): ?>
                    <ul class="list-group">
                        <?php foreach ($sobrasFrequentes as $sobra): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= esc($sobra->item) ?>
                            <span class="badge bg-success rounded-pill"><?= $sobra->total ?>x</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhuma sobra registrada</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-minus-circle me-2"></i>
                <strong>Top 5 Faltas Mais Frequentes</strong>
            </div>
            <div class="card-body">
                <?php if (!empty($faltasFrequentes)): ?>
                    <ul class="list-group">
                        <?php foreach ($faltasFrequentes as $falta): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= esc($falta->item) ?>
                            <span class="badge bg-danger rounded-pill"><?= $falta->total ?>x</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted mb-0">Nenhuma falta registrada</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('checklists/relatorio') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Empresa</label>
                    <select name="empresa_id" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa->id ?>" <?= ($filtros['empresa_id'] ?? '') == $empresa->id ? 'selected' : '' ?>>
                            <?= esc($empresa->nome_fantasia) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= esc($filtros['data_inicio'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= esc($filtros['data_fim'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="abertura" <?= ($filtros['tipo'] ?? '') === 'abertura' ? 'selected' : '' ?>>Abertura</option>
                        <option value="encerramento" <?= ($filtros['tipo'] ?? '') === 'encerramento' ? 'selected' : '' ?>>Encerramento</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="<?= base_url('checklists/relatorio') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Checklists -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>
        Checklists Registrados (<?= count($registros) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabelaChecklists">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Empresa</th>
                        <th>Operador</th>
                        <th>Status</th>
                        <th>Finalizado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($registros)): ?>
                        <?php foreach ($registros as $registro): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($registro->data)) ?></td>
                            <td>
                                <span class="badge bg-<?= $registro->tipo === 'abertura' ? 'primary' : 'warning' ?>">
                                    <i class="fas fa-<?= $registro->tipo === 'abertura' ? 'sun' : 'moon' ?> me-1"></i>
                                    <?= ucfirst($registro->tipo) ?>
                                </span>
                            </td>
                            <td>
                                <small><strong><?= esc($registro->empresa_nome) ?></strong></small>
                            </td>
                            <td>
                                <small><?= esc($registro->operador_nome) ?></small>
                            </td>
                            <td>
                                <?php
                                $statusCores = [
                                    'pendente' => 'secondary',
                                    'em_andamento' => 'warning',
                                    'finalizado' => 'success'
                                ];
                                $statusLabels = [
                                    'pendente' => 'Pendente',
                                    'em_andamento' => 'Em Andamento',
                                    'finalizado' => 'Finalizado'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusCores[$registro->status] ?>">
                                    <?= $statusLabels[$registro->status] ?>
                                </span>
                            </td>
                            <td>
                                <small>
                                    <?php if ($registro->finalizado_em): ?>
                                        <?= date('d/m/Y', strtotime($registro->finalizado_em)) ?><br>
                                        <?= date('H:i', strtotime($registro->finalizado_em)) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= base_url('checklists/ver/' . $registro->id) ?>"
                                   class="btn btn-sm btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum checklist encontrado
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    <?php if (!empty($registros)): ?>
    $('#tabelaChecklists').DataTable({
        order: [[0, 'desc']], // Ordena pela data
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: 6 } // Desabilita ordenação na coluna de Ações
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
