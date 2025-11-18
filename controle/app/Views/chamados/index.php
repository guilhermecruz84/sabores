<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-ticket-alt me-2"></i>
        Chamados
    </h2>
    <a href="<?= base_url('chamados/novo') ?>" class="btn btn-primary btn-lg">
        <i class="fas fa-plus-circle me-2"></i>
        Novo Chamado
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="<?= base_url('chamados') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="ocorrencia" <?= ($filtros['tipo'] ?? '') === 'ocorrencia' ? 'selected' : '' ?>>Ocorrência</option>
                        <option value="solicitacao" <?= ($filtros['tipo'] ?? '') === 'solicitacao' ? 'selected' : '' ?>>Solicitação</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="aberto" <?= ($filtros['status'] ?? '') === 'aberto' ? 'selected' : '' ?>>Aberto</option>
                        <option value="em_atendimento" <?= ($filtros['status'] ?? '') === 'em_atendimento' ? 'selected' : '' ?>>Em Atendimento</option>
                        <option value="aguardando_cliente" <?= ($filtros['status'] ?? '') === 'aguardando_cliente' ? 'selected' : '' ?>>Aguardando Cliente</option>
                        <option value="finalizado" <?= ($filtros['status'] ?? '') === 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select name="categoria" class="form-select">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= esc($categoria->nome) ?>" <?= ($filtros['categoria'] ?? '') === $categoria->nome ? 'selected' : '' ?>>
                            <?= esc($categoria->nome) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control" placeholder="Protocolo ou assunto..." value="<?= esc($filtros['busca'] ?? '') ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>
                        Filtrar
                    </button>
                    <a href="<?= base_url('chamados') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Chamados -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>
        Lista de Chamados (<?= count($chamados) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabelaChamados">
                <thead>
                    <tr>
                        <th>Protocolo</th>
                        <th>Tipo</th>
                        <th>Assunto</th>
                        <?php if ($usuarioLogado->tipo !== 'cliente'): ?>
                        <th>Cliente/Empresa</th>
                        <?php endif; ?>
                        <th>Categoria</th>
                        <th>Prioridade</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($chamados)): ?>
                        <?php foreach ($chamados as $chamado): ?>
                        <tr>
                            <td><strong>#<?= esc($chamado->protocolo) ?></strong></td>
                            <td>
                                <?php if ($chamado->tipo === 'ocorrencia'): ?>
                                    <span class="badge bg-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Ocorrência
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-file-alt me-1"></i>
                                        Solicitação
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= esc($chamado->assunto) ?></strong><br>
                                <small class="text-muted"><?= substr(esc($chamado->descricao), 0, 50) ?>...</small>
                            </td>
                            <?php if ($usuarioLogado->tipo !== 'cliente'): ?>
                            <td>
                                <small>
                                    <strong><?= esc($chamado->empresa_nome) ?></strong><br>
                                    <?= esc($chamado->usuario_nome) ?>
                                </small>
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php if ($chamado->categoria): ?>
                                    <span class="badge bg-secondary"><?= esc($chamado->categoria) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $prioridadeCores = [
                                    'baixa' => 'success',
                                    'media' => 'info',
                                    'alta' => 'warning',
                                    'urgente' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?= $prioridadeCores[$chamado->prioridade] ?>">
                                    <?= ucfirst($chamado->prioridade) ?>
                                </span>
                            </td>
                            <td>
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
                                <span class="badge bg-<?= $statusCores[$chamado->status] ?>">
                                    <?= $statusLabels[$chamado->status] ?>
                                </span>
                            </td>
                            <td>
                                <small>
                                    <?= date('d/m/Y', strtotime($chamado->created_at)) ?><br>
                                    <?= date('H:i', strtotime($chamado->created_at)) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= base_url('chamados/ver/' . $chamado->id) ?>" class="btn btn-sm btn-primary" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $usuarioLogado->tipo !== 'cliente' ? '9' : '8' ?>" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum chamado encontrado
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    <?php if (!empty($chamados)): ?>
    // Ajusta o índice da coluna de data baseado no tipo de usuário
    var isCliente = <?= $usuarioLogado->tipo === 'cliente' ? 'true' : 'false' ?>;
    var dataColumnIndex = isCliente ? 6 : 7;
    var acoesColumnIndex = isCliente ? 7 : 8;

    $('#tabelaChamados').DataTable({
        order: [[dataColumnIndex, 'desc']],
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: acoesColumnIndex } // Desabilita ordenação na coluna de Ações
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
