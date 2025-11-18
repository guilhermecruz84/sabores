<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0">
                <i class="fas fa-history text-primary me-2"></i>
                Histórico de Avaliações
            </h1>
            <p class="text-muted">Minhas avaliações de colaboradora</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('avaliacao-colaboradora-cliente') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nova Avaliação
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('avaliacao-colaboradora-cliente/historico') ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Data Início</label>
                    <input type="date" name="data_inicio" class="form-control" value="<?= $dataInicio ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Data Fim</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= $dataFim ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                    <a href="<?= base_url('avaliacao-colaboradora-cliente/historico') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Avaliações -->
    <?php if (empty($avaliacoes)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nenhuma avaliação encontrada. <a href="<?= base_url('avaliacao-colaboradora-cliente') ?>" class="alert-link">Fazer primeira avaliação</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($avaliacoes as $avaliacao): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?= date('d/m/Y', strtotime($avaliacao['data'])) ?>
                                </span>
                                <span class="badge bg-primary rounded-pill">
                                    Média: <?= number_format($avaliacao['media_geral'], 1) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Notas por critério:</small>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Assiduidade</small>
                                    <span class="rating-badge"><?= $avaliacao['assiduidade_pontualidade'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Apresentação</small>
                                    <span class="rating-badge"><?= $avaliacao['apresentacao_pessoal'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Atendimento</small>
                                    <span class="rating-badge"><?= $avaliacao['atendimento_relacionamento'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Agilidade</small>
                                    <span class="rating-badge"><?= $avaliacao['agilidade_produtividade'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Qualidade</small>
                                    <span class="rating-badge"><?= $avaliacao['qualidade_execucao'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Cumprimento Regras</small>
                                    <span class="rating-badge"><?= $avaliacao['cumprimento_regras'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Proatividade</small>
                                    <span class="rating-badge"><?= $avaliacao['proatividade'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Organização</small>
                                    <span class="rating-badge"><?= $avaliacao['organizacao_limpeza'] ?></span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <small>Percepção Geral</small>
                                    <span class="rating-badge"><?= $avaliacao['percepcao_geral'] ?></span>
                                </div>
                            </div>

                            <?php if (!empty($avaliacao['observacoes'])): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted d-block mb-1"><strong>Observações:</strong></small>
                                    <small class="text-muted"><?= esc($avaliacao['observacoes']) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.rating-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    font-weight: bold;
    font-size: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
</style>

<?= $this->endSection() ?>
