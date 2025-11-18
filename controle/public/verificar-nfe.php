<?php
// Verificação rápida do arquivo Nfe.php
$nfeFile = __DIR__ . '/../app/Controllers/Operacional/Nfe.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== VERIFICAÇÃO DO ARQUIVO NFE.PHP ===\n\n";

if (!file_exists($nfeFile)) {
    echo "❌ ERRO: Arquivo não encontrado!\n";
    echo "Caminho esperado: $nfeFile\n";
    exit;
}

echo "✅ Arquivo encontrado: $nfeFile\n";
echo "Data de modificação: " . date('Y-m-d H:i:s', filemtime($nfeFile)) . "\n\n";

$content = file_get_contents($nfeFile);

// Verificar constante BASE
if (preg_match("/private const BASE = '([^']+)'/", $content, $matches)) {
    $baseValue = $matches[1];
    echo "Constante BASE encontrada: '$baseValue'\n\n";

    if ($baseValue === 'operacional/nfe') {
        echo "✅ CORRETO! A constante está como 'operacional/nfe'\n";
        echo "✅ O arquivo foi atualizado com sucesso!\n\n";
        echo "Agora você pode:\n";
        echo "1. Limpar cache (se houver)\n";
        echo "2. Testar a importação de NFe novamente\n";
    } else {
        echo "❌ ERRO! A constante ainda está como '$baseValue'\n";
        echo "❌ O arquivo NÃO foi atualizado!\n\n";
        echo "AÇÃO NECESSÁRIA:\n";
        echo "Faça upload do arquivo Nfe.php do seu computador local para:\n";
        echo "$nfeFile\n";
    }
} else {
    echo "❌ ERRO: Não conseguiu encontrar a constante BASE no arquivo\n";
}

echo "\n\n";
echo "Horário da verificação: " . date('Y-m-d H:i:s') . "\n";
?>
