<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Check NFe</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}";
echo "pre{background:#fff;padding:15px;border:1px solid #ddd;border-radius:5px;}</style></head><body>";

echo "<h1>🔍 Verificação Arquivo Nfe.php</h1>";

$nfeFile = dirname(__DIR__) . '/app/Controllers/Operacional/Nfe.php';

echo "<p><strong>Caminho:</strong> " . htmlspecialchars($nfeFile) . "</p>";

if (file_exists($nfeFile)) {
    echo "<p class='ok'>✅ Arquivo existe</p>";

    $modTime = filemtime($nfeFile);
    echo "<p><strong>Última modificação:</strong> " . date('Y-m-d H:i:s', $modTime) . "</p>";

    $content = @file_get_contents($nfeFile);

    if ($content === false) {
        echo "<p class='error'>❌ Não foi possível ler o arquivo</p>";
    } else {
        echo "<p class='ok'>✅ Arquivo lido com sucesso (" . strlen($content) . " bytes)</p>";

        // Procurar a constante BASE
        $found = preg_match("/private\\s+const\\s+BASE\\s*=\\s*'([^']+)'/", $content, $matches);

        if ($found) {
            $baseValue = $matches[1];
            echo "<p><strong>Constante BASE encontrada:</strong> <code>" . htmlspecialchars($baseValue) . "</code></p>";

            if ($baseValue === 'operacional/nfe') {
                echo "<p class='ok'>✅✅✅ CORRETO! Está como 'operacional/nfe'</p>";
                echo "<p>O arquivo foi atualizado corretamente!</p>";
            } else {
                echo "<p class='error'>❌❌❌ ERRADO! Está como '" . htmlspecialchars($baseValue) . "'</p>";
                echo "<p class='error'>Deveria ser: 'operacional/nfe'</p>";
                echo "<p><strong>AÇÃO:</strong> Faça upload do arquivo Nfe.php corrigido!</p>";
            }
        } else {
            echo "<p class='error'>❌ Constante BASE não encontrada no arquivo</p>";

            // Mostrar primeiras linhas para debug
            $lines = explode("\n", $content);
            echo "<p>Primeiras 15 linhas do arquivo:</p>";
            echo "<pre>";
            for ($i = 0; $i < min(15, count($lines)); $i++) {
                echo htmlspecialchars($lines[$i]) . "\n";
            }
            echo "</pre>";
        }
    }

} else {
    echo "<p class='error'>❌ Arquivo NÃO existe no caminho especificado</p>";
    echo "<p>Verifique se o caminho está correto</p>";
}

echo "<hr>";
echo "<p><small>Data/Hora: " . date('Y-m-d H:i:s') . "</small></p>";
echo "<p><small>PHP Version: " . phpversion() . "</small></p>";

echo "</body></html>";
?>
