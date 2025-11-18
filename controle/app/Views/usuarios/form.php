<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-user-<?= $usuario ? 'edit' : 'plus' ?> me-2"></i>
        <?= $usuario ? 'Editar' : 'Novo' ?> Usuário
    </h2>
    <a href="<?= base_url('usuarios') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= base_url($usuario ? 'usuarios/atualizar/' . $usuario->id : 'usuarios/criar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <!-- Nome -->
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($erros['nome']) ? 'is-invalid' : '' ?>"
                           id="nome" name="nome" value="<?= old('nome', $usuario->nome ?? '') ?>" required>
                    <?php if (isset($erros['nome'])): ?>
                        <div class="invalid-feedback"><?= $erros['nome'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= isset($erros['email']) ? 'is-invalid' : '' ?>"
                           id="email" name="email" value="<?= old('email', $usuario->email ?? '') ?>" required>
                    <?php if (isset($erros['email'])): ?>
                        <div class="invalid-feedback"><?= $erros['email'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Senha -->
                <div class="col-md-6">
                    <label for="senha" class="form-label">
                        Senha <?= !$usuario ? '<span class="text-danger">*</span>' : '(deixe em branco para não alterar)' ?>
                    </label>
                    <input type="password" class="form-control <?= isset($erros['senha']) ? 'is-invalid' : '' ?>"
                           id="senha" name="senha" <?= !$usuario ? 'required' : '' ?>>
                    <?php if (isset($erros['senha'])): ?>
                        <div class="invalid-feedback"><?= $erros['senha'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Telefone -->
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone"
                           value="<?= old('telefone', $usuario->telefone ?? '') ?>"
                           placeholder="(00) 00000-0000">
                </div>

                <!-- Tipo -->
                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($erros['tipo']) ? 'is-invalid' : '' ?>"
                            id="tipo" name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="admin" <?= old('tipo', $usuario->tipo ?? '') === 'admin' ? 'selected' : '' ?>>
                            Admin
                        </option>
                        <option value="atendente" <?= old('tipo', $usuario->tipo ?? '') === 'atendente' ? 'selected' : '' ?>>
                            Administrativo
                        </option>
                        <option value="cliente" <?= old('tipo', $usuario->tipo ?? '') === 'cliente' ? 'selected' : '' ?>>
                            Cliente
                        </option>
                        <option value="operador" <?= old('tipo', $usuario->tipo ?? '') === 'operador' ? 'selected' : '' ?>>
                            Operador (Refeitório)
                        </option>
                        <option value="avaliador" <?= old('tipo', $usuario->tipo ?? '') === 'avaliador' ? 'selected' : '' ?>>
                            Avaliador (Tablet)
                        </option>
                    </select>
                    <?php if (isset($erros['tipo'])): ?>
                        <div class="invalid-feedback"><?= $erros['tipo'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Empresa -->
                <div class="col-md-6">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select class="form-select" id="empresa_id" name="empresa_id">
                        <option value="">Nenhuma</option>
                        <?php foreach ($empresas as $empresa): ?>
                        <option value="<?= $empresa->id ?>"
                                <?= old('empresa_id', $usuario->empresa_id ?? '') == $empresa->id ? 'selected' : '' ?>>
                            <?= esc($empresa->nome_fantasia) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($usuario): ?>
                <!-- Status (apenas ao editar) -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                               <?= old('ativo', $usuario->ativo ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ativo">
                            Usuário Ativo
                        </label>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botões -->
                <div class="col-12">
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?= $usuario ? 'Atualizar' : 'Criar' ?> Usuário
                    </button>
                    <a href="<?= base_url('usuarios') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Máscara para telefone
    $('#telefone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        }
        $(this).val(value);
    });
});
</script>
<?= $this->endSection() ?>
