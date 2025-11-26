<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FF6B35 0%, #004E89 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #FF6B35;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #004E89;
            display: inline-block;
            min-width: 120px;
        }
        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .priority-urgente {
            background-color: #dc3545;
        }
        .priority-alta {
            background-color: #fd7e14;
        }
        .priority-media {
            background-color: #ffc107;
            color: #333;
        }
        .priority-baixa {
            background-color: #28a745;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #FF6B35 0%, #004E89 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎫 Novo Chamado Aberto</h1>
        </div>

        <div class="content">
            <p>Um novo chamado foi aberto no sistema:</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Protocolo:</span>
                    <strong style="font-size: 18px; color: #FF6B35;">#<?= esc($chamado->protocolo) ?></strong>
                </div>

                <div class="info-row">
                    <span class="info-label">Tipo:</span>
                    <span><?= ucfirst(esc($chamado->tipo)) ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Assunto:</span>
                    <strong><?= esc($chamado->assunto) ?></strong>
                </div>

                <div class="info-row">
                    <span class="info-label">Prioridade:</span>
                    <span class="priority-badge priority-<?= esc($chamado->prioridade) ?>">
                        <?= strtoupper(esc($chamado->prioridade)) ?>
                    </span>
                </div>

                <?php if (!empty($chamado->categoria)): ?>
                <div class="info-row">
                    <span class="info-label">Categoria:</span>
                    <span><?= esc($chamado->categoria) ?></span>
                </div>
                <?php endif; ?>

                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span><?= esc($usuario->nome ?? 'N/A') ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Empresa:</span>
                    <span><?= esc($empresa->nome ?? 'N/A') ?></span>
                </div>

                <div class="info-row">
                    <span class="info-label">Data/Hora:</span>
                    <span><?= date('d/m/Y H:i', strtotime($chamado->created_at)) ?></span>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <strong style="color: #004E89;">Descrição:</strong>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 10px; white-space: pre-wrap;">
<?= esc($chamado->descricao) ?>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="<?= base_url('chamados/ver/' . $chamado->id) ?>" class="button">
                    Ver Chamado Completo
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Sabores em Movimento</strong></p>
            <p>Este é um email automático. Por favor, não responda este email.</p>
            <p>Acesse o sistema para responder ao chamado.</p>
        </div>
    </div>
</body>
</html>
