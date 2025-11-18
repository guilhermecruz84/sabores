<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste ChecklistAlertaModel</h1><hr>";

// Definir caminhos
define('ROOTPATH', realpath('../') . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);

echo "<h2>1. Verificar arquivo</h2>";
$modelPath = APPPATH . 'Models/ChecklistAlertaModel.php';
if (file_exists($modelPath)) {
    echo "✓ Arquivo existe: $modelPath<br>";
    echo "Tamanho: " . filesize($modelPath) . " bytes<br>";

    // Ler primeiros bytes para verificar BOM
    $content = file_get_contents($modelPath);
    if (substr($content, 0, 3) == "\xEF\xBB\xBF") {
        echo "⚠ PROBLEMA: Arquivo tem BOM UTF-8!<br>";
    } else {
        echo "✓ Arquivo sem BOM<br>";
    }

    // Verificar se começa com <?php
    if (substr(ltrim($content), 0, 5) == '<?php') {
        echo "✓ Arquivo começa com &lt;?php<br>";
    } else {
        echo "✗ PROBLEMA: Arquivo não começa com &lt;?php<br>";
    }

    // Verificar namespace
    if (strpos($content, 'namespace App\\Models;') !== false) {
        echo "✓ Namespace correto encontrado<br>";
    } else {
        echo "✗ PROBLEMA: Namespace não encontrado ou incorreto<br>";
    }

    // Verificar nome da classe
    if (strpos($content, 'class ChecklistAlertaModel') !== false) {
        echo "✓ Declaração da classe encontrada<br>";
    } else {
        echo "✗ PROBLEMA: Declaração da classe não encontrada<br>";
    }

} else {
    echo "✗ Arquivo NÃO existe<br>";
}

echo "<hr>";

echo "<h2>2. Tentar carregar via autoloader</h2>";
try {
    require_once ROOTPATH . 'vendor/autoload.php';
    echo "✓ Autoloader carregado<br>";

    // Tentar incluir manualmente
    require_once $modelPath;
    echo "✓ Arquivo incluído manualmente<br>";

    // Verificar se a classe existe
    if (class_exists('App\\Models\\ChecklistAlertaModel', false)) {
        echo "✓ Classe App\\Models\\ChecklistAlertaModel existe!<br>";

        // Tentar instanciar
        $model = new \App\Models\ChecklistAlertaModel();
        echo "✓ Model instanciado com sucesso!<br>";

    } else {
        echo "✗ Classe não foi encontrada após incluir o arquivo<br>";

        // Listar classes declaradas que contém "Alerta"
        $classes = get_declared_classes();
        $alertaClasses = array_filter($classes, function($class) {
            return stripos($class, 'Alerta') !== false;
        });

        if (!empty($alertaClasses)) {
            echo "<br>Classes com 'Alerta' encontradas:<br>";
            echo "<pre>" . print_r($alertaClasses, true) . "</pre>";
        }
    }

} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
