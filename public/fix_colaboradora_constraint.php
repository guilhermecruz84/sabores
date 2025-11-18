<?php

// Script para remover constraint UNIQUE da tabela avaliacoes_colaboradores
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

    echo "<h1>Corrigindo constraint da tabela avaliacoes_colaboradores</h1>";

    // Remover constraint UNIQUE empresa_data_unica
    $sql = "ALTER TABLE avaliacoes_colaboradores DROP INDEX empresa_data_unica";

    if ($mysqli->query($sql)) {
        echo "<p style='color: green;'>✓ Constraint UNIQUE removida com sucesso!</p>";
        echo "<p>Agora é possível ter múltiplas avaliações de colaboradoras no mesmo dia.</p>";
    } else {
        echo "<p style='color: red;'>✗ Erro ao remover constraint: " . $mysqli->error . "</p>";
    }

    // Verificar índices da tabela
    echo "<h2>Índices atuais da tabela avaliacoes_colaboradores:</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Nome do Índice</th><th>Coluna</th><th>Tipo</th></tr>";

    $result = $mysqli->query("SHOW INDEX FROM avaliacoes_colaboradores");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Key_name'] . "</td>";
        echo "<td>" . $row['Column_name'] . "</td>";
        echo "<td>" . ($row['Non_unique'] == 0 ? 'UNIQUE' : 'INDEX') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    $mysqli->close();

    echo "<hr>";
    echo "<p><strong>IMPORTANTE: Delete este arquivo (fix_colaboradora_constraint.php) após executar!</strong></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}
