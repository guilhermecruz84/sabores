<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    <h2 class="card-title text-success mb-3">Avaliação Enviada!</h2>
                    <p class="card-text text-muted mb-4">
                        Obrigado por avaliar o desempenho da colaboradora. Sua opinião é muito importante para nós!
                    </p>
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>
                            Voltar ao Dashboard
                        </a>
                        <a href="<?= base_url('avaliacao-colaboradora-cliente/historico') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-history me-2"></i>
                            Ver Minhas Avaliações
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
