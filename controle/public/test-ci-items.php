<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Carregar CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require $pathsPath;

$paths = new Config\Paths();
require FCPATH . '../app/Config/Bootstrap.php';

echo "<h2>Teste - Itens com CodeIgniter</h2>";

$db = \Config\Database::connect();

$importId = $_GET['id'] ?? 28;

// Teste 1: Contar itens
echo "<h3>1. Total de itens no import_id={$importId}:</h3>";
$count = $db->table('nfe_items')->where('import_id', $importId)->countAllResults();
echo "Total: {$count} itens<br><br>";

// Teste 2: Query original (sem JOIN)
echo "<h3>2. Query sem JOIN:</h3>";
$items1 = $db->table('nfe_items i')
    ->select('i.id, i.doc_id, i.nItem, i.xProd')
    ->where('i.import_id', $importId)
    ->limit(3)
    ->get()
    ->getResultArray();

echo "Retornou: " . count($items1) . " itens<br>";
if (!empty($items1)) {
    echo "<pre>" . print_r($items1, true) . "</pre>";
} else {
    echo "❌ Array vazio!<br>";
}

// Teste 3: Query COM JOIN
echo "<h3>3. Query com JOIN:</h3>";
$items2 = $db->table('nfe_items i')
    ->select('i.id, i.doc_id, i.nItem, i.xProd, d.dhEmi')
    ->join('nfe_docs d', 'd.id = i.doc_id', 'left')
    ->where('i.import_id', $importId)
    ->limit(3)
    ->get()
    ->getResultArray();

echo "Retornou: " . count($items2) . " itens<br>";
if (!empty($items2)) {
    echo "<pre>" . print_r($items2, true) . "</pre>";
} else {
    echo "❌ Array vazio!<br>";
}

echo "<h3>4. Última query executada:</h3>";
echo "<pre>" . $db->getLastQuery() . "</pre>";

// Teste 4: Usar o Model diretamente
echo "<h3>5. Usando NfeModel:</h3>";
require_once FCPATH . '../app/Models/NfeModel.php';
$model = new \App\Models\NfeModel();
$items3 = $model->get_items_by_import($importId);

echo "Retornou: " . count($items3) . " itens<br>";
if (!empty($items3)) {
    echo "<pre>" . print_r(array_slice($items3, 0, 2), true) . "</pre>";
} else {
    echo "❌ Array vazio!<br>";
}
