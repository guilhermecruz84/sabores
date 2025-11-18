<?= $this->extend('layouts/avaliador') ?>

<?= $this->section('content') ?>

<style>
    body {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .avaliacao-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }
    .avaliacao-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 700px;
        width: 100%;
    }
    .avaliacao-title {
        font-size: 2.2rem;
        font-weight: bold;
        color: #00a8cc;
        margin-bottom: 0.5rem;
        text-align: center;
    }
    .avaliacao-data {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 2rem;
        text-align: center;
    }
    .btn-avaliacao-opcao {
        width: 100%;
        padding: 1.8rem;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 20px;
        border: 3px solid transparent;
        margin-bottom: 1rem;
        transition: all 0.3s;
        background: #f8f9fa;
        color: #333;
        text-align: left;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    .btn-avaliacao-opcao:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .btn-avaliacao-opcao {
        position: relative;
    }
    .btn-avaliacao-opcao i {
        font-size: 2.5rem;
        margin-right: 1.5rem;
    }
    .btn-otimo { color: #28a745; }
    .btn-bom { color: #17a2b8; }
    .btn-regular { color: #ffc107; }
    .btn-ruim { color: #dc3545; }

    /* Estados ativos com cores específicas */
    .btn-avaliacao-opcao.active {
        transform: scale(1.08);
        border-width: 5px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
    }
    .btn-avaliacao-opcao.active::before {
        content: '✓';
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 3rem;
        font-weight: bold;
        animation: checkmark 0.3s ease-in-out;
    }
    @keyframes checkmark {
        0% { opacity: 0; transform: translateY(-50%) scale(0); }
        100% { opacity: 1; transform: translateY(-50%) scale(1); }
    }

    /* Ótimo - Verde */
    .btn-otimo.active {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        color: white !important;
        border-color: #1e7e34 !important;
    }
    .btn-otimo.active i,
    .btn-otimo.active div {
        color: white !important;
    }
    .btn-otimo.active::before {
        color: white !important;
    }

    /* Bom - Azul */
    .btn-bom.active {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        color: white !important;
        border-color: #117a8b !important;
    }
    .btn-bom.active i,
    .btn-bom.active div {
        color: white !important;
    }
    .btn-bom.active::before {
        color: white !important;
    }

    /* Regular - Amarelo */
    .btn-regular.active {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
        color: white !important;
        border-color: #e0a800 !important;
    }
    .btn-regular.active i,
    .btn-regular.active div {
        color: white !important;
    }
    .btn-regular.active::before {
        color: white !important;
    }

    /* Ruim - Vermelho */
    .btn-ruim.active {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        color: white !important;
        border-color: #bd2130 !important;
    }
    .btn-ruim.active i,
    .btn-ruim.active div {
        color: white !important;
    }
    .btn-ruim.active::before {
        color: white !important;
    }
    .btn-submit {
        width: 100%;
        padding: 1.5rem;
        font-size: 1.3rem;
        font-weight: bold;
        border-radius: 20px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border: none;
        margin-top: 2rem;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
    }
    .form-label-custom {
        font-size: 1.2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 1rem;
        display: block;
    }
    .form-control-custom {
        font-size: 1.1rem;
        padding: 1rem;
        border-radius: 15px;
        border: 2px solid #ddd;
    }
    .form-control-custom:focus {
        border-color: #00a8cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 168, 204, 0.25);
    }
</style>

<div class="avaliacao-container">
    <div class="avaliacao-card">
        <div class="avaliacao-title">
            <i class="fas fa-user-friends me-2"></i>
            Avaliar Colaboradora
        </div>
        <div class="avaliacao-data">
            <?php
            $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
            $hoje = date('Y-m-d');
            $diaSemana = $diasSemana[date('w', strtotime($hoje))];
            ?>
            <?= $diaSemana ?>, <?= date('d/m/Y', strtotime($hoje)) ?>
        </div>

        <form action="<?= base_url('avaliador/salvar-avaliacao-colaboradora') ?>" method="POST" id="formAvaliacao">
            <?= csrf_field() ?>

            <label class="form-label-custom">Como você avalia o atendimento da colaboradora?</label>

            <input type="radio" name="avaliacao" value="otimo" id="otimo" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'otimo') ? 'checked' : '' ?>>
            <label for="otimo" class="btn-avaliacao-opcao btn-otimo <?= ($avaliacao && $avaliacao->avaliacao === 'otimo') ? 'active' : '' ?>">
                <i class="fas fa-star"></i>
                <div>
                    <div>Ótimo</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Atendimento excelente!</small>
                </div>
            </label>

            <input type="radio" name="avaliacao" value="bom" id="bom" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'bom') ? 'checked' : '' ?>>
            <label for="bom" class="btn-avaliacao-opcao btn-bom <?= ($avaliacao && $avaliacao->avaliacao === 'bom') ? 'active' : '' ?>">
                <i class="fas fa-thumbs-up"></i>
                <div>
                    <div>Bom</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Atendimento satisfatório</small>
                </div>
            </label>

            <input type="radio" name="avaliacao" value="regular" id="regular" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'regular') ? 'checked' : '' ?>>
            <label for="regular" class="btn-avaliacao-opcao btn-regular <?= ($avaliacao && $avaliacao->avaliacao === 'regular') ? 'active' : '' ?>">
                <i class="fas fa-meh"></i>
                <div>
                    <div>Regular</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Pode melhorar</small>
                </div>
            </label>

            <input type="radio" name="avaliacao" value="ruim" id="ruim" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'ruim') ? 'checked' : '' ?>>
            <label for="ruim" class="btn-avaliacao-opcao btn-ruim <?= ($avaliacao && $avaliacao->avaliacao === 'ruim') ? 'active' : '' ?>">
                <i class="fas fa-times-circle"></i>
                <div>
                    <div>Ruim</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Necessita atenção</small>
                </div>
            </label>

            <div class="mt-4" id="motivoContainer" style="display: none;">
                <label class="form-label-custom">Por que essa avaliação? <span class="text-danger">*</span></label>
                <textarea name="motivo" id="motivo" class="form-control form-control-custom" rows="4"
                          placeholder="Descreva o motivo da sua avaliação..."><?= $avaliacao ? esc($avaliacao->motivo) : '' ?></textarea>
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="fas fa-check-circle me-2"></i>
                Concluir Avaliação
            </button>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Marcar opcao ativa ao clicar
    $('input[name="avaliacao"]').change(function() {
        var valor = $(this).val();

        // Remove active de todos
        $('.btn-avaliacao-opcao').removeClass('active');

        // Adiciona active no label correto
        $('label[for="' + valor + '"]').addClass('active');

        // Mostrar campo de motivo para Regular e Ruim (OBRIGATÓRIO)
        if (valor === 'regular' || valor === 'ruim') {
            $('#motivoContainer').slideDown();
            $('#motivo').attr('required', true); // OBRIGATÓRIO
        } else {
            $('#motivoContainer').slideUp();
            $('#motivo').attr('required', false);
            $('#motivo').val(''); // Limpar campo
        }
    });

    // Trigger inicial se já houver seleção
    if ($('input[name="avaliacao"]:checked').length) {
        $('input[name="avaliacao"]:checked').trigger('change');
    }
});
</script>
<?= $this->endSection() ?>
