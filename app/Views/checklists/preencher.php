<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<style>
.foto-preview .card {
    border: 2px solid #28a745;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.foto-preview .preview-img {
    max-height: 300px;
    object-fit: contain;
    background-color: #f8f9fa;
}

.btn-capturar-foto {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.btn-capturar-foto:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    color: white;
}

.foto-container {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

@media (max-width: 768px) {
    .btn-capturar-foto,
    .btn-escolher-arquivo {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-<?= $registro->tipo === 'abertura' ? 'sun' : 'moon' ?> me-2"></i>
        Checklist de <?= ucfirst($registro->tipo) ?>
    </h2>
    <a href="<?= base_url('checklists') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="card mb-4">
    <div class="card-header bg-<?= $registro->tipo === 'abertura' ? 'primary' : 'warning' ?> text-<?= $registro->tipo === 'abertura' ? 'white' : 'dark' ?>">
        <strong>Informações do Checklist</strong>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <strong>Data:</strong><br>
                <?= date('d/m/Y', strtotime($registro->data)) ?>
            </div>
            <div class="col-md-3">
                <strong>Tipo:</strong><br>
                <?= ucfirst($registro->tipo) ?>
            </div>
            <div class="col-md-3">
                <strong>Status:</strong><br>
                <span class="badge bg-<?= $registro->status === 'finalizado' ? 'success' : 'warning' ?>">
                    <?= $registro->status === 'finalizado' ? 'Finalizado' : 'Em Andamento' ?>
                </span>
            </div>
            <div class="col-md-3">
                <strong>Criado em:</strong><br>
                <?= date('d/m/Y H:i', strtotime($registro->created_at)) ?>
            </div>
        </div>
    </div>
</div>

<form action="<?= base_url('checklists/salvar/' . $registro->id) ?>" method="post" id="formChecklist" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Perguntas do Checklist -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-list-check me-2"></i>
            Perguntas
        </div>
        <div class="card-body">
            <?php foreach ($itens as $item): ?>
            <div class="mb-4 pb-3 border-bottom">
                <label class="form-label">
                    <strong><?= $item->ordem ?>. <?= esc($item->descricao) ?></strong>
                    <?php if ($item->obrigatorio): ?>
                        <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>

                <?php
                $respostaAtual = $respostasMap[$item->id] ?? null;
                ?>

                <?php if ($item->tipo_resposta === 'sim_nao'): ?>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="respostas[<?= $item->id ?>][conforme]"
                                   id="item_<?= $item->id ?>_sim"
                                   value="1"
                                   <?= $respostaAtual && $respostaAtual->conforme == 1 ? 'checked' : '' ?>
                                   <?= $item->obrigatorio ? 'required' : '' ?>>
                            <label class="form-check-label text-success" for="item_<?= $item->id ?>_sim">
                                <i class="fas fa-check-circle"></i> Sim / Conforme
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio"
                                   name="respostas[<?= $item->id ?>][conforme]"
                                   id="item_<?= $item->id ?>_nao"
                                   value="0"
                                   <?= $respostaAtual && $respostaAtual->conforme == 0 ? 'checked' : '' ?>
                                   <?= $item->obrigatorio ? 'required' : '' ?>>
                            <label class="form-check-label text-danger" for="item_<?= $item->id ?>_nao">
                                <i class="fas fa-times-circle"></i> Não / Não Conforme
                            </label>
                        </div>
                    </div>

                <?php elseif ($item->tipo_resposta === 'texto'): ?>
                    <input type="text" class="form-control"
                           name="respostas[<?= $item->id ?>][resposta]"
                           value="<?= $respostaAtual ? esc($respostaAtual->resposta) : '' ?>"
                           placeholder="Digite sua resposta..."
                           <?= $item->obrigatorio ? 'required' : '' ?>>

                <?php elseif ($item->tipo_resposta === 'numero'): ?>
                    <div class="input-group" style="max-width: 300px;">
                        <input type="number" class="form-control"
                               name="respostas[<?= $item->id ?>][resposta]"
                               value="<?= $respostaAtual ? esc($respostaAtual->resposta) : '' ?>"
                               placeholder="Ex: 65"
                               step="0.1"
                               <?= $item->obrigatorio ? 'required' : '' ?>>
                        <?php if (strpos(strtolower($item->descricao), 'temperatura') !== false): ?>
                        <span class="input-group-text">°C</span>
                        <?php endif; ?>
                    </div>
                    <?php
                    // Mostrar alerta visual se for temperatura
                    if (strpos(strtolower($item->descricao), 'temperatura') !== false):
                        $valorAtual = $respostaAtual ? floatval($respostaAtual->resposta) : null;
                        if ($valorAtual !== null):
                            if (strpos(strtolower($item->descricao), 'quente') !== false && $valorAtual < 60):
                    ?>
                                <div class="alert alert-danger mt-2 mb-0 py-1 px-2 small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Atenção:</strong> Temperatura abaixo do mínimo (60°C)
                                </div>
                    <?php
                            elseif (strpos(strtolower($item->descricao), 'frio') !== false && $valorAtual > 10):
                    ?>
                                <div class="alert alert-danger mt-2 mb-0 py-1 px-2 small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Atenção:</strong> Temperatura acima do máximo (10°C)
                                </div>
                    <?php
                            endif;
                        endif;
                    endif;
                    ?>
                <?php endif; ?>

                <!-- Campo de foto (se requerido) -->
                <?php if ($item->requer_foto): ?>
                <div class="mt-3 foto-container">
                    <label class="form-label">
                        <i class="fas fa-camera me-1"></i>
                        <strong>Foto <?= $item->obrigatorio ? '(Obrigatória)' : '(Opcional)' ?></strong>
                        <?php if ($item->obrigatorio): ?>
                        <span class="text-danger">*</span>
                        <?php endif; ?>
                    </label>

                    <!-- Botão para abrir câmera -->
                    <div class="mb-2">
                        <button type="button" class="btn btn-primary btn-capturar-foto" data-item-id="<?= $item->id ?>">
                            <i class="fas fa-camera me-2"></i>
                            Tirar Foto
                        </button>
                        <button type="button" class="btn btn-secondary btn-escolher-arquivo" data-item-id="<?= $item->id ?>">
                            <i class="fas fa-file-upload me-2"></i>
                            Escolher Arquivo
                        </button>
                    </div>

                    <!-- Input file (oculto) -->
                    <input type="file" class="d-none input-foto"
                           name="fotos[<?= $item->id ?>]"
                           id="foto_<?= $item->id ?>"
                           accept="image/*"
                           capture="environment"
                           data-item-id="<?= $item->id ?>"
                           <?= $item->obrigatorio && !($respostaAtual && $respostaAtual->foto_path) ? 'required' : '' ?>>

                    <!-- Preview da foto -->
                    <div class="foto-preview mt-2" id="preview_<?= $item->id ?>" style="display: none;">
                        <div class="card" style="max-width: 300px;">
                            <img src="" class="card-img-top preview-img" alt="Preview da foto">
                            <div class="card-body p-2">
                                <button type="button" class="btn btn-sm btn-danger btn-remover-foto" data-item-id="<?= $item->id ?>">
                                    <i class="fas fa-trash me-1"></i> Remover Foto
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if ($respostaAtual && $respostaAtual->foto_path): ?>
                        <div class="mt-2 foto-atual">
                            <strong class="text-success">
                                <i class="fas fa-check-circle me-1"></i> Foto já cadastrada
                            </strong><br>
                            <a href="<?= base_url($respostaAtual->foto_path) ?>" target="_blank" class="btn btn-sm btn-info mt-1">
                                <i class="fas fa-eye me-1"></i> Ver Foto Atual
                            </a>
                            <small class="text-muted d-block mt-1">Tire uma nova foto para substituir</small>
                        </div>
                    <?php endif; ?>

                    <small class="text-muted d-block mt-2">Formatos aceitos: JPG, PNG. Máximo: 5MB</small>
                </div>
                <?php endif; ?>

                <!-- Campo de observação para não conformidades -->
                <div class="mt-2" id="obs_<?= $item->id ?>" style="display: <?= $respostaAtual && $respostaAtual->conforme == 0 ? 'block' : 'none' ?>;">
                    <label class="form-label small">Observação (descreva o problema):</label>
                    <textarea class="form-control" rows="2"
                              name="respostas[<?= $item->id ?>][resposta]"
                              placeholder="Descreva a não conformidade..."><?= $respostaAtual && $respostaAtual->conforme == 0 ? esc($respostaAtual->resposta) : '' ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sobras e Faltas (apenas para encerramento) -->
    <?php if ($registro->tipo === 'encerramento'): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-boxes me-2"></i>
            Controle de Sobras e Faltas
        </div>
        <div class="card-body">
            <!-- Sobras -->
            <h5 class="mb-3">
                <i class="fas fa-plus-circle text-success me-2"></i>
                Sobras de Alimentos
            </h5>
            <div id="sobras-container">
                <?php
                $sobras = array_filter($produtos, fn($p) => $p->tipo_registro === 'sobra');
                if (empty($sobras)) {
                    $sobras = [(object)['item' => '', 'quantidade' => '', 'observacao' => '']];
                }
                ?>
                <?php foreach ($sobras as $idx => $sobra): ?>
                <div class="row mb-3 sobra-item border rounded p-2 bg-light">
                    <div class="col-md-3">
                        <label class="form-label small">Item</label>
                        <input type="text" class="form-control sobra-item-input" name="sobras[<?= $idx ?>][item]"
                               list="datalist-sobras" placeholder="Item (ex: Arroz, Feijão...)" value="<?= esc($sobra->item) ?>"
                               autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Quantidade</label>
                        <input type="text" class="form-control" name="sobras[<?= $idx ?>][quantidade]"
                               placeholder="Quantidade estimada" value="<?= esc($sobra->quantidade ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Observação</label>
                        <input type="text" class="form-control" name="sobras[<?= $idx ?>][observacao]"
                               placeholder="Observação" value="<?= esc($sobra->observacao ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Foto</label>
                        <input type="file" class="form-control" name="sobras_foto_<?= $idx ?>" accept="image/*">
                        <?php if (!empty($sobra->foto)): ?>
                            <small class="text-success">
                                <i class="fas fa-check"></i> Foto anexada
                            </small>
                            <input type="hidden" name="sobras[<?= $idx ?>][foto_atual]" value="<?= esc($sobra->foto) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-sobra w-100" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-2" id="add-sobra">
                <i class="fas fa-plus me-1"></i> Adicionar Sobra
            </button>

            <hr class="my-4">

            <!-- Faltas -->
            <h5 class="mb-3">
                <i class="fas fa-minus-circle text-danger me-2"></i>
                Faltas de Alimentos
            </h5>
            <div id="faltas-container">
                <?php
                $faltas = array_filter($produtos, fn($p) => $p->tipo_registro === 'falta');
                if (empty($faltas)) {
                    $faltas = [(object)['item' => '', 'quantidade' => '', 'observacao' => '']];
                }
                ?>
                <?php foreach ($faltas as $idx => $falta): ?>
                <div class="row mb-2 falta-item">
                    <div class="col-md-4">
                        <input type="text" class="form-control falta-item-input" name="faltas[<?= $idx ?>][item]"
                               list="datalist-faltas" placeholder="Item (ex: Arroz, Feijão...)" value="<?= esc($falta->item) ?>"
                               autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="faltas[<?= $idx ?>][quantidade]"
                               placeholder="Quantidade estimada" value="<?= esc($falta->quantidade ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="faltas[<?= $idx ?>][observacao]"
                               placeholder="Observação" value="<?= esc($falta->observacao ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-falta" title="Remover">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-success btn-sm mt-2" id="add-falta">
                <i class="fas fa-plus me-1"></i> Adicionar Falta
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Observações Gerais -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-comment me-2"></i>
            Observações Gerais
        </div>
        <div class="card-body">
            <textarea class="form-control" name="observacoes" rows="4"
                      placeholder="Adicione observações gerais sobre este checklist..."><?= esc($registro->observacoes ?? '') ?></textarea>
        </div>
    </div>

    <!-- Botões -->
    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>
            Salvar Rascunho
        </button>
        <button type="submit" name="finalizar" value="1" class="btn btn-success">
            <i class="fas fa-check-circle me-2"></i>
            Salvar e Finalizar
        </button>
        <a href="<?= base_url('checklists') ?>" class="btn btn-secondary">
            <i class="fas fa-times me-2"></i>
            Cancelar
        </a>
    </div>
</form>

<!-- Datalists para autocompletar -->
<datalist id="datalist-sobras"></datalist>
<datalist id="datalist-faltas"></datalist>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Carregar sugestões de autocompletar para sobras e faltas
    function carregarSugestoes(tipo, datalistId) {
        $.ajax({
            url: '<?= base_url('checklists/getItensAutocompletar') ?>',
            method: 'GET',
            data: { tipo: tipo },
            success: function(response) {
                const datalist = document.getElementById(datalistId);
                datalist.innerHTML = '';
                response.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item;
                    datalist.appendChild(option);
                });
            }
        });
    }

    // Carregar sugestões ao iniciar a página
    carregarSugestoes('sobra', 'datalist-sobras');
    carregarSugestoes('falta', 'datalist-faltas');

    // Mostrar/ocultar campo de observação para não conformidades
    $('input[type="radio"][name*="[conforme]"]').on('change', function() {
        const itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
        const isNaoConforme = $(this).val() === '0' && $(this).is(':checked');
        $(`#obs_${itemId}`).toggle(isNaoConforme);
    });

    // Adicionar sobra
    let sobraIndex = <?= count($sobras ?? [1]) ?>;
    $('#add-sobra').click(function() {
        const html = `
            <div class="row mb-3 sobra-item border rounded p-2 bg-light">
                <div class="col-md-3">
                    <label class="form-label small">Item</label>
                    <input type="text" class="form-control sobra-item-input" name="sobras[${sobraIndex}][item]"
                           list="datalist-sobras" placeholder="Item (ex: Arroz, Feijão...)" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Quantidade</label>
                    <input type="text" class="form-control" name="sobras[${sobraIndex}][quantidade]" placeholder="Quantidade estimada">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Observação</label>
                    <input type="text" class="form-control" name="sobras[${sobraIndex}][observacao]" placeholder="Observação">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Foto</label>
                    <input type="file" class="form-control" name="sobras_foto_${sobraIndex}" accept="image/*">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-sobra w-100" title="Remover">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#sobras-container').append(html);
        sobraIndex++;
    });

    // Adicionar falta
    let faltaIndex = <?= count($faltas ?? [1]) ?>;
    $('#add-falta').click(function() {
        const html = `
            <div class="row mb-2 falta-item">
                <div class="col-md-4">
                    <input type="text" class="form-control falta-item-input" name="faltas[${faltaIndex}][item]"
                           list="datalist-faltas" placeholder="Item (ex: Arroz, Feijão...)" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="faltas[${faltaIndex}][quantidade]" placeholder="Quantidade estimada">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="faltas[${faltaIndex}][observacao]" placeholder="Observação">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-falta" title="Remover">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#faltas-container').append(html);
        faltaIndex++;
    });

    // Remover sobra
    $(document).on('click', '.remove-sobra', function() {
        if ($('.sobra-item').length > 1) {
            $(this).closest('.sobra-item').remove();
        }
    });

    // Remover falta
    $(document).on('click', '.remove-falta', function() {
        if ($('.falta-item').length > 1) {
            $(this).closest('.falta-item').remove();
        }
    });

    // Confirmação ao finalizar
    $('button[name="finalizar"]').click(function(e) {
        if (!confirm('Tem certeza que deseja finalizar este checklist? Após finalizado não poderá ser editado.')) {
            e.preventDefault();
        }
    });

    // ========== FUNCIONALIDADE DE CAPTURA DE FOTO ==========

    // Botão "Tirar Foto" - abre a câmera
    $('.btn-capturar-foto').click(function() {
        const itemId = $(this).data('item-id');
        const input = $(`#foto_${itemId}`);
        input.attr('capture', 'environment'); // Garante que abre a câmera
        input.click();
    });

    // Botão "Escolher Arquivo" - abre galeria
    $('.btn-escolher-arquivo').click(function() {
        const itemId = $(this).data('item-id');
        const input = $(`#foto_${itemId}`);
        input.removeAttr('capture'); // Remove capture para abrir galeria
        input.click();
    });

    // Quando uma foto é selecionada/capturada
    $('.input-foto').on('change', function() {
        const itemId = $(this).data('item-id');
        const file = this.files[0];

        if (file) {
            // Validar tamanho (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Arquivo muito grande! O tamanho máximo é 5MB.');
                $(this).val('');
                return;
            }

            // Validar tipo
            if (!file.type.match('image.*')) {
                alert('Por favor, selecione apenas imagens (JPG, PNG).');
                $(this).val('');
                return;
            }

            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = $(`#preview_${itemId}`);
                preview.find('.preview-img').attr('src', e.target.result);
                preview.show();
            };
            reader.readAsDataURL(file);

            // Remover "required" se tinha
            $(this).removeAttr('required');
        }
    });

    // Remover foto
    $('.btn-remover-foto').click(function() {
        const itemId = $(this).data('item-id');
        const input = $(`#foto_${itemId}`);
        const preview = $(`#preview_${itemId}`);

        // Limpar input e preview
        input.val('');
        preview.hide();
        preview.find('.preview-img').attr('src', '');

        // Verificar se foto é obrigatória (e não tem foto antiga)
        const container = input.closest('.foto-container');
        const temFotoAtual = container.find('.foto-atual').length > 0;
        const isObrigatorio = input.closest('.mb-4').find('.text-danger').length > 0;

        if (isObrigatorio && !temFotoAtual) {
            input.attr('required', 'required');
        }
    });
});
</script>
<?= $this->endSection() ?>
