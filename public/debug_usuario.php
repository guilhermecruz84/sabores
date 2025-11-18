<?php
session_start();

echo "<h2>Debug do Usuário Logado</h2>";

if (isset($_SESSION['usuario'])) {
    echo "<h3 style='color: green;'>✓ Usuário está logado</h3>";
    echo "<pre>";
    print_r($_SESSION['usuario']);
    echo "</pre>";

    $usuario = (object) $_SESSION['usuario'];
    echo "<h3>Verificações:</h3>";
    echo "<p>Tipo: <strong>" . $usuario->tipo . "</strong></p>";
    echo "<p>Tipo === 'atendente': " . ($usuario->tipo === 'atendente' ? 'SIM' : 'NÃO') . "</p>";
    echo "<p>Tipo == 'atendente': " . ($usuario->tipo == 'atendente' ? 'SIM' : 'NÃO') . "</p>";
    echo "<p>in_array: " . (in_array($usuario->tipo, ['admin', 'atendente']) ? 'SIM' : 'NÃO') . "</p>";

} else {
    echo "<h3 style='color: red;'>✗ Nenhum usuário logado</h3>";
}

echo "<hr>";
echo "<p><a href='/' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Voltar</a></p>";
