<?php
/**
 * Webhook para Deploy Automático SEM SSH
 *
 * Este script baixa o código do GitHub em ZIP e descompacta automaticamente
 * Funciona mesmo sem acesso SSH!
 *
 * URL: https://saboresemmovimento.com.br/controle/webhook.php
 */

// ===== CONFIGURAÇÃO =====
define('SECRET_TOKEN', 'sabores_webhook_2025_secret'); // Mesmo secret do GitHub
define('GITHUB_REPO', 'guilhermecruz84/sabores'); // Seu repositório
define('GITHUB_BRANCH', 'main'); // Branch principal
define('DEPLOY_PATH', __DIR__ . '/..'); // Diretório raiz do projeto (um nível acima de public)
define('LOG_FILE', __DIR__ . '/webhook.log');
define('TEMP_ZIP', __DIR__ . '/temp_github.zip');
define('TEMP_EXTRACT', __DIR__ . '/temp_extract');

// ===== FUNÇÕES =====

/**
 * Registra log
 */
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log = "[$timestamp] $message\n";
    file_put_contents(LOG_FILE, $log, FILE_APPEND);
    echo $log; // Também exibe no output
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
 * Baixa código do GitHub
 */
function downloadFromGitHub() {
    $zipUrl = 'https://github.com/' . GITHUB_REPO . '/archive/refs/heads/' . GITHUB_BRANCH . '.zip';

    logMessage("Baixando de: $zipUrl");

    // Usar cURL para baixar
    $ch = curl_init($zipUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutos

    $zipContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$zipContent) {
        throw new Exception("Falha ao baixar ZIP do GitHub (HTTP $httpCode)");
    }

    // Salvar ZIP
    if (!file_put_contents(TEMP_ZIP, $zipContent)) {
        throw new Exception("Falha ao salvar ZIP temporário");
    }

    logMessage("ZIP baixado com sucesso (" . strlen($zipContent) . " bytes)");
    return true;
}

/**
 * Descompacta e instala arquivos
 */
function extractAndDeploy() {
    // Criar diretório temporário
    if (file_exists(TEMP_EXTRACT)) {
        deleteDirectory(TEMP_EXTRACT);
    }
    mkdir(TEMP_EXTRACT, 0755, true);

    // Descompactar
    logMessage("Descompactando ZIP...");
    $zip = new ZipArchive();

    if ($zip->open(TEMP_ZIP) !== true) {
        throw new Exception("Falha ao abrir ZIP");
    }

    $zip->extractTo(TEMP_EXTRACT);
    $zip->close();

    // GitHub cria pasta com nome do repo-branch
    $extractedFolder = TEMP_EXTRACT . '/sabores-' . GITHUB_BRANCH;

    if (!is_dir($extractedFolder)) {
        throw new Exception("Pasta extraída não encontrada: $extractedFolder");
    }

    logMessage("ZIP extraído em: $extractedFolder");

    // Copiar arquivos (exceto writable/, .env, etc)
    $excludes = ['writable', '.env', '.env.production', 'NULL', '.git', '.DS_Store'];

    copyFiles($extractedFolder . '/controle', DEPLOY_PATH, $excludes);

    logMessage("Arquivos copiados para: " . DEPLOY_PATH);

    // Limpar temporários
    deleteDirectory(TEMP_EXTRACT);
    @unlink(TEMP_ZIP);

    return true;
}

/**
 * Copia arquivos recursivamente
 */
function copyFiles($source, $dest, $excludes = []) {
    if (!is_dir($source)) {
        logMessage("AVISO: Source não é diretório: $source");
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $copied = 0;
    foreach ($iterator as $item) {
        $relativePath = str_replace($source . '/', '', $item->getPathname());

        // Verificar exclusões
        $skip = false;
        foreach ($excludes as $exclude) {
            if (strpos($relativePath, $exclude) === 0) {
                $skip = true;
                break;
            }
        }

        if ($skip) {
            continue;
        }

        $target = $dest . '/' . $relativePath;

        if ($item->isDir()) {
            if (!is_dir($target)) {
                mkdir($target, 0755, true);
            }
        } else {
            $targetDir = dirname($target);
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            copy($item->getPathname(), $target);
            $copied++;
        }
    }

    logMessage("$copied arquivos copiados");
}

/**
 * Remove diretório recursivamente
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        if ($item->isDir()) {
            rmdir($item->getPathname());
        } else {
            unlink($item->getPathname());
        }
    }

    rmdir($dir);
}

// ===== PROCESSAMENTO =====

header('Content-Type: application/json');

try {
    logMessage("=== Webhook recebido ===");

    // 1. Pegar payload
    $payload = file_get_contents('php://input');
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

    logMessage("IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    logMessage("User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));

    // 2. Validar assinatura (DESCOMENTE em produção)
    // if (!validateSignature($payload, $signature)) {
    //     http_response_code(403);
    //     logMessage("ERRO: Assinatura inválida");
    //     echo json_encode(['error' => 'Invalid signature']);
    //     exit;
    // }

    // 3. Decodificar payload
    $data = json_decode($payload, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload');
    }

    // 4. Verificar evento
    $event = $_SERVER['HTTP_X_GITHUB_EVENT'] ?? '';

    if ($event !== 'push') {
        logMessage("Evento ignorado: $event");
        echo json_encode(['message' => 'Event ignored', 'event' => $event]);
        exit;
    }

    // 5. Verificar branch
    $branch = isset($data['ref']) ? basename($data['ref']) : '';

    if ($branch !== GITHUB_BRANCH) {
        logMessage("Branch ignorada: $branch");
        echo json_encode(['message' => 'Branch ignored', 'branch' => $branch]);
        exit;
    }

    // 6. Iniciar deploy
    logMessage("Iniciando deploy automático...");
    logMessage("Repo: " . GITHUB_REPO);
    logMessage("Branch: " . GITHUB_BRANCH);

    // Baixar código
    downloadFromGitHub();

    // Descompactar e instalar
    extractAndDeploy();

    logMessage("✅ Deploy concluído com sucesso!");

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Deploy realizado com sucesso!',
        'timestamp' => date('Y-m-d H:i:s'),
        'repo' => GITHUB_REPO,
        'branch' => GITHUB_BRANCH
    ]);

} catch (Exception $e) {
    logMessage("❌ ERRO: " . $e->getMessage());
    logMessage("Stack trace: " . $e->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

logMessage("=== Fim do processamento ===\n");
