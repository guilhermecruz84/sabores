<?php

// Script temporário para corrigir o banco de dados
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

    echo "<h1>Corrigindo estrutura do banco de dados</h1>";

    // Tornar cardapio_id nullable
    $sql = "ALTER TABLE avaliacoes_cardapio MODIFY COLUMN cardapio_id INT(11) UNSIGNED NULL";

    if ($mysqli->query($sql)) {
        echo "<p style='color: green;'>✓ Coluna cardapio_id alterada para NULLABLE com sucesso!</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro ao alterar coluna: " . $mysqli->error . "</p>";
    }

    // Verificar a estrutura
    echo "<h2>Estrutura da tabela avaliacoes_cardapio:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    $result = $mysqli->query("DESCRIBE avaliacoes_cardapio");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    $mysqli->close();

    echo "<hr>";
    echo "<p><strong>IMPORTANTE: Delete este arquivo (fix_db.php) após executar!</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
