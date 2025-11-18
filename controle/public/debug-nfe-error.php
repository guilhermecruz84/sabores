<?php
// Habilita exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>Debug NFe Errors</title>";
echo "<style>body{font-family:monospace;padding:20px;background:#1e1e1e;color:#fff;}";
echo ".error{background:#ff0000;color:#fff;padding:15px;margin:10px 0;border-radius:5px;}";
echo ".info{background:#0066cc;color:#fff;padding:15px;margin:10px 0;border-radius:5px;}";
echo "pre{background:#000;color:#0f0;padding:15px;overflow-x:auto;}</style></head><body>";

echo "<h1>🔍 Debug NFe - Últimos Erros</h1>";

// Verificar logs do CodeIgniter
$logPath = dirname(__DIR__) . '/writable/logs/';

if (is_dir($logPath)) {
    echo "<div class='info'>✅ Pasta de logs encontrada: $logPath</div>";

    // Pegar o arquivo de log mais recente
    $files = glob($logPath . 'log-*.php');
    if (!empty($files)) {
        // Ordenar por data de modificação (mais recente primeiro)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $latestLog = $files[0];
        $logName = basename($latestLog);
        $logTime = date('Y-m-d H:i:s', filemtime($latestLog));

        echo "<div class='info'>📄 Log mais recente: $logName<br>Modificado em: $logTime</div>";

        // Ler últimas 100 linhas do log
        $content = file_get_contents($latestLog);
        $lines = explode("\n", $content);
        $recentLines = array_slice($lines, -150); // Últimas 150 linhas

        // Filtrar apenas linhas com ERROR, CRITICAL, Exception
        $errorLines = [];
        foreach ($recentLines as $line) {
            if (stripos($line, 'ERROR') !== false ||
                stripos($line, 'CRITICAL') !== false ||
                stripos($line, 'Exception') !== false ||
                stripos($line, 'Fatal') !== false ||
                stripos($line, 'nfe') !== false ||
                stripos($line, 'Nfe') !== false) {
                $errorLines[] = $line;
            }
        }

        if (!empty($errorLines)) {
            echo "<div class='error'>";
            echo "<strong>⚠️ ERROS ENCONTRADOS:</strong>";
            echo "<pre>" . htmlspecialchars(implode("\n", $errorLines)) . "</pre>";
            echo "</div>";
        } else {
            echo "<div class='info'>ℹ️ Nenhum erro relacionado a NFe encontrado no log recente</div>";
        }

        // Mostrar últimas 30 linhas gerais
        echo "<h2>Últimas 30 linhas do log:</h2>";
        echo "<pre>" . htmlspecialchars(implode("\n", array_slice($lines, -30))) . "</pre>";

    } else {
        echo "<div class='error'>❌ Nenhum arquivo de log encontrado</div>";
    }
} else {
    echo "<div class='error'>❌ Pasta de logs não encontrada: $logPath</div>";
}

// Verificar se há log de erros do PHP
$phpErrorLog = dirname(__DIR__) . '/writable/logs/php_errors.log';
if (file_exists($phpErrorLog)) {
    echo "<h2>PHP Error Log:</h2>";
    $phpErrors = file_get_contents($phpErrorLog);
    echo "<pre>" . htmlspecialchars($phpErrors) . "</pre>";
}

// Informações do servidor
echo "<hr><h2>Informações do Servidor:</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Date/Time: " . date('Y-m-d H:i:s') . "\n";
echo "Error Reporting: " . error_reporting() . "\n";
echo "Display Errors: " . ini_get('display_errors') . "\n";
echo "</pre>";

echo "</body></html>";
?>
