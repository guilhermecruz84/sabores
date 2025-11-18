<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-eye me-2"></i>
        Visualizar Checklist
    </h2>
    <div>
        <button class="btn btn-secondary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>
            Imprimir
        </button>
        <a href="<?= base_url('checklists') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Voltar
        </a>
    </div>
</div>

<!-- Informações do Checklist -->
<div class="card mb-4">
    <div class="card-header bg-<?= $registro->tipo === 'abertura' ? 'primary' : 'warning' ?> text-<?= $registro->tipo === 'abertura' ? 'white' : 'dark' ?>">
        <strong>Informações do Checklist</strong>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Empresa:</strong><br>
                <?= esc($registro->empresa_nome) ?>
            </div>
            <div class="col-md-3">
                <strong>Operador:</strong><br>
                <?= esc($registro->operador_nome) ?>
            </div>
            <div class="col-md-2">
                <strong>Data:</strong><br>
                <?= date('d/m/Y', strtotime($registro->data)) ?>
            </div>
            <div class="col-md-2">
                <strong>Tipo:</strong><br>
                <span class="badge bg-<?= $registro->tipo === 'abertura' ? 'primary' : 'warning' ?>">
                    <?= ucfirst($registro->tipo) ?>
                </span>
            </div>
            <div class="col-md-2">
                <strong>Status:</strong><br>
                <span class="badge bg-success">Finalizado</span>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <strong>Criado em:</strong><br>
                <?= date('d/m/Y H:i', strtotime($registro->created_at)) ?>
            </div>
            <div class="col-md-6">
                <strong>Finalizado em:</strong><br>
                <?= $registro->finalizado_em ? date('d/m/Y H:i', strtotime($registro->finalizado_em)) : '-' ?>
            </div>
        </div>
    </div>
</div>

<!-- Respostas -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-list-check me-2"></i>
        Respostas do Checklist
    </div>
    <div class="card-body">
        <?php if (!empty($respostas)): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="50%">Pergunta</th>
                            <th width="12%">Conforme</th>
                            <th width="23%">Resposta/Observação</th>
                            <th width="15%">Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($respostas as $resposta): ?>
                        <tr class="<?= $resposta->conforme === 0 ? 'table-danger' : '' ?>">
                            <td><?= esc($resposta->descricao) ?></td>
                            <td class="text-center">
                                <?php if ($resposta->tipo_resposta === 'sim_nao'): ?>
                                    <?php if ($resposta->conforme === 1): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Sim
                                        </span>
                                    <?php elseif ($resposta->conforme === 0): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Não
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($resposta->resposta): ?>
                                    <?php if ($resposta->tipo_resposta === 'numero' && strpos(strtolower($resposta->descricao), 'temperatura') !== false): ?>
                                        <strong><?= esc($resposta->resposta) ?>°C</strong>
                                        <?php
                                        $temp = floatval($resposta->resposta);
                                        if (strpos(strtolower($resposta->descricao), 'quente') !== false):
                                            if ($temp < 60):
                                        ?>
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-exclamation-triangle"></i> Abaixo de 60°C
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-check"></i> OK
                                            </span>
                                        <?php
                                            endif;
                                        elseif (strpos(strtolower($resposta->descricao), 'frio') !== false):
                                            if ($temp > 10):
                                        ?>
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-exclamation-triangle"></i> Acima de 10°C
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-check"></i> OK
                                            </span>
                                        <?php
                                            endif;
                                        endif;
                                        ?>
                                    <?php else: ?>
                                        <?= esc($resposta->resposta) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($resposta->foto_path)): ?>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalFoto"
                                       onclick="showFoto('<?= base_url($resposta->foto_path) ?>', '<?= esc($resposta->descricao) ?>')">
                                        <img src="<?= base_url($resposta->foto_path) ?>"
                                             alt="Foto"
                                             class="img-thumbnail"
                                             style="max-width: 100px; max-height: 80px; cursor: pointer;"
                                             onerror="this.parentElement.innerHTML='<button class=\'btn btn-sm btn-info\'><i class=\'fas fa-camera\'></i> Ver</button>'">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">Nenhuma resposta registrada.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Sobras e Faltas (apenas para encerramento) -->
<?php if ($registro->tipo === 'encerramento' && !empty($produtos)): ?>
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <i class="fas fa-boxes me-2"></i>
        Controle de Sobras e Faltas
    </div>
    <div class="card-body">
        <?php
        $sobras = array_filter($produtos, fn($p) => $p->tipo_registro === 'sobra');
        $faltas = array_filter($produtos, fn($p) => $p->tipo_registro === 'falta');
        ?>

        <?php if (!empty($sobras)): ?>
        <h5 class="text-success mb-3">
            <i class="fas fa-plus-circle me-2"></i>
            Sobras de Alimentos
        </h5>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Quantidade</th>
                        <th>Observação</th>
                        <th width="120">Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sobras as $sobra): ?>
                    <tr>
                        <td><strong><?= esc($sobra->item) ?></strong></td>
                        <td><?= esc($sobra->quantidade ?? '-') ?></td>
                        <td><?= esc($sobra->observacao ?? '-') ?></td>
                        <td class="text-center">
                            <?php if (!empty($sobra->foto)): ?>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalFoto" onclick="showFoto('<?= base_url($sobra->foto) ?>', '<?= esc($sobra->item) ?>')">
                                    <img src="<?= base_url($sobra->foto) ?>" alt="Foto" class="img-thumbnail" style="max-width: 80px; max-height: 80px; cursor: pointer;">
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (!empty($faltas)): ?>
        <h5 class="text-danger mb-3">
            <i class="fas fa-minus-circle me-2"></i>
            Faltas de Alimentos
        </h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Quantidade</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faltas as $falta): ?>
                    <tr>
                        <td><strong><?= esc($falta->item) ?></strong></td>
                        <td><?= esc($falta->quantidade ?? '-') ?></td>
                        <td><?= esc($falta->observacao ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (empty($sobras) && empty($faltas)): ?>
            <p class="text-muted">Nenhuma sobra ou falta registrada.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Observações Gerais -->
<?php if ($registro->observacoes): ?>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-comment me-2"></i>
        Observações Gerais
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(esc($registro->observacoes)) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Modal para visualizar foto -->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFotoTitulo">Foto da Sobra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalFotoImagem" src="" alt="Foto" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showFoto(url, item) {
    document.getElementById('modalFotoTitulo').textContent = 'Foto: ' + item;
    document.getElementById('modalFotoImagem').src = url;
}
</script>
<?= $this->endSection() ?>
