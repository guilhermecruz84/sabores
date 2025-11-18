<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-chart-bar text-primary me-2"></i>
                Dashboard - Avaliações de Colaboradora
            </h1>
            <p class="text-muted">Estatísticas e relatórios das avaliações dos clientes</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('avaliacao-colaboradora-cliente/dashboard') ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $dataInicio ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= $dataFim ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total de Avaliações</h6>
                            <h2 class="mb-0"><?= $estatisticas['total_avaliacoes'] ?? 0 ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Média Geral</h6>
                            <h2 class="mb-0"><?= number_format($mediaGeral, 2) ?></h2>
                        </div>
                        <div>
                            <i class="fas fa-star fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Período</h6>
                            <p class="mb-0 small"><?= date('d/m', strtotime($dataInicio)) ?> a <?= date('d/m', strtotime($dataFim)) ?></p>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Melhor Nota</h6>
                            <h2 class="mb-0">
                                <?php
                                if ($estatisticas && $estatisticas['total_avaliacoes'] > 0) {
                                    $medias = [
                                        $estatisticas['media_assiduidade'],
                                        $estatisticas['media_apresentacao'],
                                        $estatisticas['media_atendimento'],
                                        $estatisticas['media_agilidade'],
                                        $estatisticas['media_qualidade'],
                                        $estatisticas['media_cumprimento'],
                                        $estatisticas['media_proatividade'],
                                        $estatisticas['media_organizacao'],
                                        $estatisticas['media_percepcao']
                                    ];
                                    echo number_format(max($medias), 2);
                                } else {
                                    echo '0.00';
                                }
                                ?>
                            </h2>
                        </div>
                        <div>
                            <i class="fas fa-trophy fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Médias por Critério -->
    <?php if ($estatisticas && $estatisticas['total_avaliacoes'] > 0): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                Médias por Critério de Avaliação
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Assiduidade e Pontualidade</span>
                            <strong><?= number_format($estatisticas['media_assiduidade'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                 style="width: <?= ($estatisticas['media_assiduidade'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_assiduidade'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Apresentação Pessoal</span>
                            <strong><?= number_format($estatisticas['media_apresentacao'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: <?= ($estatisticas['media_apresentacao'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_apresentacao'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Atendimento e Relacionamento</span>
                            <strong><?= number_format($estatisticas['media_atendimento'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                 style="width: <?= ($estatisticas['media_atendimento'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_atendimento'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Agilidade e Produtividade</span>
                            <strong><?= number_format($estatisticas['media_agilidade'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                 style="width: <?= ($estatisticas['media_agilidade'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_agilidade'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Qualidade da Execução</span>
                            <strong><?= number_format($estatisticas['media_qualidade'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                 style="width: <?= ($estatisticas['media_qualidade'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_qualidade'], 2) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Cumprimento das Regras</span>
                            <strong><?= number_format($estatisticas['media_cumprimento'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-secondary" role="progressbar"
                                 style="width: <?= ($estatisticas['media_cumprimento'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_cumprimento'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Proatividade</span>
                            <strong><?= number_format($estatisticas['media_proatividade'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar" style="background: #6f42c1; width: <?= ($estatisticas['media_proatividade'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_proatividade'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Organização e Limpeza</span>
                            <strong><?= number_format($estatisticas['media_organizacao'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-dark" role="progressbar"
                                 style="width: <?= ($estatisticas['media_organizacao'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_organizacao'], 2) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">Percepção Geral</span>
                            <strong><?= number_format($estatisticas['media_percepcao'], 2) ?></strong>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar" style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); width: <?= ($estatisticas['media_percepcao'] / 5) * 100 ?>%">
                                <?= number_format($estatisticas['media_percepcao'], 2) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Avaliações Detalhadas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Avaliações Detalhadas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th class="text-center">Média</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avaliacoes as $avaliacao): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($avaliacao['data'])) ?></td>
                            <td><?= esc($avaliacao['cliente_nome']) ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">
                                    <?= number_format($avaliacao['media_geral'], 2) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($avaliacao['observacoes'])): ?>
                                    <small class="text-muted">
                                        <?= esc(substr($avaliacao['observacoes'], 0, 100)) ?>
                                        <?= strlen($avaliacao['observacoes']) > 100 ? '...' : '' ?>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">Sem observações</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nenhuma avaliação encontrada no período selecionado.
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
