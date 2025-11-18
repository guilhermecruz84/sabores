<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="mb-4">
    <h2 class="mb-0">
        <i class="fas fa-star me-2"></i>
        Avaliar Cardápios
    </h2>
    <p class="text-muted">Avalie os cardápios servidos no refeitório</p>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" id="formFiltros">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="document.getElementById('formFiltros').submit()">
                        <option value="pendentes" <?= ($status ?? 'pendentes') === 'pendentes' ? 'selected' : '' ?>>
                            ⏳ Pendentes (Não Avaliados)
                        </option>
                        <option value="avaliados" <?= ($status ?? '') === 'avaliados' ? 'selected' : '' ?>>
                            ✓ Avaliados
                        </option>
                        <option value="todos" <?= ($status ?? '') === 'todos' ? 'selected' : '' ?>>
                            📋 Todos
                        </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mês</label>
                    <select name="mes" class="form-select" onchange="document.getElementById('formFiltros').submit()">
                        <?php
                        $meses = [
                            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                        ];
                        $mesSelecionado = $mes ?? date('m');
                        foreach ($meses as $valor => $nome): ?>
                            <option value="<?= $valor ?>" <?= $mesSelecionado == $valor ? 'selected' : '' ?>>
                                <?= $nome ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ano</label>
                    <select name="ano" class="form-select" onchange="document.getElementById('formFiltros').submit()">
                        <?php
                        $anoSelecionado = $ano ?? date('Y');
                        for ($a = date('Y'); $a >= date('Y') - 1; $a--): ?>
                            <option value="<?= $a ?>" <?= $anoSelecionado == $a ? 'selected' : '' ?>>
                                <?= $a ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Contador de Resultados -->
<?php if (!empty($cardapios)): ?>
<div class="alert alert-secondary mb-3">
    <i class="fas fa-info-circle me-2"></i>
    <strong><?= count($cardapios) ?></strong>
    <?= count($cardapios) === 1 ? 'cardápio encontrado' : 'cardápios encontrados' ?>
    <?php
    $statusTexto = [
        'pendentes' => 'pendente(s) de avaliação',
        'avaliados' => 'já avaliado(s)',
        'todos' => 'no total'
    ];
    echo '(' . $statusTexto[$status ?? 'pendentes'] . ')';
    ?>
</div>
<?php endif; ?>

<?php if (empty($cardapios)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <?php
        $mensagens = [
            'pendentes' => 'Nenhum cardápio pendente de avaliação neste período.',
            'avaliados' => 'Nenhum cardápio avaliado neste período.',
            'todos' => 'Nenhum cardápio cadastrado para este período.'
        ];
        echo $mensagens[$status ?? 'pendentes'];
        ?>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($cardapios as $cardapio): ?>
        <div class="col-md-3 col-lg-2 mb-3">
            <div class="card h-100 text-center <?= $cardapio->ja_avaliado ? 'border-success' : 'border-primary' ?>" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <div class="card-header bg-<?= $cardapio->ja_avaliado ? 'success' : 'primary' ?> text-white">
                    <?php
                    $diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                    $diaSemana = $diasSemana[date('w', strtotime($cardapio->data))];
                    ?>
                    <div style="font-size: 2rem;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h6 class="mb-0"><?= $diaSemana ?></h6>
                    <small><?= date('d/m/Y', strtotime($cardapio->data)) ?></small>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <?php if ($cardapio->ja_avaliado): ?>
                        <div class="mb-2">
                            <div style="font-size: 2rem;">
                                <?php
                                $avaliacaoIcons = [
                                    'otimo' => '⭐⭐⭐⭐',
                                    'bom' => '⭐⭐⭐',
                                    'regular' => '⭐⭐',
                                    'ruim' => '⭐'
                                ];
                                echo $avaliacaoIcons[$cardapio->avaliacao->avaliacao];
                                ?>
                            </div>
                            <small class="text-success"><i class="fas fa-check"></i> Avaliado</small>
                        </div>
                        <a href="<?= base_url('avaliacoes/avaliar/' . $cardapio->id) ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Alterar
                        </a>
                    <?php else: ?>
                        <div class="mb-2" style="font-size: 3rem; color: #ddd;">
                            <i class="far fa-star"></i>
                        </div>
                        <a href="<?= base_url('avaliacoes/avaliar/' . $cardapio->id) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-star"></i> Avaliar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
