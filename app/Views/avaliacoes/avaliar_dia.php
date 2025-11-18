<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="fas fa-star me-2"></i>
        Avaliar Cardápio
    </h2>
    <a href="<?= base_url('avaliacoes') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Voltar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Informações do Dia -->
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-calendar-day me-3" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="mb-0">
                        <?php
                        $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                        $diaSemana = $diasSemana[date('w', strtotime($data))];
                        echo $diaSemana;
                        ?>
                    </h5>
                    <p class="mb-0"><?= date('d/m/Y', strtotime($data)) ?></p>
                </div>
            </div>
        </div>

        <!-- Formulário de Avaliação -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-star me-2"></i>
                <?= $avaliacao ? 'Editar Avaliação' : 'Avaliar Cardápio' ?>
            </div>
            <div class="card-body">
                <form action="<?= base_url('avaliacoes/salvar-avaliacao-dia') ?>" method="POST" id="formAvaliacao">
                    <?= csrf_field() ?>
                    <input type="hidden" name="data" value="<?= $data ?>">

                    <div class="mb-4">
                        <label class="form-label">Como você avalia este cardápio?</label>

                        <div class="d-grid gap-2">
                            <input type="radio" class="btn-check" name="avaliacao" id="otimo" value="otimo"
                                   <?= $avaliacao && $avaliacao->avaliacao === 'otimo' ? 'checked' : '' ?> required>
                            <label class="btn btn-outline-success btn-lg" for="otimo">
                                ⭐⭐⭐⭐ Ótimo
                            </label>

                            <input type="radio" class="btn-check" name="avaliacao" id="bom" value="bom"
                                   <?= $avaliacao && $avaliacao->avaliacao === 'bom' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-primary btn-lg" for="bom">
                                ⭐⭐⭐ Bom
                            </label>

                            <input type="radio" class="btn-check" name="avaliacao" id="regular" value="regular"
                                   <?= $avaliacao && $avaliacao->avaliacao === 'regular' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-warning btn-lg" for="regular">
                                ⭐⭐ Regular
                            </label>

                            <input type="radio" class="btn-check" name="avaliacao" id="ruim" value="ruim"
                                   <?= $avaliacao && $avaliacao->avaliacao === 'ruim' ? 'checked' : '' ?>>
                            <label class="btn btn-outline-danger btn-lg" for="ruim">
                                ⭐ Ruim
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="motivoDiv" style="display: none;">
                        <label for="motivo" class="form-label">
                            Motivo <span class="text-danger">*</span>
                            <small class="text-muted">(Obrigatório para Regular ou Ruim)</small>
                        </label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="4"
                                  placeholder="Por favor, informe o motivo da sua avaliação..."><?= $avaliacao ? esc($avaliacao->motivo) : '' ?></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>
                            Salvar Avaliação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Mostrar/ocultar campo de motivo
    function toggleMotivo() {
        const avaliacao = $('input[name="avaliacao"]:checked').val();
        if (avaliacao === 'regular' || avaliacao === 'ruim') {
            $('#motivoDiv').show();
            $('#motivo').prop('required', true);
        } else {
            $('#motivoDiv').hide();
            $('#motivo').prop('required', false);
        }
    }

    // Verificar no load
    toggleMotivo();

    // Verificar ao mudar
    $('input[name="avaliacao"]').change(toggleMotivo);

    // Validação antes de enviar
    $('#formAvaliacao').submit(function(e) {
        const avaliacao = $('input[name="avaliacao"]:checked').val();
        if ((avaliacao === 'regular' || avaliacao === 'ruim') && !$('#motivo').val().trim()) {
            e.preventDefault();
            alert('Por favor, informe o motivo da sua avaliação.');
            $('#motivo').focus();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
