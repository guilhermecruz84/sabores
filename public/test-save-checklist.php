<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Salvamento de Checklist</h1><hr>";

// Simular ambiente CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('ROOTPATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('WRITEPATH', ROOTPATH . 'writable' . DIRECTORY_SEPARATOR);

echo "<h2>1. Verificar Autoloader</h2>";
try {
    require_once ROOTPATH . 'vendor/autoload.php';
    echo "✓ Autoloader carregado<br>";
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "<br>";
    die();
}

echo "<hr><h2>2. Tentar carregar ChecklistAlertaModel</h2>";
try {
    // Verificar se a classe pode ser carregada
    if (class_exists('App\\Models\\ChecklistAlertaModel')) {
        echo "✓ Classe ChecklistAlertaModel pode ser encontrada pelo autoloader<br>";
    } else {
        echo "✗ Classe NÃO encontrada pelo autoloader<br>";
        echo "Tentando incluir manualmente...<br>";
        require_once APPPATH . 'Models/ChecklistAlertaModel.php';

        if (class_exists('App\\Models\\ChecklistAlertaModel')) {
            echo "✓ Classe carregada manualmente<br>";
        } else {
            echo "✗ ERRO: Classe ainda não existe após include manual<br>";
        }
    }
} catch (Exception $e) {
    echo "✗ ERRO ao carregar classe: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><h2>3. Tentar instanciar o Model</h2>";
try {
    $alertaModel = new \App\Models\ChecklistAlertaModel();
    echo "✓ ChecklistAlertaModel instanciado com sucesso!<br>";
    echo "Tipo: " . get_class($alertaModel) . "<br>";
} catch (Exception $e) {
    echo "✗ ERRO ao instanciar: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "✗ ERRO FATAL: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><h2>4. Verificar método gerarAlertasNaoConformidades no Checklists</h2>";
$checklistsPath = APPPATH . 'Controllers/Checklists.php';
if (file_exists($checklistsPath)) {
    $content = file_get_contents($checklistsPath);

    if (strpos($content, 'function gerarAlertasNaoConformidades') !== false) {
        echo "✓ Método gerarAlertasNaoConformidades existe<br>";
    } else {
        echo "✗ Método gerarAlertasNaoConformidades NÃO encontrado<br>";
    }

    if (strpos($content, 'new \App\Models\ChecklistAlertaModel') !== false ||
        strpos($content, 'new ChecklistAlertaModel') !== false) {
        echo "✓ Controller tenta instanciar ChecklistAlertaModel<br>";
    } else {
        echo "⚠ Controller pode não estar instanciando o model corretamente<br>";
    }
} else {
    echo "✗ Checklists.php não encontrado<br>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
