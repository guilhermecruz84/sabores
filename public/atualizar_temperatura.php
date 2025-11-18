<?php
/**
 * Script para atualizar itens de temperatura no checklist
 * Execute este arquivo acessando: http://localhost:8080/atualizar_temperatura.php
 */

// Configurações do banco
$host = 'br404.hostgator.com.br';
$db   = 'guil5541_sabores';
$user = 'guil5541_sabores';
$pass = 'Sm2025.#';
$port = 3306;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2 style='color: green;'>✓ Conectado ao banco de dados com sucesso!</h2>";

    // Atualizar item de temperatura dos alimentos quentes
    $sql1 = "UPDATE checklists_itens
             SET tipo_resposta = 'numero',
                 descricao = 'Temperatura dos alimentos quentes (°C) - Deve estar acima de 60°C'
             WHERE id = 2 AND tipo = 'abertura'";

    $pdo->exec($sql1);
    echo "<p style='color: green;'>✓ Item de temperatura de alimentos quentes atualizado</p>";

    // Atualizar item de temperatura dos alimentos frios
    $sql2 = "UPDATE checklists_itens
             SET tipo_resposta = 'numero',
                 descricao = 'Temperatura dos alimentos frios (°C) - Deve estar abaixo de 10°C'
             WHERE id = 3 AND tipo = 'abertura'";

    $pdo->exec($sql2);
    echo "<p style='color: green;'>✓ Item de temperatura de alimentos frios atualizado</p>";

    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Atualização concluída com sucesso!</h3>";
    echo "<p>Agora os operadores poderão registrar os valores exatos das temperaturas.</p>";
    echo "<p><a href='/checklists/itens' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ver Itens do Checklist</a></p>";
    echo "<p><a href='/' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Voltar ao Sistema</a></p>";
    echo "<p style='color: #999; margin-top: 20px;'><small>Você pode deletar este arquivo após a execução.</small></p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>✗ Erro!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
