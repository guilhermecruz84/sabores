<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-building me-2"></i>
        Empresas
    </h2>
    <a href="<?= base_url('empresas/nova') ?>" class="btn btn-primary btn-lg">
        <i class="fas fa-plus-circle me-2"></i>
        Nova Empresa
    </a>
</div>

<!-- Lista de Empresas -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>
        Lista de Empresas (<?= count($empresas) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabelaEmpresas">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Usuários</th>
                        <th>Chamados</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($empresas)): ?>
                        <?php foreach ($empresas as $empresa): ?>
                        <tr>
                            <td>
                                <strong><?= esc($empresa->nome_fantasia) ?></strong>
                                <?php if ($empresa->razao_social !== $empresa->nome_fantasia): ?>
                                    <br><small class="text-muted"><?= esc($empresa->razao_social) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($empresa->cnpj): ?>
                                    <small><?= esc($empresa->cnpj) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($empresa->email): ?>
                                    <small><?= esc($empresa->email) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($empresa->telefone): ?>
                                    <small><?= esc($empresa->telefone) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <i class="fas fa-users me-1"></i>
                                    <?= $empresa->total_usuarios ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="fas fa-ticket-alt me-1"></i>
                                    <?= $empresa->total_chamados ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($empresa->ativo): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?= date('d/m/Y', strtotime($empresa->created_at)) ?><br>
                                    <?= date('H:i', strtotime($empresa->created_at)) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= base_url('empresas/editar/' . $empresa->id) ?>" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhuma empresa encontrada
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
    <?php if (!empty($empresas)): ?>
    $('#tabelaEmpresas').DataTable({
        order: [[7, 'desc']], // Ordena pela data de cadastro
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: 8 } // Desabilita ordenação na coluna de Ações
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
