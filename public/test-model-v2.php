<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste ChecklistAlertaModel v2</h1><hr>";

// Usar caminho relativo baseado na localização do arquivo
$modelPath = __DIR__ . '/../app/Models/ChecklistAlertaModel.php';
$realPath = realpath($modelPath);

echo "<h2>1. Verificar arquivo</h2>";
echo "Caminho procurado: $modelPath<br>";
echo "Caminho real: " . ($realPath ? $realPath : 'NÃO ENCONTRADO') . "<br><br>";

if (file_exists($modelPath)) {
    echo "✓ Arquivo existe!<br>";
    echo "Tamanho: " . filesize($modelPath) . " bytes<br>";

    // Ler conteúdo
    $content = file_get_contents($modelPath);

    // Verificar BOM
    if (substr($content, 0, 3) == "\xEF\xBB\xBF") {
        echo "⚠ <strong style='color:red'>PROBLEMA: Arquivo tem BOM UTF-8!</strong><br>";
        echo "→ Você precisa salvar o arquivo sem BOM<br>";
    } else {
        echo "✓ Arquivo sem BOM<br>";
    }

    // Verificar início
    $trimmed = ltrim($content);
    if (substr($trimmed, 0, 5) == '<?php') {
        echo "✓ Arquivo começa com &lt;?php<br>";
    } else {
        echo "✗ <strong style='color:red'>PROBLEMA: Arquivo não começa corretamente</strong><br>";
        echo "Primeiros caracteres: " . htmlspecialchars(substr($trimmed, 0, 20)) . "<br>";
    }

    // Verificar namespace
    if (strpos($content, 'namespace App\\Models;') !== false) {
        echo "✓ Namespace correto: namespace App\\Models;<br>";
    } else {
        echo "✗ <strong style='color:red'>PROBLEMA: Namespace não encontrado</strong><br>";

        // Procurar qualquer namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            echo "Namespace encontrado: " . htmlspecialchars($matches[1]) . "<br>";
        }
    }

    // Verificar classe
    if (preg_match('/class\s+ChecklistAlertaModel/', $content)) {
        echo "✓ Classe ChecklistAlertaModel encontrada<br>";
    } else {
        echo "✗ <strong style='color:red'>PROBLEMA: Classe não encontrada</strong><br>";

        // Procurar qualquer classe
        if (preg_match('/class\s+(\w+)/', $content, $matches)) {
            echo "Classe encontrada: " . htmlspecialchars($matches[1]) . "<br>";
        }
    }

    // Verificar extends Model
    if (strpos($content, 'extends Model') !== false) {
        echo "✓ Extende CodeIgniter\\Model<br>";
    } else {
        echo "⚠ Não extende Model<br>";
    }

    echo "<br><strong>Primeiras 500 caracteres do arquivo:</strong><br>";
    echo "<pre style='background:#f5f5f5; padding:10px; border:1px solid #ccc;'>";
    echo htmlspecialchars(substr($content, 0, 500));
    echo "</pre>";

} else {
    echo "✗ <strong style='color:red'>Arquivo NÃO existe no caminho esperado</strong><br><br>";

    // Listar arquivos na pasta Models
    $modelsDir = __DIR__ . '/../app/Models/';
    echo "Tentando listar arquivos em: $modelsDir<br>";

    if (is_dir($modelsDir)) {
        echo "✓ Diretório Models existe<br>";
        echo "<br><strong>Arquivos na pasta Models:</strong><br>";
        $files = scandir($modelsDir);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "✗ Diretório Models NÃO existe<br>";
    }
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
