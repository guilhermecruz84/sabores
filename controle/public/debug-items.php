<?php
require_once '../app/Config/Paths.php';
$paths = new Config\Paths();
require_once FCPATH . '../app/Config/Bootstrap.php';
$app = Config\Services::codeigniter();
$app->initialize();

use App\Models\NfeModel;

$importId = $_GET['id'] ?? 28;

$model = new NfeModel();
$items = $model->get_items_by_import((int)$importId);

echo "<h2>Debug - Itens do Lote #{$importId}</h2>";
echo "<pre>";
echo "Total de itens: " . count($items) . "\n\n";
print_r($items);
echo "</pre>";
