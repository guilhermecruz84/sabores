<?php

// Script para adicionar coluna 'foto' na tabela checklists_produtos
// DELETAR APÓS USO!

$host = 'br404.hostgator.com.br';
$database = 'guil5541_sabores';
$username = 'guil5541_sabores';
$password = 'Sm2025.#';

try {
    $mysqli = new mysqli($host, $username, $password, $database);

    if ($mysqli->connect_error) {
        die("Erro de conexão: " . $mysqli->connect_error);
    }

    echo "<h1>Adicionando coluna 'foto' na tabela checklists_produtos</h1>";

    // Adicionar coluna foto
    $sql = "ALTER TABLE checklists_produtos ADD COLUMN foto VARCHAR(255) NULL AFTER observacao";

    if ($mysqli->query($sql)) {
        echo "<p style='color: green;'>✓ Coluna 'foto' adicionada com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro ao adicionar coluna: " . $mysqli->error . "</p>";
    }

    // Verificar estrutura da tabela
    echo "<h2>Estrutura atualizada da tabela checklists_produtos:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Padrão</th></tr>";

    $result = $mysqli->query("DESCRIBE checklists_produtos");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    $mysqli->close();

    echo "<hr>";
    echo "<p><strong>IMPORTANTE: Delete este arquivo (add_foto_sobras.php) após executar!</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
