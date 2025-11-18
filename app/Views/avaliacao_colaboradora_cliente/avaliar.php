<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">
                <i class="fas fa-star text-warning me-2"></i>
                Avaliação de Colaboradora
            </h1>
            <p class="text-muted">Avalie o desempenho da colaboradora este mês</p>
        </div>
    </div>

    <?php if ($jaAvaliou): ?>
        <div class="alert alert-success">
            <h5 class="alert-heading">
                <i class="fas fa-check-circle me-2"></i>
                Avaliação do Mês Já Realizada!
            </h5>
            <p class="mb-2">
                Você já fez sua avaliação de <strong><?= $nomeMes ?></strong> no dia
                <strong><?= date('d/m/Y', strtotime($avaliacaoDoMes['data'])) ?></strong>.
            </p>
            <hr>
            <p class="mb-0 small">
                <i class="fas fa-info-circle me-1"></i>
                Você pode fazer uma nova avaliação apenas no próximo mês.
            </p>
            <div class="mt-3">
                <a href="<?= base_url('avaliacao-colaboradora-cliente/historico') ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-history me-2"></i>
                    Ver Todas as Avaliações
                </a>
            </div>
        </div>

        <!-- Card com resumo da avaliação do mês -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>
                    Sua Avaliação de <?= $nomeMes ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Notas por Critério:</h6>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Assiduidade e Pontualidade:</span>
                            <strong><?= $avaliacaoDoMes['assiduidade_pontualidade'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Apresentação Pessoal:</span>
                            <strong><?= $avaliacaoDoMes['apresentacao_pessoal'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Atendimento e Relacionamento:</span>
                            <strong><?= $avaliacaoDoMes['atendimento_relacionamento'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Agilidade e Produtividade:</span>
                            <strong><?= $avaliacaoDoMes['agilidade_produtividade'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Qualidade da Execução:</span>
                            <strong><?= $avaliacaoDoMes['qualidade_execucao'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Cumprimento das Regras:</span>
                            <strong><?= $avaliacaoDoMes['cumprimento_regras'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Proatividade:</span>
                            <strong><?= $avaliacaoDoMes['proatividade'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Organização e Limpeza:</span>
                            <strong><?= $avaliacaoDoMes['organizacao_limpeza'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Percepção Geral:</span>
                            <strong><?= $avaliacaoDoMes['percepcao_geral'] ?> <i class="fas fa-star text-warning"></i></strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Média Geral:</strong>
                            <h5 class="mb-0">
                                <span class="badge bg-primary"><?= number_format($avaliacaoDoMes['media_geral'], 2) ?></span>
                            </h5>
                        </div>
                    </div>
                </div>

                <?php if (!empty($avaliacaoDoMes['observacoes'])): ?>
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="text-primary">Suas Observações:</h6>
                        <p class="text-muted mb-0"><?= esc($avaliacaoDoMes['observacoes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

    <div class="row">
        <div class="col-lg-10 col-xl-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Formulário de Avaliação - <?= $nomeMes ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('avaliacao-colaboradora-cliente/salvar') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="alert alert-light border">
                            <strong>Instruções:</strong> Avalie cada critério com notas de 1 a 5, onde:
                            <br>
                            <span class="badge bg-danger">1</span> Péssimo &nbsp;
                            <span class="badge bg-warning">2</span> Ruim &nbsp;
                            <span class="badge bg-info">3</span> Regular &nbsp;
                            <span class="badge bg-primary">4</span> Bom &nbsp;
                            <span class="badge bg-success">5</span> Excelente
                            <hr>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Você pode avaliar apenas <strong>uma vez por mês</strong>.
                            </small>
                        </div>

                        <!-- 1. Assiduidade e Pontualidade -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-clock me-2"></i>
                                1. Assiduidade e Pontualidade
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Chega no horário diariamente</li>
                                <li>Cumpre corretamente os intervalos</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="assiduidade_pontualidade" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 2. Apresentação Pessoal -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user-tie me-2"></i>
                                2. Apresentação Pessoal
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Uniforme limpo</li>
                                <li>Uso correto de EPIs</li>
                                <li>Higiene pessoal adequada</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="apresentacao_pessoal" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 3. Atendimento e Relacionamento -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-handshake me-2"></i>
                                3. Atendimento e Relacionamento
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Atendimento cordial com os colaboradores</li>
                                <li>Boa comunicação</li>
                                <li>Respeito e educação</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="atendimento_relacionamento" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 4. Agilidade e Produtividade -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-bolt me-2"></i>
                                4. Agilidade e Produtividade
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Agilidade no atendimento das refeições</li>
                                <li>Organização e fluxo na distribuição</li>
                                <li>Cumprimento do tempo de reposição dos alimentos</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="agilidade_produtividade" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 5. Qualidade da Execução -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-check-double me-2"></i>
                                5. Qualidade da Execução
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Mantém a bancada limpa</li>
                                <li>Segue as orientações do cliente</li>
                                <li>Cumpre os padrões definidos pela sua empresa</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="qualidade_execucao" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 6. Cumprimento das Regras do Cliente -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-gavel me-2"></i>
                                6. Cumprimento das Regras do Cliente
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Segue normas internas</li>
                                <li>Respeita regras de convivência</li>
                                <li>Mantém comportamento profissional</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="cumprimento_regras" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 7. Proatividade -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-lightbulb me-2"></i>
                                7. Proatividade
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Identifica problemas e comunica rapidamente</li>
                                <li>Ajuda em atividades adicionais quando necessário</li>
                                <li>Mostra iniciativa no dia a dia</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="proatividade" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 8. Organização e Limpeza Geral -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-broom me-2"></i>
                                8. Organização e Limpeza Geral
                            </h6>
                            <ul class="small text-muted mb-3">
                                <li>Mesa de trabalho organizada</li>
                                <li>Limpeza do refeitório conforme padrões</li>
                                <li>Zelo pelos equipamentos utilizados</li>
                            </ul>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="organizacao_limpeza" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 9. Percepção Geral do Cliente -->
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-line me-2"></i>
                                9. Percepção Geral do Cliente
                            </h6>
                            <p class="small text-muted mb-3">
                                Nível geral de satisfação com o trabalho dela
                            </p>
                            <div class="rating-stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <label class="rating-label">
                                        <input type="radio" name="percepcao_geral" value="<?= $i ?>" required>
                                        <span class="rating-star" data-value="<?= $i ?>">
                                            <i class="fas fa-star"></i>
                                        </span>
                                        <span class="rating-text"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- 10. Observações -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-comment me-2"></i>
                                10. Observações Importantes
                            </h6>
                            <p class="small text-muted mb-3">
                                Campo livre para escrever fatos, elogios ou situações específicas (opcional)
                            </p>
                            <textarea name="observacoes" class="form-control" rows="5" placeholder="Digite suas observações aqui..."></textarea>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Enviar Avaliação
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<style>
.rating-stars {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.rating-label {
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s;
}

.rating-label:hover {
    background: #f8f9fa;
}

.rating-label input[type="radio"] {
    display: none;
}

.rating-star {
    font-size: 32px;
    color: #ddd;
    transition: all 0.3s;
}

.rating-label input[type="radio"]:checked ~ .rating-star {
    color: #ffc107;
}

.rating-label:hover .rating-star {
    color: #ffeb3b;
    transform: scale(1.1);
}

.rating-text {
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.rating-label input[type="radio"]:checked ~ .rating-text {
    color: #ffc107;
}
</style>

<script>
// Adicionar interatividade às estrelas
document.querySelectorAll('.rating-stars').forEach(ratingGroup => {
    const labels = ratingGroup.querySelectorAll('.rating-label');

    labels.forEach((label, index) => {
        label.addEventListener('mouseenter', () => {
            labels.forEach((l, i) => {
                const star = l.querySelector('.rating-star');
                if (i <= index) {
                    star.style.color = '#ffc107';
                } else {
                    const isChecked = l.querySelector('input[type="radio"]').checked;
                    star.style.color = isChecked ? '#ffc107' : '#ddd';
                }
            });
        });
    });

    ratingGroup.addEventListener('mouseleave', () => {
        labels.forEach(l => {
            const star = l.querySelector('.rating-star');
            const isChecked = l.querySelector('input[type="radio"]').checked;
            star.style.color = isChecked ? '#ffc107' : '#ddd';
        });
    });
});
</script>

<?= $this->endSection() ?>
