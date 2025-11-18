<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Debug Completo - Capturar Erro Real</h1><hr>";

// 1. Verificar PHP
echo "<h2>1. Informações do PHP</h2>";
echo "Versão PHP: " . phpversion() . "<br>";
echo "display_errors: " . ini_get('display_errors') . "<br>";
echo "error_reporting: " . error_reporting() . "<br><br>";

// 2. Verificar arquivo Alertas.php
echo "<h2>2. Verificar Alertas.php</h2>";
$alertasPath = dirname(__DIR__) . '/app/Controllers/Alertas.php';
echo "Caminho: $alertasPath<br>";

if (file_exists($alertasPath)) {
    echo "✓ Arquivo existe<br>";

    $content = file_get_contents($alertasPath);

    // Verificar se verificarLogin está protected
    if (preg_match('/protected\s+function\s+verificarLogin/', $content)) {
        echo "✓ <span style='color:green'>verificarLogin() está PROTECTED</span><br>";
    } elseif (preg_match('/private\s+function\s+verificarLogin/', $content)) {
        echo "✗ <span style='color:red'>verificarLogin() está PRIVATE (ERRO!)</span><br>";
    } else {
        echo "⚠ verificarLogin() não encontrado<br>";
    }

    // Verificar uso de usuario_tipo
    if (strpos($content, "get('usuario_tipo')") !== false) {
        echo "✓ Usa usuario_tipo corretamente<br>";
    } elseif (strpos($content, "get('perfil')") !== false) {
        echo "✗ <span style='color:red'>Usa 'perfil' (ERRO! Deve ser 'usuario_tipo')</span><br>";
    }

    // Verificar valores de acesso
    if (strpos($content, "['admin', 'atendente']") !== false) {
        echo "✓ Valores corretos: ['admin', 'atendente']<br>";
    } elseif (strpos($content, "['Admin', 'Administrativo']") !== false) {
        echo "✗ <span style='color:red'>Valores errados: ['Admin', 'Administrativo']</span><br>";
    }

    echo "<br><strong>Última modificação:</strong> " . date("Y-m-d H:i:s", filemtime($alertasPath)) . "<br>";

} else {
    echo "✗ <span style='color:red'>Arquivo NÃO existe!</span><br>";
}

// 3. Verificar ChecklistAlertaModel.php
echo "<hr><h2>3. Verificar ChecklistAlertaModel.php</h2>";
$modelPath = dirname(__DIR__) . '/app/Models/ChecklistAlertaModel.php';
echo "Caminho: $modelPath<br>";

if (file_exists($modelPath)) {
    echo "✓ Arquivo existe<br>";
    echo "Última modificação: " . date("Y-m-d H:i:s", filemtime($modelPath)) . "<br>";
} else {
    echo "✗ <span style='color:red'>Arquivo NÃO existe!</span><br>";
}

// 4. Verificar Checklists.php
echo "<hr><h2>4. Verificar Checklists.php (método gerarAlertasNaoConformidades)</h2>";
$checklistsPath = dirname(__DIR__) . '/app/Controllers/Checklists.php';

if (file_exists($checklistsPath)) {
    $content = file_get_contents($checklistsPath);

    if (strpos($content, 'function gerarAlertasNaoConformidades') !== false) {
        echo "✓ Método gerarAlertasNaoConformidades existe<br>";

        // Verificar se está sendo chamado
        if (strpos($content, 'gerarAlertasNaoConformidades(') !== false) {
            echo "✓ Método está sendo chamado<br>";
        }

        // Verificar instanciação do model
        if (strpos($content, 'new \\App\\Models\\ChecklistAlertaModel') !== false) {
            echo "✓ Instancia ChecklistAlertaModel corretamente<br>";
        } elseif (strpos($content, 'new ChecklistAlertaModel') !== false) {
            echo "⚠ Instancia sem namespace completo<br>";
        }

    } else {
        echo "✗ <span style='color:red'>Método gerarAlertasNaoConformidades NÃO encontrado</span><br>";
    }

    echo "Última modificação: " . date("Y-m-d H:i:s", filemtime($checklistsPath)) . "<br>";
}

// 5. TENTAR REPLICAR O ERRO
echo "<hr><h2>5. Tentar Replicar o Erro (Instanciar Alertas)</h2>";
try {
    // Carregar autoloader
    require_once dirname(__DIR__) . '/vendor/autoload.php';
    echo "✓ Autoloader carregado<br>";

    // Tentar carregar a classe
    if (class_exists('App\\Controllers\\Alertas')) {
        echo "✓ Classe Alertas pode ser carregada<br>";

        // Tentar instanciar (isso pode gerar o erro)
        try {
            $reflection = new \ReflectionClass('App\\Controllers\\Alertas');
            echo "✓ ReflectionClass criada<br>";

            $method = $reflection->getMethod('verificarLogin');
            $modifiers = \Reflection::getModifierNames($method->getModifiers());

            echo "<br><strong>Modificadores de verificarLogin():</strong> " . implode(', ', $modifiers) . "<br>";

            if (in_array('protected', $modifiers)) {
                echo "✓ <span style='color:green'>verificarLogin() é PROTECTED (correto!)</span><br>";
            } elseif (in_array('private', $modifiers)) {
                echo "✗ <span style='color:red'>verificarLogin() é PRIVATE (ERRO!)</span><br>";
            }

        } catch (ReflectionException $e) {
            echo "⚠ Não conseguiu refletir o método: " . $e->getMessage() . "<br>";
        }

    } else {
        echo "✗ Classe Alertas NÃO pode ser carregada<br>";
    }

} catch (Exception $e) {
    echo "✗ <span style='color:red'>ERRO: " . $e->getMessage() . "</span><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// 6. Ler últimas linhas do log
echo "<hr><h2>6. Últimas 20 Linhas do Log</h2>";
$logPath = dirname(__DIR__) . '/writable/logs/log-' . date('Y-m-d') . '.log';

if (file_exists($logPath)) {
    $lines = file($logPath);
    $lastLines = array_slice($lines, -20);

    echo "<pre style='background:#f5f5f5;padding:10px;border:1px solid #ccc;overflow-x:auto;font-size:12px;'>";
    foreach ($lastLines as $line) {
        // Colorir linhas de erro
        if (strpos($line, 'CRITICAL') !== false || strpos($line, 'ERROR') !== false) {
            echo "<span style='color:red;font-weight:bold;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "Log de hoje não encontrado: $logPath<br>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
