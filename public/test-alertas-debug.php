<?php
// Script de debug para testar o controller de Alertas

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug do Sistema de Alertas</h1>";
echo "<hr>";

// Definir caminhos
define('ROOTPATH', realpath('../') . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);

echo "<h2>1. Verificar arquivos</h2>";
$arquivos = [
    'Controller' => APPPATH . 'Controllers/Alertas.php',
    'Model' => APPPATH . 'Models/ChecklistAlertaModel.php',
    'View Index' => APPPATH . 'Views/alertas/index.php',
    'View Historico' => APPPATH . 'Views/alertas/historico.php'
];

foreach ($arquivos as $nome => $caminho) {
    if (file_exists($caminho)) {
        echo "✓ $nome existe<br>";
    } else {
        echo "✗ $nome NÃO existe: $caminho<br>";
    }
}

echo "<hr>";

// Teste 2: Tentar carregar o controller manualmente
echo "<h2>2. Testar carregamento do Controller</h2>";
try {
    // Carregar autoloader do CodeIgniter
    require_once ROOTPATH . 'vendor/autoload.php';

    echo "✓ Autoloader carregado<br>";

    // Tentar instanciar o model
    if (class_exists('App\Models\ChecklistAlertaModel')) {
        echo "✓ Classe ChecklistAlertaModel encontrada<br>";

        // Tentar criar instância do model
        try {
            $model = new \App\Models\ChecklistAlertaModel();
            echo "✓ Model instanciado com sucesso<br>";
        } catch (Exception $e) {
            echo "✗ ERRO ao instanciar model: " . $e->getMessage() . "<br>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        echo "✗ Classe ChecklistAlertaModel não encontrada<br>";
    }

} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";

// Teste 3: Verificar sintaxe dos arquivos PHP
echo "<h2>3. Verificar sintaxe dos arquivos</h2>";

$controller = file_get_contents(APPPATH . 'Controllers/Alertas.php');
$model = file_get_contents(APPPATH . 'Models/ChecklistAlertaModel.php');

// Verificar se tem BOM ou caracteres estranhos
if (substr($controller, 0, 3) == "\xEF\xBB\xBF") {
    echo "⚠ Alertas.php tem BOM UTF-8 (pode causar problemas)<br>";
} else {
    echo "✓ Alertas.php sem BOM<br>";
}

if (substr($model, 0, 3) == "\xEF\xBB\xBF") {
    echo "⚠ ChecklistAlertaModel.php tem BOM UTF-8 (pode causar problemas)<br>";
} else {
    echo "✓ ChecklistAlertaModel.php sem BOM<br>";
}

// Verificar erros de sintaxe
echo "<br><strong>Verificando sintaxe PHP:</strong><br>";
$temp_controller = tempnam(sys_get_temp_dir(), 'php');
file_put_contents($temp_controller, $controller);
exec("php -l $temp_controller 2>&1", $output_controller, $return_controller);
if ($return_controller === 0) {
    echo "✓ Alertas.php: Sintaxe OK<br>";
} else {
    echo "✗ Alertas.php: ERRO de sintaxe<br><pre>" . implode("\n", $output_controller) . "</pre>";
}
unlink($temp_controller);

$temp_model = tempnam(sys_get_temp_dir(), 'php');
file_put_contents($temp_model, $model);
exec("php -l $temp_model 2>&1", $output_model, $return_model);
if ($return_model === 0) {
    echo "✓ ChecklistAlertaModel.php: Sintaxe OK<br>";
} else {
    echo "✗ ChecklistAlertaModel.php: ERRO de sintaxe<br><pre>" . implode("\n", $output_model) . "</pre>";
}
unlink($temp_model);

echo "<hr>";

// Teste 4: Verificar banco de dados
echo "<h2>4. Testar conexão e tabelas do banco</h2>";
try {
    require_once APPPATH . 'Config/Database.php';
    $db = \Config\Database::connect();

    echo "✓ Conexão com banco OK<br>";

    // Verificar tabela checklist_alertas
    $query = $db->query("SHOW TABLES LIKE 'checklist_alertas'");
    if ($query->getNumRows() > 0) {
        echo "✓ Tabela checklist_alertas existe<br>";

        // Contar registros
        $count = $db->query("SELECT COUNT(*) as total FROM checklist_alertas")->getRow();
        echo "&nbsp;&nbsp;→ Total de alertas: " . $count->total . "<br>";
    } else {
        echo "✗ Tabela checklist_alertas NÃO existe<br>";
    }

    // Verificar campo gera_alerta
    $query = $db->query("SHOW COLUMNS FROM checklists_itens LIKE 'gera_alerta'");
    if ($query->getNumRows() > 0) {
        echo "✓ Campo gera_alerta existe em checklists_itens<br>";
    } else {
        echo "✗ Campo gera_alerta NÃO existe em checklists_itens<br>";
    }

} catch (Exception $e) {
    echo "✗ ERRO no banco: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";

// Teste 5: Simular requisição ao controller
echo "<h2>5. Tentar executar o controller</h2>";
try {
    // Inicializar sessão
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Simular usuário logado (Admin)
    $_SESSION['usuario_id'] = 1;
    $_SESSION['perfil'] = 'Admin';
    $_SESSION['tipo'] = 'admin';

    echo "✓ Sessão configurada<br>";
    echo "✓ Tentando carregar o controller...<br>";

    // Incluir o controller manualmente
    require_once APPPATH . 'Controllers/Alertas.php';

    echo "✓ Arquivo do controller incluído<br>";

} catch (Exception $e) {
    echo "✗ ERRO ao executar controller: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "✗ ERRO FATAL: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
