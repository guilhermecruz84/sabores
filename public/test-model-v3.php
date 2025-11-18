<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste ChecklistAlertaModel v3 - Diagnóstico Completo</h1><hr>";

// Mostrar o diretório atual
echo "<h2>Informações do Sistema</h2>";
echo "Diretório atual (__DIR__): " . __DIR__ . "<br>";
echo "Diretório atual (getcwd): " . getcwd() . "<br>";
echo "Script filename: " . __FILE__ . "<br>";

// Tentar diferentes caminhos
$caminhosPossiveis = [
    __DIR__ . '/../app/Models/ChecklistAlertaModel.php',
    dirname(__DIR__) . '/app/Models/ChecklistAlertaModel.php',
    '/home1/guil5541/saboresemmovimento.com.br/controle/app/Models/ChecklistAlertaModel.php',
];

echo "<hr><h2>Testando Caminhos Possíveis</h2>";
foreach ($caminhosPossiveis as $idx => $caminho) {
    echo "<strong>Caminho " . ($idx + 1) . ":</strong> $caminho<br>";
    if (file_exists($caminho)) {
        echo "✓ <span style='color:green'>EXISTE!</span><br>";
        $tamanho = filesize($caminho);
        echo "&nbsp;&nbsp;Tamanho: $tamanho bytes<br>";

        if ($tamanho > 0) {
            $conteudo = file_get_contents($caminho);
            echo "&nbsp;&nbsp;Primeiros 200 caracteres:<br>";
            echo "&nbsp;&nbsp;<pre style='background:#f5f5f5;padding:5px;'>" . htmlspecialchars(substr($conteudo, 0, 200)) . "</pre>";
        }
    } else {
        echo "✗ <span style='color:red'>NÃO EXISTE</span><br>";
    }
    echo "<br>";
}

echo "<hr><h2>Listar Conteúdo da Pasta app/</h2>";
$appDir = dirname(__DIR__) . '/app';
echo "Caminho: $appDir<br><br>";

if (is_dir($appDir)) {
    echo "✓ Pasta app/ existe<br>";
    echo "<strong>Conteúdo:</strong><br><ul>";
    $items = scandir($appDir);
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $fullPath = $appDir . '/' . $item;
            if (is_dir($fullPath)) {
                echo "<li>📁 $item/</li>";
            } else {
                echo "<li>📄 $item</li>";
            }
        }
    }
    echo "</ul>";

    // Listar Models
    $modelsDir = $appDir . '/Models';
    if (is_dir($modelsDir)) {
        echo "<br><strong>Conteúdo de Models/:</strong><br><ul>";
        $models = scandir($modelsDir);
        foreach ($models as $model) {
            if ($model != '.' && $model != '..') {
                echo "<li>$model";
                if ($model == 'ChecklistAlertaModel.php') {
                    echo " <strong style='color:green'>← ENCONTRADO!</strong>";
                }
                echo "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<br>✗ Pasta Models/ não existe<br>";
    }

} else {
    echo "✗ Pasta app/ não existe no caminho esperado<br>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
