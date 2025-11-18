<?php
// Script de teste para verificar o sistema de alertas

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste do Sistema de Alertas</h1>";
echo "<hr>";

// Teste 1: Verificar arquivo do Controller
$controllerPath = '../app/Controllers/Alertas.php';
echo "<h2>1. Verificar arquivo Alertas.php</h2>";
if (file_exists($controllerPath)) {
    echo "✓ Arquivo existe<br>";

    // Tentar incluir o arquivo
    try {
        require_once '../app/Config/Autoload.php';
        require_once '../vendor/autoload.php';

        if (class_exists('App\Controllers\Alertas')) {
            echo "✓ Classe App\\Controllers\\Alertas existe<br>";
        } else {
            echo "✗ Classe não encontrada<br>";
        }
    } catch (Exception $e) {
        echo "✗ ERRO: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ Arquivo NÃO existe<br>";
}

echo "<hr>";

// Teste 2: Verificar Model
$modelPath = '../app/Models/ChecklistAlertaModel.php';
echo "<h2>2. Verificar arquivo ChecklistAlertaModel.php</h2>";
if (file_exists($modelPath)) {
    echo "✓ Arquivo existe<br>";
} else {
    echo "✗ Arquivo NÃO existe<br>";
}

echo "<hr>";

// Teste 3: Verificar Views
echo "<h2>3. Verificar Views de Alertas</h2>";
$views = [
    '../app/Views/alertas/index.php',
    '../app/Views/alertas/historico.php'
];

foreach ($views as $view) {
    if (file_exists($view)) {
        echo "✓ " . basename($view) . " existe<br>";
    } else {
        echo "✗ " . basename($view) . " NÃO existe<br>";
    }
}

echo "<hr>";

// Teste 4: Verificar tabela no banco
echo "<h2>4. Verificar tabela checklist_alertas no banco</h2>";
try {
    require_once '../app/Config/Database.php';

    $config = config('Database');
    $db = \Config\Database::connect();

    // Tentar consultar a tabela
    $query = $db->query("SHOW TABLES LIKE 'checklist_alertas'");
    if ($query->getNumRows() > 0) {
        echo "✓ Tabela checklist_alertas existe<br>";

        // Verificar estrutura
        $query = $db->query("DESCRIBE checklist_alertas");
        echo "<br><strong>Estrutura da tabela:</strong><br>";
        echo "<pre>";
        print_r($query->getResultArray());
        echo "</pre>";
    } else {
        echo "✗ Tabela checklist_alertas NÃO existe<br>";
        echo "<strong>Execute o arquivo install_alertas.sql no banco de dados!</strong><br>";
    }
} catch (Exception $e) {
    echo "✗ ERRO ao conectar ao banco: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Teste 5: Verificar campo gera_alerta na tabela checklists_itens
echo "<h2>5. Verificar campo gera_alerta em checklists_itens</h2>";
try {
    $query = $db->query("DESCRIBE checklists_itens");
    $fields = $query->getResultArray();
    $geraAlertaExists = false;

    foreach ($fields as $field) {
        if ($field['Field'] == 'gera_alerta') {
            $geraAlertaExists = true;
            break;
        }
    }

    if ($geraAlertaExists) {
        echo "✓ Campo gera_alerta existe na tabela checklists_itens<br>";
    } else {
        echo "✗ Campo gera_alerta NÃO existe<br>";
        echo "<strong>Execute o SQL: ALTER TABLE checklists_itens ADD COLUMN gera_alerta TINYINT(1) NOT NULL DEFAULT 0 AFTER requer_foto;</strong><br>";
    }
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>IMPORTANTE: DELETE este arquivo após o teste!</strong></p>";
?>
