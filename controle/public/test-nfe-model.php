<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test NFe Model</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".ok{color:green;font-weight:bold;}.error{color:red;font-weight:bold;}";
echo "pre{background:#fff;padding:15px;border:1px solid #ddd;}</style></head><body>";

echo "<h1>🔍 Teste NFe Model e Database</h1>";

// Verificar se o arquivo NfeModel existe
$modelPath = dirname(__DIR__) . '/app/Models/NfeModel.php';
echo "<h2>1. Verificar Arquivo NfeModel.php</h2>";
if (file_exists($modelPath)) {
    echo "<p class='ok'>✅ Arquivo existe: $modelPath</p>";
} else {
    echo "<p class='error'>❌ Arquivo NÃO existe: $modelPath</p>";
}

// Verificar conexão com banco
echo "<h2>2. Testar Conexão com Banco de Dados</h2>";
try {
    $mysqli = new mysqli('br404.hostgator.com.br', 'guil5541_sabores', 'Sm2025.#', 'guil5541_sabores', 3306);

    if ($mysqli->connect_error) {
        echo "<p class='error'>❌ Erro de conexão: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p class='ok'>✅ Conexão OK</p>";

        // Verificar se as tabelas NFe existem
        echo "<h2>3. Verificar Tabelas NFe</h2>";

        $tables = [
            'nfe_imports',
            'nfe_docs',
            'nfe_items',
            'refeicoes',
            'servicos'
        ];

        foreach ($tables as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "<p class='ok'>✅ Tabela '$table' existe</p>";

                // Contar registros
                $count = $mysqli->query("SELECT COUNT(*) as total FROM $table");
                if ($count) {
                    $row = $count->fetch_assoc();
                    echo "<p style='margin-left:20px;'>→ Registros: " . $row['total'] . "</p>";
                }
            } else {
                echo "<p class='error'>❌ Tabela '$table' NÃO existe</p>";
            }
        }

        // Verificar estrutura da tabela nfe_imports (se existir)
        $result = $mysqli->query("DESCRIBE nfe_imports");
        if ($result) {
            echo "<h2>4. Estrutura da Tabela nfe_imports</h2>";
            echo "<pre>";
            while ($row = $result->fetch_assoc()) {
                echo $row['Field'] . " - " . $row['Type'] . "\n";
            }
            echo "</pre>";
        }

        $mysqli->close();
    }

} catch (Exception $e) {
    echo "<p class='error'>❌ Exceção: " . $e->getMessage() . "</p>";
}

// Verificar permissões da pasta writable
echo "<h2>5. Verificar Permissões</h2>";
$writablePath = dirname(__DIR__) . '/writable/logs/';
if (is_dir($writablePath)) {
    $perms = fileperms($writablePath);
    $permsOctal = substr(sprintf('%o', $perms), -4);
    echo "<p>Pasta logs: $writablePath</p>";
    echo "<p>Permissões: $permsOctal</p>";

    if (is_writable($writablePath)) {
        echo "<p class='ok'>✅ Pasta é gravável</p>";
    } else {
        echo "<p class='error'>❌ Pasta NÃO é gravável - precisa de chmod 775 ou 777</p>";
    }
} else {
    echo "<p class='error'>❌ Pasta não existe: $writablePath</p>";
}

echo "<hr><p><small>Data/Hora: " . date('Y-m-d H:i:s') . "</small></p>";
echo "</body></html>";
?>
