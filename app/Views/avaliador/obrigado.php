<?= $this->extend('layouts/avaliador') ?>

<?= $this->section('content') ?>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .obrigado-container {
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 2rem;
    }
    .obrigado-card {
        background: white;
        border-radius: 30px;
        padding: 4rem 3rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        text-align: center;
        max-width: 600px;
        width: 100%;
        animation: slideIn 0.5s ease-out;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .obrigado-icon {
        font-size: 6rem;
        color: #28a745;
        margin-bottom: 2rem;
        animation: bounce 1s ease-in-out;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    .obrigado-title {
        font-size: 3rem;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 1rem;
    }
    .obrigado-message {
        font-size: 1.5rem;
        color: #666;
        margin-bottom: 3rem;
        line-height: 1.6;
    }
    .btn-nova-avaliacao {
        width: 100%;
        padding: 1.8rem;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        transition: all 0.3s;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }
    .btn-nova-avaliacao:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.5);
        color: white;
    }
    .info-avaliacao {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
    .info-avaliacao p {
        margin: 0;
        font-size: 1.1rem;
        color: #666;
    }
    .info-avaliacao strong {
        color: #28a745;
    }
</style>

<div class="obrigado-container">
    <div class="obrigado-card">
        <div class="obrigado-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="obrigado-title">
            Muito Obrigado!
        </div>
        <div class="obrigado-message">
            Suas avaliações de hoje foram concluídas com sucesso. Sua opinião é muito importante para nós!
        </div>

        <div class="info-avaliacao">
            <p><i class="fas fa-calendar-check me-2"></i> Avaliações concluídas em <strong><?= date('d/m/Y') ?></strong></p>
        </div>

        <a href="<?= base_url('avaliador') ?>" class="btn btn-nova-avaliacao">
            <i class="fas fa-redo me-2"></i>
            Nova Avaliação
        </a>

        <div class="mt-4">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Você pode realizar novas avaliações a qualquer momento
            </small>
        </div>
    </div>
</div>

<script>
// Contador regressivo
let segundos = 3;
const countdownElement = document.createElement('div');
countdownElement.style.cssText = 'font-size: 1.5rem; color: #667eea; font-weight: bold; margin-top: 2rem;';
countdownElement.innerHTML = '<i class="fas fa-clock me-2"></i>Redirecionando em <span id="countdown">3</span> segundos...';
document.querySelector('.obrigado-card').appendChild(countdownElement);

// Atualizar contador a cada segundo
const intervalo = setInterval(function() {
    segundos--;
    document.getElementById('countdown').textContent = segundos;

    if (segundos <= 0) {
        clearInterval(intervalo);
        countdownElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Carregando nova avaliação...';
    }
}, 1000);

// Redirecionar após 3 segundos
setTimeout(function() {
    window.location.href = '<?= base_url('avaliador') ?>';
}, 3000);
</script>

<?= $this->endSection() ?>
