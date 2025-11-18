<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <a href="<?= base_url('chamados') ?>" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>

    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h2 class="mb-1">
                Chamado #<?= esc($chamado->protocolo) ?>
            </h2>
            <p class="text-muted mb-0">
                Aberto em <?= date('d/m/Y \à\s H:i', strtotime($chamado->created_at)) ?>
                <?php if ($chamado->data_finalizacao): ?>
                    | Finalizado em <?= date('d/m/Y \à\s H:i', strtotime($chamado->data_finalizacao)) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="text-end">
            <?php
            $statusCores = [
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
            <span class="badge bg-<?= $statusCores[$chamado->status] ?> fs-5">
                <?= $statusLabels[$chamado->status] ?>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Coluna Principal -->
    <div class="col-md-8">
        <!-- Informações do Chamado -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-info-circle me-2"></i>
                Informações
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?php if ($chamado->tipo === 'ocorrencia'): ?>
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Ocorrência
                        </span>
                    <?php else: ?>
                        <span class="badge bg-info fs-6">
                            <i class="fas fa-file-alt me-1"></i>
                            Solicitação
                        </span>
                    <?php endif; ?>

                    <?php if ($chamado->categoria): ?>
                        <span class="badge bg-secondary fs-6 ms-2">
                            <?= esc($chamado->categoria) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h4 class="mb-3"><?= esc($chamado->assunto) ?></h4>

                <div class="bg-light p-3 rounded mb-3">
                    <?= nl2br(esc($chamado->descricao)) ?>
                </div>

                <?php if (!empty($anexos)): ?>
                <div class="mb-3">
                    <h6><i class="fas fa-paperclip me-2"></i>Anexos Iniciais</h6>
                    <div class="row">
                        <?php foreach ($anexos as $anexo): ?>
                            <?php if ($anexo->resposta_id === null): ?>
                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body p-2 text-center">
                                        <?php if (\App\Models\AnexoModel::isImagem($anexo->tipo)): ?>
                                            <img src="<?= base_url('uploads/chamados/' . $anexo->nome_arquivo) ?>" class="img-fluid rounded mb-2" style="max-height: 100px;">
                                        <?php else: ?>
                                            <i class="fas fa-file fa-3x text-secondary mb-2"></i>
                                        <?php endif; ?>
                                        <p class="mb-0 small">
                                            <strong><?= esc($anexo->nome_original) ?></strong><br>
                                            <small class="text-muted"><?= \App\Models\AnexoModel::formatarTamanho($anexo->tamanho) ?></small>
                                        </p>
                                        <a href="<?= base_url('chamados/download/' . $anexo->id) ?>" class="btn btn-sm btn-primary mt-1">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Timeline de Respostas -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-comments me-2"></i>
                Histórico de Conversas (<?= count($respostas) ?>)
            </div>
            <div class="card-body">
                <?php if (!empty($respostas)): ?>
                    <?php foreach ($respostas as $resposta): ?>
                    <div class="border-start border-3 <?= $resposta->usuario_tipo === 'cliente' ? 'border-primary' : 'border-success' ?> ps-3 pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong><?= esc($resposta->usuario_nome) ?></strong>
                                <span class="badge bg-<?= $resposta->usuario_tipo === 'cliente' ? 'primary' : 'success' ?> ms-2">
                                    <?= ucfirst($resposta->usuario_tipo) ?>
                                </span>
                                <?php if ($resposta->interno): ?>
                                    <span class="badge bg-warning ms-1">
                                        <i class="fas fa-lock"></i> Nota Interna
                                    </span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($resposta->created_at)) ?>
                            </small>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <?= nl2br(esc($resposta->mensagem)) ?>
                        </div>

                        <?php
                        // Buscar anexos desta resposta
                        $anexosResposta = array_filter($anexos, function($a) use ($resposta) {
                            return $a->resposta_id == $resposta->id;
                        });
                        ?>
                        <?php if (!empty($anexosResposta)): ?>
                        <div class="mt-2">
                            <small class="text-muted"><i class="fas fa-paperclip"></i> Anexos:</small>
                            <?php foreach ($anexosResposta as $anexo): ?>
                                <a href="<?= base_url('chamados/download/' . $anexo->id) ?>" class="btn btn-sm btn-outline-primary ms-1">
                                    <i class="fas fa-download"></i> <?= esc($anexo->nome_original) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center mb-0">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        Nenhuma resposta ainda
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulário de Resposta -->
        <?php if ($chamado->status !== 'finalizado'): ?>
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-reply me-2"></i>
                Adicionar Resposta
            </div>
            <div class="card-body">
                <form action="<?= base_url('chamados/responder/' . $chamado->id) ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <textarea class="form-control" name="mensagem" rows="4" required placeholder="Digite sua resposta..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Anexar Arquivos (opcional)</label>
                        <input type="file" class="form-control" name="anexos[]" multiple>
                    </div>

                    <?php if (in_array($usuarioLogado->tipo, ['admin', 'atendente'])): ?>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interno" id="notaInterna">
                            <label class="form-check-label" for="notaInterna">
                                <i class="fas fa-lock"></i> Nota interna (visível apenas para equipe)
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>
                        Enviar Resposta
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Informações do Cliente -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-building me-2"></i>
                Cliente
            </div>
            <div class="card-body">
                <h6><?= esc($chamado->empresa_nome) ?></h6>
                <p class="mb-1">
                    <i class="fas fa-user me-2"></i>
                    <?= esc($chamado->usuario_nome) ?>
                </p>
                <p class="mb-1">
                    <i class="fas fa-envelope me-2"></i>
                    <?= esc($chamado->usuario_email) ?>
                </p>
                <?php if ($chamado->usuario_telefone): ?>
                <p class="mb-0">
                    <i class="fas fa-phone me-2"></i>
                    <?= esc($chamado->usuario_telefone) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Atendente -->
        <?php if (in_array($usuarioLogado->tipo, ['admin', 'atendente'])): ?>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-user-tie me-2"></i>
                Atendente
            </div>
            <div class="card-body">
                <?php if ($chamado->atendente_id): ?>
                    <p class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <?= esc($chamado->atendente_nome) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Não atribuído
                    </p>
                    <form action="<?= base_url('chamados/atribuir/' . $chamado->id) ?>" method="post">
                        <?= csrf_field() ?>
                        <select name="atendente_id" class="form-select form-select-sm mb-2" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($atendentes as $atendente): ?>
                            <option value="<?= $atendente->id ?>">
                                <?= esc($atendente->nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            Atribuir
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ações -->
        <?php if ($chamado->status !== 'finalizado'): ?>
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-tools me-2"></i>
                Ações
            </div>
            <div class="card-body">
                <?php if ($chamado->usuario_id == $usuarioLogado->id): ?>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#modalFinalizar">
                        <i class="fas fa-check-circle me-2"></i>
                        Finalizar Chamado
                    </button>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle"></i> Apenas você pode finalizar este chamado
                    </small>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Apenas o cliente pode finalizar o chamado
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Avaliação -->
        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                Chamado Finalizado
            </div>
            <div class="card-body">
                <?php if ($chamado->avaliacao): ?>
                    <p class="mb-2"><strong>Avaliação:</strong></p>
                    <div class="mb-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?= $i <= $chamado->avaliacao ? 'text-warning' : 'text-muted' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <?php if ($chamado->comentario_avaliacao): ?>
                        <p class="mb-0"><strong>Comentário:</strong><br>
                        <?= nl2br(esc($chamado->comentario_avaliacao)) ?>
                        </p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">Sem avaliação</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Finalizar -->
<div class="modal fade" id="modalFinalizar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('chamados/finalizar/' . $chamado->id) ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>
                        Finalizar Chamado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Você está prestes a finalizar este chamado. Como foi o atendimento?</p>

                    <div class="mb-3">
                        <label class="form-label">Avaliação</label>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star star-rating text-muted" data-rating="<?= $i ?>" style="cursor: pointer; font-size: 2rem;"></i>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="avaliacao" id="avaliacaoInput" value="">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comentário (opcional)</label>
                        <textarea class="form-control" name="comentario_avaliacao" rows="3" placeholder="Deixe um comentário sobre o atendimento..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>
                        Finalizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Sistema de avaliação por estrelas
$(document).ready(function() {
    $('.star-rating').hover(function() {
        const rating = $(this).data('rating');
        $('.star-rating').each(function(index) {
            if (index < rating) {
                $(this).removeClass('text-muted').addClass('text-warning');
            } else {
                $(this).removeClass('text-warning').addClass('text-muted');
            }
        });
    });

    $('.star-rating').click(function() {
        const rating = $(this).data('rating');
        $('#avaliacaoInput').val(rating);
        $('.star-rating').removeClass('text-warning').addClass('text-muted');
        $('.star-rating').each(function(index) {
            if (index < rating) {
                $(this).removeClass('text-muted').addClass('text-warning');
            }
        });
    });

    $('.rating-stars').mouseleave(function() {
        const currentRating = $('#avaliacaoInput').val();
        if (currentRating) {
            $('.star-rating').each(function(index) {
                if (index < currentRating) {
                    $(this).removeClass('text-muted').addClass('text-warning');
                } else {
                    $(this).removeClass('text-warning').addClass('text-muted');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
