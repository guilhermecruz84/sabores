<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="mb-0">
        <i class="fas fa-clipboard-check me-2"></i>
        Meus Checklists
    </h2>
    <p class="text-muted">Gerencie seus checklists diários de abertura e encerramento</p>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card <?= $aberturaDisponivel ? 'bg-primary' : 'bg-secondary' ?> text-white h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-sun me-2"></i>
                    Checklist de Abertura
                </h5>
                <p class="card-text">Registre o recebimento das refeições e condições iniciais</p>
                <?php if ($aberturaDisponivel): ?>
                    <a href="<?= base_url('checklists/novo/abertura') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i>
                        Iniciar Abertura
                    </a>
                <?php else: ?>
                    <button class="btn btn-light" disabled>
                        <i class="fas fa-ban me-2"></i>
                        Não disponível hoje
                    </button>
                    <small class="d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Não configurado para hoje
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card <?= $encerramentoDisponivel ? 'bg-warning' : 'bg-secondary' ?> text-<?= $encerramentoDisponivel ? 'dark' : 'white' ?> h-100">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-moon me-2"></i>
                    Checklist de Encerramento
                </h5>
                <p class="card-text">Registre sobras, faltas e ocorrências do dia</p>
                <?php if ($encerramentoDisponivel): ?>
                    <a href="<?= base_url('checklists/novo/encerramento') ?>" class="btn btn-dark">
                        <i class="fas fa-plus me-2"></i>
                        Iniciar Encerramento
                    </a>
                <?php else: ?>
                    <button class="btn btn-light" disabled>
                        <i class="fas fa-ban me-2"></i>
                        Não disponível hoje
                    </button>
                    <small class="d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Não configurado para hoje
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Checklists Pendentes -->
<?php if (!empty($registrosPendentes)): ?>
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Checklists Pendentes</strong>
    </div>
    <div class="card-body">
        <div class="list-group">
            <?php foreach ($registrosPendentes as $registro): ?>
            <a href="<?= base_url('checklists/preencher/' . $registro->id) ?>"
               class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">
                        <i class="fas fa-<?= $registro->tipo === 'abertura' ? 'sun' : 'moon' ?> me-2"></i>
                        <?= ucfirst($registro->tipo) ?> - <?= date('d/m/Y', strtotime($registro->data)) ?>
                    </h6>
                    <span class="badge bg-<?= $registro->status === 'pendente' ? 'danger' : 'warning' ?>">
                        <?= $registro->status === 'pendente' ? 'Não Iniciado' : 'Em Andamento' ?>
                    </span>
                </div>
                <p class="mb-1 text-muted small">
                    Criado em: <?= date('d/m/Y H:i', strtotime($registro->created_at)) ?>
                </p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Histórico Recente -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-2"></i>
        Histórico Recente
    </div>
    <div class="card-body">
        <?php if (!empty($registrosRecentes)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Finalizado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrosRecentes as $registro): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($registro->data)) ?></td>
                        <td>
                            <i class="fas fa-<?= $registro->tipo === 'abertura' ? 'sun' : 'moon' ?> me-1"></i>
                            <?= ucfirst($registro->tipo) ?>
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
                            <?php if ($registro->finalizado_em): ?>
                                <?= date('d/m/Y H:i', strtotime($registro->finalizado_em)) ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($registro->status === 'finalizado'): ?>
                                <a href="<?= base_url('checklists/ver/' . $registro->id) ?>"
                                   class="btn btn-sm btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('checklists/preencher/' . $registro->id) ?>"
                                   class="btn btn-sm btn-primary" title="Continuar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
            <p>Nenhum checklist encontrado</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
