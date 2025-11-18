<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-utensils me-2"></i>
        Gerenciar Cardápios
    </h2>
    <div>
        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalCardapio">
            <i class="fas fa-plus me-2"></i>
            Novo Cardápio
        </button>
    </div>
</div>

<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Dica:</strong> Os cardápios criados aqui ficarão disponíveis para avaliação dos clientes. Cliente avalia apenas por dia, sem visualizar a descrição detalhada na listagem.
</div>

<!-- Filtro de Empresa -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Selecione a Empresa</label>
                    <select name="empresa_id" class="form-select" required>
                        <option value="">Selecione uma empresa...</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa->id ?>"
                                    <?= $empresa_id_selecionada == $empresa->id ? 'selected' : '' ?>>
                                <?= esc($empresa->nome_fantasia) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($empresa_id_selecionada): ?>
    <?php if (empty($cardapios)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Nenhum cardápio cadastrado para esta empresa.
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Avaliações</th>
                                <th>Média</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cardapios as $cardapio): ?>
                            <tr>
                                <td>
                                    <strong><?= date('d/m/Y', strtotime($cardapio->data)) ?></strong>
                                </td>
                                <td>
                                    <small style="white-space: pre-line;">
                                        <?= substr(esc($cardapio->descricao), 0, 100) ?>...
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= $cardapio->total_avaliacoes ?> avaliações
                                    </span>
                                </td>
                                <td>
                                    <?php if ($cardapio->media_numerica): ?>
                                        <span class="badge bg-success">
                                            <?= number_format($cardapio->media_numerica, 1) ?>/4
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Sem avaliações</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editarCardapio(<?= htmlspecialchars(json_encode($cardapio)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?= base_url('avaliacoes/deletar-cardapio/' . $cardapio->id) ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Tem certeza que deseja deletar este cardápio?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Modal Novo/Editar Cardápio -->
<div class="modal fade" id="modalCardapio" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('avaliacoes/salvar-cardapio') ?>" method="POST" id="formCardapio">
                <?= csrf_field() ?>
                <input type="hidden" name="cardapio_id" id="cardapio_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Cardápio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Empresa <span class="text-danger">*</span></label>
                        <select name="empresa_id" id="modal_empresa_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= $empresa->id ?>"
                                        <?= $empresa_id_selecionada == $empresa->id ? 'selected' : '' ?>>
                                    <?= esc($empresa->nome_fantasia) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" name="data" id="data" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição do Cardápio <span class="text-danger">*</span></label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="10" required
                                  placeholder="Exemplo:&#10;**Prato Principal:** Frango grelhado&#10;**Acompanhamentos:** Arroz, feijão, batata&#10;**Saladas:** Alface, tomate&#10;**Sobremesa:** Fruta"></textarea>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Use **texto** para negrito.
                            <strong>Importante:</strong> Cliente verá esta descrição apenas ao avaliar.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editarCardapio(cardapio) {
    $('#modalTitle').text('Editar Cardápio');
    $('#formCardapio').attr('action', '<?= base_url('avaliacoes/salvar-cardapio') ?>/' + cardapio.id);
    $('#cardapio_id').val(cardapio.id);
    $('#modal_empresa_id').val(cardapio.empresa_id);
    $('#data').val(cardapio.data);
    $('#descricao').val(cardapio.descricao);
    $('#modalCardapio').modal('show');
}

// Resetar form ao abrir modal para novo
$('#modalCardapio').on('show.bs.modal', function (e) {
    if (!$(e.relatedTarget).data('edit')) {
        $('#modalTitle').text('Novo Cardápio');
        $('#formCardapio').attr('action', '<?= base_url('avaliacoes/salvar-cardapio') ?>');
        $('#formCardapio')[0].reset();
        $('#cardapio_id').val('');
        <?php if ($empresa_id_selecionada): ?>
        $('#modal_empresa_id').val('<?= $empresa_id_selecionada ?>');
        <?php endif; ?>
    }
});
</script>
<?= $this->endSection() ?>
