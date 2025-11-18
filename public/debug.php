<?php
// Script de debug temporário - REMOVER após identificar o problema

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug do Sistema</h1>";
echo "<hr>";

// 1. Verificar versão do PHP
echo "<h2>1. Versão do PHP</h2>";
echo "Versão: " . PHP_VERSION . "<br>";
echo "Requisito mínimo: 8.1<br>";
echo "Status: " . (version_compare(PHP_VERSION, '8.1', '>=') ? '✓ OK' : '✗ ERRO') . "<br>";

// 2. Verificar caminhos
echo "<h2>2. Caminhos</h2>";
echo "Diretório atual: " . __DIR__ . "<br>";
echo "FCPATH: " . (defined('FCPATH') ? FCPATH : 'Não definido') . "<br>";

// 3. Verificar se o arquivo de configuração existe
echo "<h2>3. Arquivos Importantes</h2>";
$appPath = dirname(__DIR__) . '/app';
$envPath = dirname(__DIR__) . '/.env';
$pathsPath = $appPath . '/Config/Paths.php';

echo ".env existe: " . (file_exists($envPath) ? '✓ SIM' : '✗ NÃO') . "<br>";
echo "Paths.php existe: " . (file_exists($pathsPath) ? '✓ SIM' : '✗ NÃO') . "<br>";
echo "app/ existe: " . (is_dir($appPath) ? '✓ SIM' : '✗ NÃO') . "<br>";

// 4. Verificar diretórios writable
echo "<h2>4. Permissões de Escrita</h2>";
$writablePath = dirname(__DIR__) . '/writable';
$sessionPath = $writablePath . '/session';

echo "writable/ existe: " . (is_dir($writablePath) ? '✓ SIM' : '✗ NÃO') . "<br>";
echo "writable/ é gravável: " . (is_writable($writablePath) ? '✓ SIM' : '✗ NÃO') . "<br>";
echo "writable/session/ existe: " . (is_dir($sessionPath) ? '✓ SIM' : '✗ NÃO') . "<br>";
echo "writable/session/ é gravável: " . (is_writable($sessionPath) ? '✓ SIM' : '✗ NÃO') . "<br>";

// 5. Verificar conteúdo do .env
if (file_exists($envPath)) {
    echo "<h2>5. Conteúdo do .env</h2>";
    echo "<pre>";
    $envContent = file_get_contents($envPath);
    // Ocultar senha do banco
    $envContent = preg_replace('/(database\.default\.password\s*=\s*)(.+)/i', '$1***OCULTO***', $envContent);
    echo htmlspecialchars($envContent);
    echo "</pre>";
}

// 6. Tentar carregar o CodeIgniter
echo "<h2>6. Teste de Carregamento do CodeIgniter</h2>";
try {
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

    if (file_exists(FCPATH . '../app/Config/Paths.php')) {
        require FCPATH . '../app/Config/Paths.php';
        echo "✓ Paths.php carregado com sucesso<br>";

        $paths = new Config\Paths();
        echo "✓ Objeto Paths criado com sucesso<br>";
        echo "System Directory: " . $paths->systemDirectory . "<br>";

        if (file_exists($paths->systemDirectory . '/Boot.php')) {
            echo "✓ Boot.php encontrado<br>";
        } else {
            echo "✗ Boot.php não encontrado em: " . $paths->systemDirectory . "<br>";
        }
    } else {
        echo "✗ Paths.php não encontrado<br>";
    }
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>IMPORTANTE:</strong> Remova este arquivo (debug.php) após identificar o problema por segurança.</p>";
?>
