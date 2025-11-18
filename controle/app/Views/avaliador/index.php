<?= $this->extend('layouts/avaliador') ?>

<?= $this->section('content') ?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .avaliador-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }
    .welcome-card {
        background: white;
        border-radius: 30px;
        padding: 3rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        text-align: center;
        max-width: 600px;
        width: 100%;
    }
    .welcome-title {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 1rem;
    }
    .welcome-subtitle {
        font-size: 1.2rem;
        color: #666;
        margin-bottom: 3rem;
    }
    .btn-avaliacao {
        width: 100%;
        padding: 2rem;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 20px;
        border: none;
        margin-bottom: 1.5rem;
        transition: all 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .btn-avaliacao:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    .btn-avaliacao i {
        font-size: 2rem;
        margin-right: 1rem;
    }
    .btn-cardapio {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .btn-colaboradora {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    .btn-avaliacao.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        margin-left: 1rem;
    }
    .status-concluido {
        background: #28a745;
        color: white;
    }
    .status-pendente {
        background: #ffc107;
        color: #333;
    }
</style>

<div class="avaliador-container">
    <div class="welcome-card">
        <div class="welcome-title">
            Sistema de Avaliação
        </div>
        <div class="welcome-subtitle">
            <?php
            $meses = [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
            ];
            $hoje = date('Y-m-d');
            $dia = date('d', strtotime($hoje));
            $mes = $meses[date('m', strtotime($hoje))];
            $ano = date('Y', strtotime($hoje));
            ?>
            Bem-vindo! Hoje é <?= $dia ?> de <?= $mes ?> de <?= $ano ?>
        </div>

        <!-- Avaliar Cardápio -->
            <?php if ($cardapioAvaliado): ?>
                <button class="btn btn-avaliacao btn-cardapio disabled" disabled>
                    <i class="fas fa-utensils"></i>
                    Avaliar Cardápio
                    <span class="status-badge status-concluido">
                        <i class="fas fa-check"></i> Concluído
                    </span>
                </button>
            <?php else: ?>
                <a href="<?= base_url('avaliador/avaliar-cardapio') ?>" class="btn btn-avaliacao btn-cardapio">
                    <i class="fas fa-utensils"></i>
                    Avaliar Cardápio
                    <span class="status-badge status-pendente">
                        <i class="fas fa-clock"></i> Pendente
                    </span>
                </a>
            <?php endif; ?>

            <!-- Avaliar Colaboradora -->
            <?php if ($colaboradoraAvaliada): ?>
                <button class="btn btn-avaliacao btn-colaboradora disabled" disabled>
                    <i class="fas fa-user-friends"></i>
                    Avaliar Colaboradora
                    <span class="status-badge status-concluido">
                        <i class="fas fa-check"></i> Concluído
                    </span>
                </button>
            <?php else: ?>
                <a href="<?= base_url('avaliador/avaliar-colaboradora') ?>" class="btn btn-avaliacao btn-colaboradora">
                    <i class="fas fa-user-friends"></i>
                    Avaliar Colaboradora
                    <span class="status-badge status-pendente">
                        <i class="fas fa-clock"></i> Pendente
                    </span>
                </a>
            <?php endif; ?>

        <?php if ($cardapioAvaliado && $colaboradoraAvaliada): ?>
            <div class="alert alert-success mt-4">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Parabéns!</strong> Todas as avaliações de hoje foram concluídas.
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
