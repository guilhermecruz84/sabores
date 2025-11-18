<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-building me-2"></i>
        <?= $empresa ? 'Editar' : 'Nova' ?> Empresa
    </h2>
    <a href="<?= base_url('empresas') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="<?= base_url($empresa ? 'empresas/atualizar/' . $empresa->id : 'empresas/criar') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <!-- Razão Social -->
                <div class="col-md-6">
                    <label for="razao_social" class="form-label">Razão Social <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($erros['razao_social']) ? 'is-invalid' : '' ?>"
                           id="razao_social" name="razao_social" value="<?= old('razao_social', $empresa->razao_social ?? '') ?>" required>
                    <?php if (isset($erros['razao_social'])): ?>
                        <div class="invalid-feedback"><?= $erros['razao_social'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Nome Fantasia -->
                <div class="col-md-6">
                    <label for="nome_fantasia" class="form-label">Nome Fantasia <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($erros['nome_fantasia']) ? 'is-invalid' : '' ?>"
                           id="nome_fantasia" name="nome_fantasia" value="<?= old('nome_fantasia', $empresa->nome_fantasia ?? '') ?>" required>
                    <?php if (isset($erros['nome_fantasia'])): ?>
                        <div class="invalid-feedback"><?= $erros['nome_fantasia'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- CNPJ -->
                <div class="col-md-6">
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input type="text" class="form-control <?= isset($erros['cnpj']) ? 'is-invalid' : '' ?>"
                           id="cnpj" name="cnpj" value="<?= old('cnpj', $empresa->cnpj ?? '') ?>"
                           placeholder="00.000.000/0000-00">
                    <?php if (isset($erros['cnpj'])): ?>
                        <div class="invalid-feedback"><?= $erros['cnpj'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Telefone -->
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control" id="telefone" name="telefone"
                           value="<?= old('telefone', $empresa->telefone ?? '') ?>"
                           placeholder="(00) 00000-0000">
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?= isset($erros['email']) ? 'is-invalid' : '' ?>"
                           id="email" name="email" value="<?= old('email', $empresa->email ?? '') ?>">
                    <?php if (isset($erros['email'])): ?>
                        <div class="invalid-feedback"><?= $erros['email'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Endereço -->
                <div class="col-md-6">
                    <label for="endereco" class="form-label">Endereço</label>
                    <input type="text" class="form-control" id="endereco" name="endereco"
                           value="<?= old('endereco', $empresa->endereco ?? '') ?>">
                </div>

                <?php if ($empresa): ?>
                <!-- Status (apenas ao editar) -->
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                               <?= old('ativo', $empresa->ativo ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="ativo">
                            Empresa Ativa
                        </label>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botões -->
                <div class="col-12">
                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        <?= $empresa ? 'Atualizar' : 'Criar' ?> Empresa
                    </button>
                    <a href="<?= base_url('empresas') ?>" class="btn btn-secondary">
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
    // Máscara para CNPJ
    $('#cnpj').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 14) {
            value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
        }
        $(this).val(value);
    });

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
