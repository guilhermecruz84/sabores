<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($titulo) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #FF6B35;
            --secondary-color: #004E89;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }

        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .register-card h3 {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
        }

        .btn-register {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 107, 53, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-card">
            <h3><i class="fas fa-user-plus me-2"></i>Criar Conta</h3>

            <?php if (session()->getFlashdata('erro')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('erro') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('erros')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('erros') as $erro): ?>
                            <li><?= $erro ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('registro') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="form-label">Nome Completo *</label>
                    <input type="text" class="form-control" name="nome" required value="<?= old('nome') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" class="form-control" name="email" required value="<?= old('email') ?>">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Senha *</label>
                        <input type="password" class="form-control" name="senha" required minlength="6">
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirmar Senha *</label>
                        <input type="password" class="form-control" name="confirmar_senha" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Empresa *</label>
                    <select class="form-select" name="empresa_id" required>
                        <option value="">Selecione sua empresa</option>
                        <?php foreach ($empresas as $empresa): ?>
                            <option value="<?= $empresa->id ?>" <?= old('empresa_id') == $empresa->id ? 'selected' : '' ?>>
                                <?= esc($empresa->nome_fantasia) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Telefone</label>
                    <input type="text" class="form-control" name="telefone" value="<?= old('telefone') ?>">
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-check me-2"></i>
                    Criar Conta
                </button>

                <div class="text-center mt-3">
                    <a href="<?= base_url('login') ?>" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>
                        Voltar para Login
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
