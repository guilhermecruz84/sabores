<?php
// Script de teste de rotas

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Teste de Rotas - Sabores</h1>";
echo "<hr>";

// Teste 1: Verificar arquivo do Controller
$controllerPath = '../app/Controllers/Checklists.php';
echo "<h2>1. Verificar arquivo Checklists.php</h2>";
if (file_exists($controllerPath)) {
    echo "✓ Arquivo existe<br>";
    echo "Tamanho: " . filesize($controllerPath) . " bytes<br>";
    echo "Última modificação: " . date('d/m/Y H:i:s', filemtime($controllerPath)) . "<br>";

    // Tentar incluir o arquivo
    try {
        require_once $controllerPath;
        echo "✓ Arquivo pode ser carregado sem erros de sintaxe<br>";

        if (class_exists('App\Controllers\Checklists')) {
            echo "✓ Classe App\Controllers\Checklists existe<br>";

            $reflection = new ReflectionClass('App\Controllers\Checklists');
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            echo "<br><strong>Métodos públicos encontrados:</strong><ul>";
            foreach ($methods as $method) {
                if ($method->class == 'App\Controllers\Checklists') {
                    echo "<li>" . $method->name . "</li>";
                }
            }
            echo "</ul>";

        } else {
            echo "✗ Classe não encontrada<br>";
        }
    } catch (Exception $e) {
        echo "✗ ERRO ao carregar arquivo: " . $e->getMessage() . "<br>";
    }
} else {
    echo "✗ Arquivo NÃO existe no caminho: " . realpath($controllerPath) . "<br>";
}

echo "<hr>";

// Teste 2: Verificar rotas
echo "<h2>2. Testar URLs</h2>";
$baseUrl = 'https://www.saboresemmovimento.com.br/controle';
echo "<ul>";
echo "<li><a href='{$baseUrl}/'>Home/Login</a></li>";
echo "<li><a href='{$baseUrl}/dashboard'>Dashboard</a></li>";
echo "<li><a href='{$baseUrl}/checklists'>Checklists (lista)</a></li>";
echo "<li><a href='{$baseUrl}/checklists/itens'>Gerenciar Itens</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Se este arquivo está funcionando, o PHP está OK.</strong></p>";
echo "<p>Se as rotas dão 404, o problema pode ser:</p>";
echo "<ul>";
echo "<li>Arquivo Checklists.php corrompido no servidor</li>";
echo "<li>Permissões incorretas (deve ser 644)</li>";
echo "<li>Cache do CodeIgniter</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='color: red;'><strong>IMPORTANTE: DELETE este arquivo após o teste!</strong></p>";
?>
