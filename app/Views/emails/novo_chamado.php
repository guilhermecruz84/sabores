<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Chamado - <?= esc($chamado['titulo']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #FF6B35 0%, #004E89 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .email-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-box strong {
            color: #856404;
        }
        .info-row {
            margin: 15px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #004E89;
            display: inline-block;
            min-width: 120px;
        }
        .info-value {
            color: #333;
        }
        .description-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #FF6B35;
        }
        .description-box h3 {
            margin-top: 0;
            color: #004E89;
            font-size: 16px;
        }
        .description-box p {
            color: #333;
            line-height: 1.6;
            margin: 0;
        }
        .btn-container {
            text-align: center;
            margin: 30px 0 20px 0;
        }
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #FF6B35 0%, #004E89 100%);
            color: white;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-warning {
            background: #ffc107;
            color: #856404;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .badge-success {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🎫 Novo Chamado Aberto</h1>
            <p>Sistema Sabores em Movimento</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="alert-box">
                <strong>⚠️ Atenção!</strong> Um novo chamado foi registrado no sistema e requer sua atenção.
            </div>

            <!-- Informações do Chamado -->
            <div class="info-row">
                <span class="info-label">📋 Título:</span>
                <span class="info-value"><strong><?= esc($chamado['titulo']) ?></strong></span>
            </div>

            <div class="info-row">
                <span class="info-label">👤 Cliente:</span>
                <span class="info-value"><?= esc($cliente['nome']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">🏢 Empresa:</span>
                <span class="info-value"><?= esc($empresa['nome']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">📧 Email:</span>
                <span class="info-value"><?= esc($cliente['email']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">🔔 Prioridade:</span>
                <span class="info-value">
                    <?php
                    $prioridadeClass = [
                        'baixa' => 'badge-info',
                        'media' => 'badge-warning',
                        'alta' => 'badge-danger',
                        'urgente' => 'badge-danger'
                    ];
                    $prioridadeTexto = [
                        'baixa' => 'Baixa',
                        'media' => 'Média',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente'
                    ];
                    ?>
                    <span class="badge <?= $prioridadeClass[$chamado['prioridade']] ?? 'badge-info' ?>">
                        <?= $prioridadeTexto[$chamado['prioridade']] ?? 'Normal' ?>
                    </span>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">📅 Data/Hora:</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($chamado['created_at'])) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">#️⃣ Número:</span>
                <span class="info-value">#<?= $chamado['id'] ?></span>
            </div>

            <!-- Descrição -->
            <div class="description-box">
                <h3>📝 Descrição do Chamado:</h3>
                <p><?= nl2br(esc($chamado['descricao'])) ?></p>
            </div>

            <!-- Botão -->
            <div class="btn-container">
                <a href="<?= $linkChamado ?>" class="btn-primary">
                    👁️ Visualizar Chamado Completo
                </a>
            </div>

            <p style="text-align: center; color: #6c757d; font-size: 13px; margin-top: 20px;">
                <strong>Link direto:</strong><br>
                <a href="<?= $linkChamado ?>" style="color: #FF6B35;"><?= $linkChamado ?></a>
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0;">
                <strong>Sabores em Movimento</strong><br>
                Sistema de Gestão de Chamados e Solicitações
            </p>
            <p style="margin: 10px 0 0 0;">
                Este é um email automático, não responda esta mensagem.
            </p>
        </div>
    </div>
</body>
</html>
