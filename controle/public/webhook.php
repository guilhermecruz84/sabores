<?php
/**
 * Webhook para Deploy Automático
 *
 * Este script recebe notificações do GitHub e executa git pull
 * quando há um push no repositório.
 *
 * URL: https://saboresemmovimento.com.br/controle/webhook.php
 */

// ===== CONFIGURAÇÃO =====
define('SECRET_TOKEN', 'sabores_webhook_2025_secret'); // Altere este token!
define('REPO_PATH', '/home/saboresemmovimento/public_html/controle');
define('LOG_FILE', __DIR__ . '/webhook.log');

// ===== FUNÇÕES =====

/**
 * Registra log
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(LOG_FILE, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Valida assinatura do GitHub
 */
function validateSignature($payload, $signature) {
    if (empty($signature)) {
        return false;
    }

    list($algo, $hash) = explode('=', $signature, 2);
    $payloadHash = hash_hmac($algo, $payload, SECRET_TOKEN);

    return hash_equals($payloadHash, $hash);
}

/**
 * Executa git pull
 */
function deployCode() {
    $commands = [
        'cd ' . REPO_PATH,
        'git fetch origin main 2>&1',
        'git reset --hard origin/main 2>&1',
    ];

    $output = [];
    $returnVar = 0;

    foreach ($commands as $cmd) {
        exec($cmd, $output, $returnVar);
        logMessage("Comando: $cmd");
        logMessage("Output: " . implode("\n", $output));

        if ($returnVar !== 0) {
            logMessage("ERRO: Comando falhou com código $returnVar");
            return [
                'success' => false,
                'error' => implode("\n", $output),
                'command' => $cmd
            ];
        }
    }

    return [
        'success' => true,
        'output' => implode("\n", $output)
    ];
}

// ===== PROCESSAMENTO =====

header('Content-Type: application/json');

try {
    // 1. Pegar payload
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

    logMessage("=== Webhook recebido ===");
    logMessage("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    logMessage("Signature: " . ($signature ?: 'none'));

    // 2. Validar assinatura (IMPORTANTE em produção!)
    // DESCOMENTE quando configurar o secret no GitHub:
    // if (!validateSignature($payload, $signature)) {
    //     http_response_code(403);
    //     echo json_encode(['error' => 'Invalid signature']);
    //     logMessage("ERRO: Assinatura inválida");
    //     exit;
    // }

    // 3. Decodificar payload
    $data = json_decode($payload, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload');
    }

    // 4. Verificar se é evento de push
    $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';

    if ($event !== 'push') {
        logMessage("Evento ignorado: $event");
        echo json_encode(['message' => 'Event ignored', 'event' => $event]);
        exit;
    }

    // 5. Verificar branch
    $branch = isset($data['ref']) ? basename($data['ref']) : '';

    if ($branch !== 'main') {
        logMessage("Branch ignorada: $branch");
        echo json_encode(['message' => 'Branch ignored', 'branch' => $branch]);
        exit;
    }

    // 6. Executar deploy
    logMessage("Iniciando deploy...");
    $result = deployCode();

    if ($result['success']) {
        logMessage("Deploy concluído com sucesso!");
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Deploy realizado com sucesso',
            'output' => $result['output']
        ]);
    } else {
        logMessage("Deploy falhou: " . $result['error']);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $result['error'],
            'command' => $result['command']
        ]);
    }

} catch (Exception $e) {
    logMessage("EXCEÇÃO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

logMessage("=== Fim do processamento ===\n");
