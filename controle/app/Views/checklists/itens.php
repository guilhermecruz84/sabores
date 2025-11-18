<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-list-check me-2"></i>
        Gerenciar Itens do Checklist
    </h2>
    <a href="<?= base_url('checklists/relatorio') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar ao Relatório
    </a>
</div>

<p class="text-muted mb-4">Configure as perguntas que os operadores devem responder nos checklists de abertura e encerramento.</p>

<!-- Checklists de Abertura -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="fas fa-sun me-2"></i><strong>Itens do Checklist de Abertura</strong></span>
        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoItem" onclick="setTipoItem('abertura')">
            <i class="fas fa-plus me-1"></i> Adicionar Item
        </button>
    </div>
    <div class="card-body">
        <?php if (!empty($itensAbertura)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Ordem</th>
                        <th width="45%">Descrição</th>
                        <th width="12%">Tipo Resposta</th>
                        <th width="8%">Obrigatório</th>
                        <th width="8%">Foto</th>
                        <th width="8%">Status</th>
                        <th width="14%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itensAbertura as $item): ?>
                    <tr>
                        <td><strong><?= $item->ordem ?></strong></td>
                        <td><?= esc($item->descricao) ?></td>
                        <td>
                            <?php
                            $tiposResposta = [
                                'sim_nao' => 'Sim/Não',
                                'texto' => 'Texto',
                                'numero' => 'Número',
                                'multipla_escolha' => 'Múltipla Escolha'
                            ];
                            ?>
                            <span class="badge bg-info"><?= $tiposResposta[$item->tipo_resposta] ?></span>
                        </td>
                        <td>
                            <?php if ($item->obrigatorio): ?>
                                <span class="badge bg-danger">Sim</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item->requer_foto): ?>
                                <span class="badge bg-primary"><i class="fas fa-camera"></i></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item->ativo): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarItem(<?= htmlspecialchars(json_encode($item)) ?>)" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="<?= base_url('checklists/itens/toggle/' . $item->id) ?>"
                               class="btn btn-sm btn-<?= $item->ativo ? 'secondary' : 'success' ?>"
                               onclick="return confirm('Deseja <?= $item->ativo ? 'desativar' : 'ativar' ?> este item?')"
                               title="<?= $item->ativo ? 'Desativar' : 'Ativar' ?>">
                                <i class="fas fa-<?= $item->ativo ? 'toggle-off' : 'toggle-on' ?>"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted text-center py-4">Nenhum item cadastrado para checklist de abertura</p>
        <?php endif; ?>
    </div>
</div>

<!-- Checklists de Encerramento -->
<div class="card mb-4">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <span><i class="fas fa-moon me-2"></i><strong>Itens do Checklist de Encerramento</strong></span>
        <button class="btn btn-dark btn-sm" data-bs-toggle="modal" data-bs-target="#modalNovoItem" onclick="setTipoItem('encerramento')">
            <i class="fas fa-plus me-1"></i> Adicionar Item
        </button>
    </div>
    <div class="card-body">
        <?php if (!empty($itensEncerramento)): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Ordem</th>
                        <th width="45%">Descrição</th>
                        <th width="12%">Tipo Resposta</th>
                        <th width="8%">Obrigatório</th>
                        <th width="8%">Foto</th>
                        <th width="8%">Status</th>
                        <th width="14%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itensEncerramento as $item): ?>
                    <tr>
                        <td><strong><?= $item->ordem ?></strong></td>
                        <td><?= esc($item->descricao) ?></td>
                        <td>
                            <?php
                            $tiposResposta = [
                                'sim_nao' => 'Sim/Não',
                                'texto' => 'Texto',
                                'numero' => 'Número',
                                'multipla_escolha' => 'Múltipla Escolha'
                            ];
                            ?>
                            <span class="badge bg-info"><?= $tiposResposta[$item->tipo_resposta] ?></span>
                        </td>
                        <td>
                            <?php if ($item->obrigatorio): ?>
                                <span class="badge bg-danger">Sim</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Não</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item->requer_foto): ?>
                                <span class="badge bg-primary"><i class="fas fa-camera"></i></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item->ativo): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarItem(<?= htmlspecialchars(json_encode($item)) ?>)" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="<?= base_url('checklists/itens/toggle/' . $item->id) ?>"
                               class="btn btn-sm btn-<?= $item->ativo ? 'secondary' : 'success' ?>"
                               onclick="return confirm('Deseja <?= $item->ativo ? 'desativar' : 'ativar' ?> este item?')"
                               title="<?= $item->ativo ? 'Desativar' : 'Ativar' ?>">
                                <i class="fas fa-<?= $item->ativo ? 'toggle-off' : 'toggle-on' ?>"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="text-muted text-center py-4">Nenhum item cadastrado para checklist de encerramento</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Novo Item -->
<div class="modal fade" id="modalNovoItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('checklists/itens/criar') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Novo Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tipo" id="novoTipo">

                    <div class="mb-3">
                        <label class="form-label">Ordem <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="ordem" min="1" required>
                        <small class="text-muted">Define a posição da pergunta no checklist</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição da Pergunta <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descricao" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Resposta <span class="text-danger">*</span></label>
                        <select class="form-select" name="tipo_resposta" required>
                            <option value="sim_nao">Sim/Não (Conforme/Não Conforme)</option>
                            <option value="texto">Texto</option>
                            <option value="numero">Número</option>
                        </select>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="obrigatorio" value="1" id="novoObrigatorio" checked>
                        <label class="form-check-label" for="novoObrigatorio">
                            Pergunta obrigatória
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requer_foto" value="1" id="novoRequerFoto">
                        <label class="form-check-label" for="novoRequerFoto">
                            <i class="fas fa-camera me-1"></i> Requer foto (obrigatória)
                        </label>
                        <small class="text-muted d-block">Se marcado, o operador deverá enviar uma foto ao preencher este item</small>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="gera_alerta" value="1" id="novoGeraAlerta">
                        <label class="form-check-label" for="novoGeraAlerta">
                            <i class="fas fa-exclamation-triangle me-1 text-danger"></i> <strong>Gera Alerta</strong>
                        </label>
                        <small class="text-muted d-block">Se marcado, ao responder "Não Conforme" será gerado um alerta para Admin/Administrativo</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Item -->
<div class="modal fade" id="modalEditarItem" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post" id="formEditarItem">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Editar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ordem <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="ordem" id="editOrdem" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição da Pergunta <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="descricao" id="editDescricao" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Resposta <span class="text-danger">*</span></label>
                        <select class="form-select" name="tipo_resposta" id="editTipoResposta" required>
                            <option value="sim_nao">Sim/Não (Conforme/Não Conforme)</option>
                            <option value="texto">Texto</option>
                            <option value="numero">Número</option>
                        </select>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="obrigatorio" value="1" id="editObrigatorio">
                        <label class="form-check-label" for="editObrigatorio">
                            Pergunta obrigatória
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requer_foto" value="1" id="editRequerFoto">
                        <label class="form-check-label" for="editRequerFoto">
                            <i class="fas fa-camera me-1"></i> Requer foto (obrigatória)
                        </label>
                        <small class="text-muted d-block">Se marcado, o operador deverá enviar uma foto ao preencher este item</small>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="gera_alerta" value="1" id="editGeraAlerta">
                        <label class="form-check-label" for="editGeraAlerta">
                            <i class="fas fa-exclamation-triangle me-1 text-danger"></i> <strong>Gera Alerta</strong>
                        </label>
                        <small class="text-muted d-block">Se marcado, ao responder "Não Conforme" será gerado um alerta para Admin/Administrativo</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function setTipoItem(tipo) {
    document.getElementById('novoTipo').value = tipo;
}

function editarItem(item) {
    document.getElementById('formEditarItem').action = '<?= base_url('checklists/itens/editar/') ?>' + item.id;
    document.getElementById('editOrdem').value = item.ordem;
    document.getElementById('editDescricao').value = item.descricao;
    document.getElementById('editTipoResposta').value = item.tipo_resposta;
    document.getElementById('editObrigatorio').checked = item.obrigatorio == 1;
    document.getElementById('editRequerFoto').checked = item.requer_foto == 1;
    document.getElementById('editGeraAlerta').checked = item.gera_alerta == 1;

    const modal = new bootstrap.Modal(document.getElementById('modalEditarItem'));
    modal.show();
}
</script>
<?= $this->endSection() ?>
