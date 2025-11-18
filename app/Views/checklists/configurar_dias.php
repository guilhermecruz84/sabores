<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-calendar-alt me-2"></i>
        Configurar Dias da Semana
    </h2>
    <a href="<?= base_url('checklists/itens') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-plus-circle me-2"></i>
                Nova Configuração
            </div>
            <div class="card-body">
                <form action="<?= base_url('checklists/salvar-configuracao') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Empresa</label>
                        <select name="empresa_id" class="form-select" required id="empresaSelect">
                            <option value="">Selecione a empresa</option>
                            <?php foreach ($empresas as $empresa): ?>
                                <option value="<?= $empresa->id ?>"><?= esc($empresa->nome_fantasia) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Checklist</label>
                        <select name="tipo" class="form-select" required id="tipoSelect">
                            <option value="">Selecione o tipo</option>
                            <option value="abertura">Abertura</option>
                            <option value="encerramento">Encerramento</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Dias da Semana Permitidos</label>
                        <div class="border rounded p-3">
                            <?php foreach ($diasSemana as $diaNum => $diaNome): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="dias[]"
                                           value="<?= $diaNum ?>" id="dia<?= $diaNum ?>">
                                    <label class="form-check-label" for="dia<?= $diaNum ?>">
                                        <?= $diaNome ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Selecione os dias em que o checklist estará disponível</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Salvar Configuração
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <i class="fas fa-info-circle me-2"></i>
                Como funciona?
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Configure quais dias da semana cada empresa pode preencher os checklists</li>
                    <li>Se nenhuma configuração for criada, o checklist estará disponível todos os dias</li>
                    <li>Cada empresa pode ter configurações diferentes para abertura e encerramento</li>
                    <li>O operador só poderá criar checklists nos dias configurados</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>
                Configurações Atuais
            </div>
            <div class="card-body">
                <?php if (!empty($configMap)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Empresa</th>
                                    <th>Tipo</th>
                                    <th>Dias Permitidos</th>
                                    <th>Status</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $configuracoes = [];
                                foreach ($configMap as $config) {
                                    $configuracoes[] = $config;
                                }

                                // Agrupar por empresa
                                usort($configuracoes, function($a, $b) {
                                    return $a->empresa_id - $b->empresa_id;
                                });

                                foreach ($configuracoes as $config):
                                    $empresa = null;
                                    foreach ($empresas as $e) {
                                        if ($e->id == $config->empresa_id) {
                                            $empresa = $e;
                                            break;
                                        }
                                    }

                                    $diasIds = explode(',', $config->dias_semana);
                                    $diasNomes = [];
                                    foreach ($diasIds as $diaId) {
                                        if (isset($diasSemana[$diaId])) {
                                            $diasNomes[] = $diasSemana[$diaId];
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?= $empresa ? esc($empresa->nome_fantasia) : 'N/A' ?></td>
                                    <td>
                                        <span class="badge bg-<?= $config->tipo === 'abertura' ? 'primary' : 'warning' ?>">
                                            <?= ucfirst($config->tipo) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= implode(', ', $diasNomes) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $config->ativo ? 'success' : 'secondary' ?>">
                                            <?= $config->ativo ? 'Ativo' : 'Inativo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url("checklists/desativar-configuracao/{$config->id}") ?>"
                                           class="btn btn-sm btn-<?= $config->ativo ? 'warning' : 'success' ?>"
                                           title="<?= $config->ativo ? 'Desativar' : 'Ativar' ?>">
                                            <i class="fas fa-power-off"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Nenhuma configuração cadastrada. Crie a primeira configuração ao lado.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Pré-preencher dias ao selecionar empresa e tipo
$('#empresaSelect, #tipoSelect').on('change', function() {
    const empresaId = $('#empresaSelect').val();
    const tipo = $('#tipoSelect').val();

    if (empresaId && tipo) {
        // Buscar configuração existente
        const key = empresaId + '_' + tipo;
        const configMap = <?= json_encode($configMap) ?>;

        if (configMap[key]) {
            const config = configMap[key];
            const diasIds = config.dias_semana.split(',');

            // Desmarcar todos
            $('input[name="dias[]"]').prop('checked', false);

            // Marcar os dias configurados
            diasIds.forEach(function(diaId) {
                $('#dia' + diaId).prop('checked', true);
            });
        } else {
            // Configuração nova, desmarcar tudo
            $('input[name="dias[]"]').prop('checked', false);
        }
    }
});
</script>
<?= $this->endSection() ?>
