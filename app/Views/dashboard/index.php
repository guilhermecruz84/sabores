<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">
            <i class="fas fa-home me-2"></i>
            Dashboard
        </h2>
        <p class="text-muted mb-0">Bem-vindo(a), <?= esc($usuarioLogado->nome) ?>!</p>
    </div>
    <a href="<?= base_url('chamados/novo') ?>" class="btn btn-primary btn-lg">
        <i class="fas fa-plus-circle me-2"></i>
        Novo Chamado
    </a>
</div>

<!-- Estatísticas em Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total de Chamados</h6>
                        <h2 class="mb-0"><?= $estatisticas->total ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-ticket-alt stat-icon text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Em Aberto</h6>
                        <h2 class="mb-0"><?= ($contagem_status['aberto'] ?? 0) + ($contagem_status['em_atendimento'] ?? 0) ?></h2>
                    </div>
                    <i class="fas fa-clock stat-icon text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Ocorrências</h6>
                        <h2 class="mb-0"><?= $contagem_tipo['ocorrencia'] ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-exclamation-triangle stat-icon text-danger"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Solicitações</h6>
                        <h2 class="mb-0"><?= $contagem_tipo['solicitacao'] ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-file-alt stat-icon text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie me-2"></i>
                Chamados por Status
            </div>
            <div class="card-body">
                <canvas id="chartStatus" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i>
                Chamados por Tipo
            </div>
            <div class="card-body">
                <canvas id="chartTipo" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chamados Recentes -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>
        <?= $usuarioLogado->tipo === 'cliente' ? 'Meus Chamados Recentes' : 'Chamados Recentes' ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Tipo</th>
                        <th>Assunto</th>
                        <?php if ($usuarioLogado->tipo !== 'cliente'): ?>
                        <th>Cliente</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($chamados_recentes)): ?>
                        <?php foreach ($chamados_recentes as $chamado): ?>
                        <tr>
                            <td><strong>#<?= esc($chamado->protocolo) ?></strong></td>
                            <td>
                                <?php if ($chamado->tipo === 'ocorrencia'): ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Ocorrência
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Solicitação
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($chamado->assunto) ?></td>
                            <?php if ($usuarioLogado->tipo !== 'cliente'): ?>
                            <td>
                                <small class="text-muted">
                                    <?= esc($chamado->empresa_nome) ?><br>
                                    <?= esc($chamado->usuario_nome) ?>
                                </small>
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php
                                $statusColors = [
                                    'aberto' => 'primary',
                                    'em_atendimento' => 'warning',
                                    'aguardando_cliente' => 'info',
                                    'finalizado' => 'success'
                                ];
                                $statusLabels = [
                                    'aberto' => 'Aberto',
                                    'em_atendimento' => 'Em Atendimento',
                                    'aguardando_cliente' => 'Aguardando Cliente',
                                    'finalizado' => 'Finalizado'
                                ];
                                ?>
                                <span class="badge bg-<?= $statusColors[$chamado->status] ?>">
                                    <?= $statusLabels[$chamado->status] ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($chamado->created_at)) ?></td>
                            <td>
                                <a href="<?= base_url('chamados/ver/' . $chamado->id) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $usuarioLogado->tipo !== 'cliente' ? '7' : '6' ?>" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum chamado encontrado
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
// Gráfico de Status
const ctxStatus = document.getElementById('chartStatus').getContext('2d');
new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
        labels: ['Aberto', 'Em Atendimento', 'Aguardando Cliente', 'Finalizado'],
        datasets: [{
            data: [
                <?= $contagem_status['aberto'] ?? 0 ?>,
                <?= $contagem_status['em_atendimento'] ?? 0 ?>,
                <?= $contagem_status['aguardando_cliente'] ?? 0 ?>,
                <?= $contagem_status['finalizado'] ?? 0 ?>
            ],
            backgroundColor: ['#0d6efd', '#ffc107', '#17a2b8', '#28a745']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de Tipo
const ctxTipo = document.getElementById('chartTipo').getContext('2d');
new Chart(ctxTipo, {
    type: 'bar',
    data: {
        labels: ['Ocorrências', 'Solicitações'],
        datasets: [{
            label: 'Quantidade',
            data: [
                <?= $contagem_tipo['ocorrencia'] ?? 0 ?>,
                <?= $contagem_tipo['solicitacao'] ?? 0 ?>
            ],
            backgroundColor: ['#dc3545', '#17a2b8']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
