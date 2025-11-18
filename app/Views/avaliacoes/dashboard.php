<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-chart-bar me-2"></i>
        Dashboard de Avaliações Comparativo
    </h2>
    <a href="<?= base_url('avaliacoes/historico') ?>" class="btn btn-primary">
        <i class="fas fa-history me-2"></i>
        Ver Histórico Completo
    </a>
</div>

<div class="alert alert-info mb-4">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Dashboard Comparativo:</strong> Visualize as avaliações de clientes, funcionários e colaboradoras lado a lado.
</div>

<!-- Filtro de Mês/Ano -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Empresa</label>
                <select name="empresa_id" class="form-select">
                    <option value="">Todas as Empresas</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa->id ?>" <?= $empresaId == $empresa->id ? 'selected' : '' ?>>
                            <?= esc($empresa->nome_fantasia) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Mês</label>
                <select name="mes" class="form-select">
                    <?php
                    $meses = [
                        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                    ];
                    foreach ($meses as $valor => $nome): ?>
                        <option value="<?= $valor ?>" <?= $mes == $valor ? 'selected' : '' ?>>
                            <?= $nome ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ano</label>
                <select name="ano" class="form-select">
                    <?php for ($a = date('Y'); $a >= date('Y') - 2; $a--): ?>
                        <option value="<?= $a ?>" <?= $ano == $a ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100">
                    <i class="fas fa-filter me-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($avaliacoes)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Nenhuma avaliação registrada para este período.
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="tabelaAvaliacoes">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Empresa</th>
                            <th class="text-center">Avaliação Cliente</th>
                            <th class="text-center">Avaliação Funcionários</th>
                            <th class="text-center">Avaliação Colaboradora</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avaliacoes as $item): ?>
                        <tr>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($item['data'])) ?></strong><br>
                                <small class="text-muted">
                                    <?php
                                    $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                    echo $diasSemana[date('w', strtotime($item['data']))];
                                    ?>
                                </small>
                            </td>
                            <td>
                                <strong><?= esc($item['empresa_nome']) ?></strong>
                            </td>

                            <!-- Avaliação Cliente -->
                            <td class="text-center">
                                <?php if ($item['cliente']): ?>
                                    <?php
                                    $cores = [
                                        'otimo' => 'success',
                                        'bom' => 'primary',
                                        'regular' => 'warning',
                                        'ruim' => 'danger'
                                    ];
                                    $textos = [
                                        'otimo' => 'Ótimo',
                                        'bom' => 'Bom',
                                        'regular' => 'Regular',
                                        'ruim' => 'Ruim'
                                    ];
                                    $cor = $cores[$item['cliente']->avaliacao];
                                    $texto = $textos[$item['cliente']->avaliacao];
                                    ?>
                                    <span class="badge bg-<?= $cor ?> fs-6">
                                        <?= $texto ?>
                                    </span>
                                    <?php if (in_array($item['cliente']->avaliacao, ['regular', 'ruim']) && $item['cliente']->motivo): ?>
                                        <button class="btn btn-sm btn-link p-0 ms-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalMotivo"
                                                onclick="showMotivo('Cliente', '<?= esc($item['empresa_nome']) ?>', '<?= date('d/m/Y', strtotime($item['data'])) ?>', '<?= esc(addslashes($item['cliente']->motivo)) ?>')">
                                            <i class="fas fa-info-circle text-<?= $cor ?>"></i>
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Avaliação Funcionários (Média) -->
                            <td class="text-center">
                                <?php if ($item['funcionarios_resumo']): ?>
                                    <?php
                                    $resumo = $item['funcionarios_resumo'];
                                    $cor = $cores[$resumo->avaliacao];
                                    $texto = $textos[$resumo->avaliacao];
                                    ?>
                                    <span class="badge bg-<?= $cor ?> fs-6">
                                        <?= $texto ?>
                                    </span>
                                    <small class="d-block text-muted mt-1">(<?= $resumo->quantidade ?> aval.)</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Avaliação Colaboradora (Média) -->
                            <td class="text-center">
                                <?php if ($item['colaboradoras_resumo']): ?>
                                    <?php
                                    $resumo = $item['colaboradoras_resumo'];
                                    $cor = $cores[$resumo->avaliacao];
                                    $texto = $textos[$resumo->avaliacao];
                                    ?>
                                    <span class="badge bg-<?= $cor ?> fs-6">
                                        <?= $texto ?>
                                    </span>
                                    <small class="d-block text-muted mt-1">(<?= $resumo->quantidade ?> aval.)</small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Botão de Ações -->
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetalhes"
                                        onclick='showDetalhesTodas(<?= json_encode($item) ?>)'>
                                    <i class="fas fa-chart-bar me-1"></i> Ver Detalhes
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal para mostrar motivo -->
<div class="modal fade" id="modalMotivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment-alt me-2"></i>
                    Motivo da Avaliação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tipo:</strong> <span id="motivoTipo"></span></p>
                <p><strong>Empresa:</strong> <span id="motivoEmpresa"></span></p>
                <p><strong>Data:</strong> <span id="motivoData"></span></p>
                <hr>
                <p><strong>Motivo:</strong></p>
                <p id="motivoTexto" class="bg-light p-3 rounded"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir detalhes completos -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-chart-bar me-2"></i>
                    Detalhes das Avaliações
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p><strong>Tipo:</strong> <span id="detalhesTipo"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Empresa:</strong> <span id="detalhesEmpresa"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Data:</strong> <span id="detalhesData"></span></p>
                    </div>
                </div>

                <hr>

                <!-- Cliente -->
                <div id="secaoCliente" class="mb-4">
                    <!-- Será preenchido via JavaScript -->
                </div>

                <!-- Funcionários -->
                <div id="secaoFuncionarios" class="mb-4">
                    <!-- Será preenchido via JavaScript -->
                </div>

                <!-- Colaboradoras -->
                <div id="secaoColaboradoras" class="mb-4">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showMotivo(tipo, empresa, data, motivo) {
    document.getElementById('motivoTipo').textContent = tipo;
    document.getElementById('motivoEmpresa').textContent = empresa;
    document.getElementById('motivoData').textContent = data;
    document.getElementById('motivoTexto').innerHTML = motivo;
}

function showDetalhesTodas(item) {
    // Preencher cabeçalho
    document.getElementById('detalhesTipo').textContent = 'Todas as Avaliações';
    document.getElementById('detalhesEmpresa').textContent = item.empresa_nome;
    document.getElementById('detalhesData').textContent = new Date(item.data).toLocaleDateString('pt-BR');

    const cores = {
        'otimo': 'success',
        'bom': 'info',
        'regular': 'warning',
        'ruim': 'danger'
    };
    const textos = {
        'otimo': 'Ótimo',
        'bom': 'Bom',
        'regular': 'Regular',
        'ruim': 'Ruim'
    };
    const icones = {
        'otimo': 'fa-star',
        'bom': 'fa-thumbs-up',
        'regular': 'fa-meh',
        'ruim': 'fa-times-circle'
    };

    // Função auxiliar para criar seção
    function criarSecao(titulo, avaliacoes, resumo) {
        if (!avaliacoes || (Array.isArray(avaliacoes) && avaliacoes.length === 0)) {
            return `
                <h6><i class="fas fa-${titulo === 'Cliente' ? 'user' : titulo === 'Funcionários' ? 'users' : 'user-friends'} me-2"></i>${titulo}</h6>
                <p class="text-muted">Sem avaliações</p>
            `;
        }

        let html = `<h6><i class="fas fa-${titulo === 'Cliente' ? 'user' : titulo === 'Funcionários' ? 'users' : 'user-friends'} me-2"></i>${titulo}</h6>`;

        // Se é um único objeto (Cliente)
        if (!Array.isArray(avaliacoes)) {
            html += `
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="card bg-${cores[avaliacoes.avaliacao]} text-white">
                            <div class="card-body text-center py-2">
                                <i class="fas ${icones[avaliacoes.avaliacao]} fa-2x"></i>
                                <h5 class="mb-0 mt-1">${textos[avaliacoes.avaliacao]}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            if (avaliacoes.motivo) {
                html += `
                    <div class="alert alert-light">
                        <strong>Observação:</strong> ${avaliacoes.motivo}
                    </div>
                `;
            }
        } else {
            // Múltiplas avaliações (Funcionários/Colaboradoras)
            // Contar por tipo
            const contadores = {'otimo': 0, 'bom': 0, 'regular': 0, 'ruim': 0};
            const observacoes = [];

            avaliacoes.forEach(function(aval) {
                contadores[aval.avaliacao]++;
                if (aval.motivo && aval.motivo.trim() !== '') {
                    observacoes.push({
                        avaliacao: aval.avaliacao,
                        motivo: aval.motivo
                    });
                }
            });

            // Resumo com cards
            html += '<div class="row mb-3">';
            Object.keys(contadores).forEach(function(tipo) {
                if (contadores[tipo] > 0) {
                    html += `
                        <div class="col-md-3 col-6 mb-2">
                            <div class="card bg-${cores[tipo]} text-white">
                                <div class="card-body text-center py-2">
                                    <i class="fas ${icones[tipo]} fa-2x"></i>
                                    <h4 class="mb-0">${contadores[tipo]}</h4>
                                    <small>${textos[tipo]}</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
            html += '</div>';

            // Observações
            if (observacoes.length > 0) {
                html += '<h6 class="mt-3"><i class="fas fa-comments me-2"></i>Observações</h6>';
                observacoes.forEach(function(obs, index) {
                    html += `
                        <div class="alert alert-${cores[obs.avaliacao]} alert-dismissible fade show">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-${cores[obs.avaliacao]} mb-1">${textos[obs.avaliacao]}</span>
                                <small class="text-muted">#${index + 1}</small>
                            </div>
                            <p class="mb-0 mt-2">${obs.motivo}</p>
                        </div>
                    `;
                });
            }
        }

        return html + '<hr>';
    }

    // Preencher seções
    document.getElementById('secaoCliente').innerHTML = criarSecao('Cliente', item.cliente);
    document.getElementById('secaoFuncionarios').innerHTML = criarSecao('Funcionários', item.funcionarios, item.funcionarios_resumo);
    document.getElementById('secaoColaboradoras').innerHTML = criarSecao('Colaboradoras', item.colaboradoras, item.colaboradoras_resumo);
}

$(document).ready(function() {
    $('#tabelaAvaliacoes').DataTable({
        order: [[0, 'desc']],
        pageLength: 50,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
        }
    });
});
</script>
<?= $this->endSection() ?>
