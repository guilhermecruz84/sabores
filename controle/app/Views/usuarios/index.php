<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-users me-2"></i>
        Usuários
    </h2>
    <a href="<?= base_url('usuarios/novo') ?>" class="btn btn-primary btn-lg">
        <i class="fas fa-plus-circle me-2"></i>
        Novo Usuário
    </a>
</div>

<!-- Lista de Usuários -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list me-2"></i>
        Lista de Usuários (<?= count($usuarios) ?>)
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tabelaUsuarios">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Empresa</th>
                        <th>Tipo</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><strong><?= esc($usuario->nome) ?></strong></td>
                            <td><?= esc($usuario->email) ?></td>
                            <td>
                                <?php if ($usuario->empresa_nome): ?>
                                    <small><?= esc($usuario->empresa_nome) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $tipoCores = [
                                    'admin' => 'danger',
                                    'atendente' => 'primary',
                                    'cliente' => 'info',
                                    'operador' => 'warning',
                                    'avaliador' => 'success'
                                ];
                                $tipoLabels = [
                                    'admin' => 'Admin',
                                    'atendente' => 'Administrativo',
                                    'cliente' => 'Cliente',
                                    'operador' => 'Operador',
                                    'avaliador' => 'Avaliador'
                                ];
                                ?>
                                <span class="badge bg-<?= $tipoCores[$usuario->tipo] ?? 'secondary' ?>">
                                    <?= $tipoLabels[$usuario->tipo] ?? $usuario->tipo ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($usuario->telefone): ?>
                                    <small><?= esc($usuario->telefone) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($usuario->ativo): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small>
                                    <?= date('d/m/Y', strtotime($usuario->created_at)) ?><br>
                                    <?= date('H:i', strtotime($usuario->created_at)) ?>
                                </small>
                            </td>
                            <td>
                                <a href="<?= base_url('usuarios/editar/' . $usuario->id) ?>" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Nenhum usuário encontrado
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
    <?php if (!empty($usuarios)): ?>
    $('#tabelaUsuarios').DataTable({
        order: [[6, 'desc']], // Ordena pela data de cadastro
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: 7 } // Desabilita ordenação na coluna de Ações
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
        }
    });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
