<?= $this->extend('layouts/avaliador') ?>

<?= $this->section('content') ?>

<style>
    body {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        overflow: hidden;
    }
    .avaliacao-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 1rem;
        overflow-y: auto;
    }
    .avaliacao-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        max-width: 700px;
        width: 100%;
        max-height: 95vh;
        overflow-y: auto;
    }
    .avaliacao-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #f5576c;
        margin-bottom: 0.3rem;
        text-align: center;
    }
    .avaliacao-data {
        font-size: 1rem;
        color: #666;
        margin-bottom: 1rem;
        text-align: center;
    }
    .btn-avaliacao-opcao {
        width: 100%;
        padding: 1rem;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 15px;
        border: 3px solid transparent;
        margin-bottom: 0.6rem;
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
        font-size: 2rem;
        margin-right: 1rem;
    }
    .btn-otimo { color: #28a745; }
    .btn-bom { color: #17a2b8; }
    .btn-regular { color: #ffc107; }
    .btn-ruim { color: #dc3545; }

    /* Estados ativos com cores específicas */
    .btn-avaliacao-opcao.active {
        transform: scale(1.05);
        border-width: 4px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    .btn-avaliacao-opcao.active::before {
        content: '✓';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
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
        padding: 1rem;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 15px;
        background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        color: white;
        border: none;
        margin-top: 1rem;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 87, 108, 0.4);
    }
    .form-label-custom {
        font-size: 1rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }
    .form-control-custom {
        font-size: 1rem;
        padding: 0.8rem;
        border-radius: 12px;
        border: 2px solid #ddd;
    }
    .form-control-custom:focus {
        border-color: #f5576c;
        box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
    }

    /* Ajustes específicos para tablets */
    @media (min-width: 768px) and (max-width: 1024px) {
        .avaliacao-card {
            padding: 1.2rem;
            max-height: 90vh;
        }
        .avaliacao-title {
            font-size: 1.6rem;
            margin-bottom: 0.2rem;
        }
        .avaliacao-data {
            font-size: 0.95rem;
            margin-bottom: 0.8rem;
        }
        .btn-avaliacao-opcao {
            padding: 0.8rem;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .btn-avaliacao-opcao i {
            font-size: 1.6rem;
            margin-right: 0.8rem;
        }
        .btn-avaliacao-opcao small {
            font-size: 0.8rem !important;
        }
        .form-label-custom {
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
        }
        .form-control-custom {
            font-size: 0.95rem;
            padding: 0.7rem;
        }
        .btn-submit {
            padding: 0.9rem;
            font-size: 1.1rem;
            margin-top: 0.8rem;
        }
    }

    /* Ajustes para tablets pequenos em paisagem */
    @media (max-height: 800px) and (orientation: landscape) {
        .avaliacao-container {
            padding: 0.5rem;
        }
        .avaliacao-card {
            padding: 1rem;
            max-height: 96vh;
        }
        .avaliacao-title {
            font-size: 1.4rem;
            margin-bottom: 0.2rem;
        }
        .avaliacao-data {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .btn-avaliacao-opcao {
            padding: 0.6rem;
            font-size: 1rem;
            margin-bottom: 0.4rem;
        }
        .btn-avaliacao-opcao i {
            font-size: 1.4rem;
            margin-right: 0.7rem;
        }
        .btn-avaliacao-opcao small {
            font-size: 0.75rem !important;
        }
        .form-label-custom {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .form-control-custom {
            font-size: 0.9rem;
            padding: 0.6rem;
        }
        .btn-submit {
            padding: 0.8rem;
            font-size: 1rem;
            margin-top: 0.6rem;
        }
        #motivoContainer {
            margin-top: 0.5rem !important;
        }
        .form-control-custom[name="motivo"] {
            min-height: 80px !important;
        }
    }
</style>

<div class="avaliacao-container">
    <div class="avaliacao-card">
        <div class="avaliacao-title">
            <i class="fas fa-utensils me-2"></i>
            Avaliar Cardápio
        </div>
        <div class="avaliacao-data">
            <?php
            $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
            $hoje = date('Y-m-d');
            $diaSemana = $diasSemana[date('w', strtotime($hoje))];
            ?>
            <?= $diaSemana ?>, <?= date('d/m/Y', strtotime($hoje)) ?>
        </div>

        <form action="<?= base_url('avaliador/salvar-avaliacao-cardapio') ?>" method="POST" id="formAvaliacao">
            <?= csrf_field() ?>

            <label class="form-label-custom">Como você avalia o cardápio de hoje?</label>

            <input type="radio" name="avaliacao" value="otimo" id="otimo" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'otimo') ? 'checked' : '' ?>>
            <label for="otimo" class="btn-avaliacao-opcao btn-otimo <?= ($avaliacao && $avaliacao->avaliacao === 'otimo') ? 'active' : '' ?>">
                <i class="fas fa-star"></i>
                <div>
                    <div>Ótimo</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Excelente qualidade!</small>
                </div>
            </label>

            <input type="radio" name="avaliacao" value="bom" id="bom" class="d-none" <?= ($avaliacao && $avaliacao->avaliacao === 'bom') ? 'checked' : '' ?>>
            <label for="bom" class="btn-avaliacao-opcao btn-bom <?= ($avaliacao && $avaliacao->avaliacao === 'bom') ? 'active' : '' ?>">
                <i class="fas fa-thumbs-up"></i>
                <div>
                    <div>Bom</div>
                    <small style="font-size: 0.9rem; font-weight: normal;">Qualidade satisfatória</small>
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

            <div class="mt-3" id="motivoContainer" style="display: none;">
                <label class="form-label-custom">Por que essa avaliação? <span class="text-danger">*</span></label>
                <textarea name="motivo" id="motivo" class="form-control form-control-custom" rows="3"
                          placeholder="Descreva o motivo da sua avaliação..."><?= $avaliacao ? esc($avaliacao->motivo) : '' ?></textarea>
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="fas fa-arrow-right me-2"></i>
                Próximo
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
