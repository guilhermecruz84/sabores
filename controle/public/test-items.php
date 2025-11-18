<?php
// Teste rápido de itens
require_once '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once FCPATH . '../app/Config/Bootstrap.php';

$db = \Config\Database::connect();

$importId = $_GET['id'] ?? 28;

echo "<h2>Teste - Itens do Lote #{$importId}</h2>";

// Teste 1: Query original (sem JOIN)
echo "<h3>1. Query Original (sem JOIN):</h3>";
$items1 = $db->table('nfe_items i')
    ->select('i.id, i.doc_id, i.nItem, i.cProd, i.xProd, i.uCom, i.qCom, i.vUnCom, i.vProd, i.vDesc, i.vItem')
    ->where('i.import_id', $importId)
    ->orderBy('i.doc_id', 'ASC')
    ->orderBy('i.nItem', 'ASC')
    ->get()->getResultArray();

echo "Total: " . count($items1) . " itens<br>";
if (!empty($items1)) {
    echo "<pre>" . print_r(array_slice($items1, 0, 2), true) . "</pre>";
}

// Teste 2: Query com JOIN
echo "<h3>2. Query com JOIN:</h3>";
$items2 = $db->table('nfe_items i')
    ->select('i.id, i.doc_id, i.nItem, i.cProd, i.xProd, i.uCom, i.qCom, i.vUnCom, i.vProd, i.vDesc, i.vItem, d.dhEmi, d.numero, d.serie')
    ->join('nfe_docs d', 'd.id = i.doc_id', 'left')
    ->where('i.import_id', $importId)
    ->orderBy('i.doc_id', 'ASC')
    ->orderBy('i.nItem', 'ASC')
    ->get()->getResultArray();

echo "Total: " . count($items2) . " itens<br>";
if (!empty($items2)) {
    echo "<pre>" . print_r(array_slice($items2, 0, 2), true) . "</pre>";
}

// Teste 3: Último SQL executado
echo "<h3>3. Última Query SQL:</h3>";
echo "<pre>" . $db->getLastQuery() . "</pre>";

// Teste 4: Verificar se existem itens na tabela
echo "<h3>4. Total de itens na tabela nfe_items:</h3>";
$total = $db->table('nfe_items')->where('import_id', $importId)->countAllResults();
echo "Total: {$total} itens para import_id={$importId}";
