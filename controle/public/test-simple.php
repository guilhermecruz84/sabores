<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Teste Simples - Conexão Banco</h2>";

// Conectar direto no banco
$host = 'localhost';
$user = 'guil5541_sabores';
$pass = 'Gl102030@@';
$db   = 'guil5541_sabores';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro de conexão: " . mysqli_connect_error());
}

echo "✅ Conectado ao banco!<br><br>";

$importId = $_GET['id'] ?? 28;

// Query simples
$sql = "SELECT COUNT(*) as total FROM nfe_items WHERE import_id = {$importId}";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo "<h3>Total de itens no lote #{$importId}:</h3>";
echo "Total: " . $row['total'] . "<br><br>";

// Query completa
$sql2 = "SELECT i.id, i.doc_id, i.nItem, i.xProd, i.qCom, i.vItem
         FROM nfe_items i
         WHERE i.import_id = {$importId}
         LIMIT 5";

$result2 = mysqli_query($conn, $sql2);

echo "<h3>Primeiros 5 itens:</h3>";
if (mysqli_num_rows($result2) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Doc</th><th>Item</th><th>Produto</th><th>Qtd</th><th>Valor</th></tr>";
    while ($item = mysqli_fetch_assoc($result2)) {
        echo "<tr>";
        echo "<td>{$item['id']}</td>";
        echo "<td>{$item['doc_id']}</td>";
        echo "<td>{$item['nItem']}</td>";
        echo "<td>{$item['xProd']}</td>";
        echo "<td>{$item['qCom']}</td>";
        echo "<td>{$item['vItem']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Nenhum item encontrado!";
}

mysqli_close($conn);
