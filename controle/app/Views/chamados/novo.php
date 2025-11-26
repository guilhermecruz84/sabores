<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="mb-0">
        <i class="fas fa-plus-circle me-2"></i>
        Novo Chamado
    </h2>
    <p class="text-muted">Abra uma ocorrência ou solicitação</p>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-edit me-2"></i>
                Informações do Chamado
            </div>
            <div class="card-body">
                <form action="<?= base_url('chamados/criar') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Tipo de Chamado *</label>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipoOcorrencia" value="ocorrencia" required <?= old('tipo') === 'ocorrencia' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tipoOcorrencia">
                                        <div class="card border-danger">
                                            <div class="card-body text-center">
                                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-2"></i>
                                                <h5>Ocorrência</h5>
                                                <small class="text-muted">Problemas, reclamações, falhas</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo" id="tipoSolicitacao" value="solicitacao" required <?= old('tipo') === 'solicitacao' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tipoSolicitacao">
                                        <div class="card border-info">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-alt fa-3x text-info mb-2"></i>
                                                <h5>Solicitação</h5>
                                                <small class="text-muted">Pedidos, informações, mudanças</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Categoria</label>
                        <select name="categoria" class="form-select" id="categoriaSelect">
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?= esc($categoria->nome) ?>"
                                    data-tipo="<?= $categoria->tipo ?>"
                                    <?= old('categoria') === $categoria->nome ? 'selected' : '' ?>>
                                <i class="<?= esc($categoria->icone) ?>"></i>
                                <?= esc($categoria->nome) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assunto *</label>
                        <input type="text" class="form-control form-control-lg" name="assunto" required placeholder="Ex: Refeição com temperatura inadequada" value="<?= old('assunto') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição Detalhada *</label>
                        <textarea class="form-control" name="descricao" rows="6" required placeholder="Descreva detalhadamente o problema ou solicitação..."><?= old('descricao') ?></textarea>
                        <small class="text-muted">Seja o mais específico possível para agilizar o atendimento</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prioridade *</label>
                        <select name="prioridade" class="form-select" required>
                            <option value="baixa" <?= old('prioridade') === 'baixa' ? 'selected' : '' ?>>
                                🟢 Baixa - Não urgente
                            </option>
                            <option value="media" <?= old('prioridade') === 'media' || !old('prioridade') ? 'selected' : '' ?>>
                                🔵 Média - Normal
                            </option>
                            <option value="alta" <?= old('prioridade') === 'alta' ? 'selected' : '' ?>>
                                🟠 Alta - Importante
                            </option>
                            <option value="urgente" <?= old('prioridade') === 'urgente' ? 'selected' : '' ?>>
                                🔴 Urgente - Requer atenção imediata
                            </option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Anexar Arquivos/Fotos (opcional)</label>
                        <input type="file" class="form-control" name="anexos[]" multiple accept="image/*,.pdf,.doc,.docx">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Você pode anexar fotos ou documentos. Máximo 5MB por arquivo.
                        </small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>
                            Abrir Chamado
                        </button>
                        <a href="<?= base_url('chamados') ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Filtrar categorias baseado no tipo selecionado
    $('input[name="tipo"]').change(function() {
        const tipoSelecionado = $(this).val();
        const categoriaSelect = $('#categoriaSelect');

        categoriaSelect.find('option').each(function() {
            const categoriaTipo = $(this).data('tipo');

            if (categoriaTipo === 'ambos' || categoriaTipo === tipoSelecionado || $(this).val() === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        // Resetar seleção se não for compatível
        const selectedOption = categoriaSelect.find('option:selected');
        const selectedTipo = selectedOption.data('tipo');

        if (selectedTipo && selectedTipo !== 'ambos' && selectedTipo !== tipoSelecionado) {
            categoriaSelect.val('');
        }
    });

    // Estilizar radio buttons
    $('.form-check-label .card').click(function() {
        $(this).closest('.form-check').find('input[type="radio"]').prop('checked', true).trigger('change');
    });

    $('input[name="tipo"]').change(function() {
        $('.form-check-label .card').removeClass('border-3');
        if ($(this).is(':checked')) {
            $(this).siblings('label').find('.card').addClass('border-3');
        }
    });
});
</script>
<?= $this->endSection() ?>
