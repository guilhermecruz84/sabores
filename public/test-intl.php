<?php
// Teste da extensão intl
echo "<h1>Teste da Extensão INTL</h1>";

// Verifica se a extensão está carregada
if (extension_loaded('intl')) {
    echo "<p style='color: green;'>✓ Extensão INTL está carregada</p>";

    // Testa a classe Locale
    try {
        $locale = Locale::getDefault();
        echo "<p>Locale padrão: <strong>$locale</strong></p>";

        Locale::setDefault('pt_BR');
        $newLocale = Locale::getDefault();
        echo "<p>Novo locale: <strong>$newLocale</strong></p>";

        echo "<p style='color: green;'>✓ Locale::setDefault() funciona corretamente</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erro ao usar Locale: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Extensão INTL NÃO está carregada</p>";
    echo "<p>Você precisa habilitar a extensão intl no php.ini</p>";
}

echo "<hr>";
echo "<h2>Informações do PHP</h2>";
echo "<p>Versão do PHP: " . PHP_VERSION . "</p>";
echo "<p>php.ini carregado: " . php_ini_loaded_file() . "</p>";

echo "<hr>";
echo "<h2>Extensões Carregadas</h2>";
$extensions = get_loaded_extensions();
sort($extensions);
echo "<pre>" . print_r($extensions, true) . "</pre>";
?>
