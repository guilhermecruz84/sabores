<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug - Cardápios do Cliente</h1><hr>";

// Definir caminhos
define('ROOTPATH', realpath('../') . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);

// Carregar autoloader
require_once ROOTPATH . 'vendor/autoload.php';

// Conectar ao banco
$db = \Config\Database::connect();

echo "<h2>1. Verificar Sessão do Usuário</h2>";
session_start();
if (isset($_SESSION['usuario_id'])) {
    echo "✓ Usuário logado<br>";
    echo "ID: " . $_SESSION['usuario_id'] . "<br>";
    echo "Tipo: " . ($_SESSION['usuario_tipo'] ?? 'N/A') . "<br>";
    echo "Empresa ID: " . ($_SESSION['usuario_empresa_id'] ?? 'N/A') . "<br>";

    $empresaId = $_SESSION['usuario_empresa_id'] ?? null;
} else {
    echo "✗ Nenhum usuário logado<br>";
    echo "<p style='color:red;'>Por favor, faça login como cliente e acesse esta página novamente.</p>";
    die();
}

echo "<hr><h2>2. Buscar Informações da Empresa</h2>";
if ($empresaId) {
    $empresa = $db->table('empresas')->where('id', $empresaId)->get()->getRow();

    if ($empresa) {
        echo "✓ Empresa encontrada<br>";
        echo "Nome: " . $empresa->nome_fantasia . "<br>";
        echo "ID: " . $empresa->id . "<br>";
    } else {
        echo "✗ <span style='color:red;'>Empresa não encontrada</span><br>";
    }
} else {
    echo "✗ <span style='color:red;'>Empresa ID não definida na sessão</span><br>";
}

echo "<hr><h2>3. Verificar Cardápios Cadastrados</h2>";
$mes = date('m');
$ano = date('Y');

echo "Buscando cardápios para:<br>";
echo "Empresa ID: $empresaId<br>";
echo "Mês: $mes<br>";
echo "Ano: $ano<br><br>";

$cardapios = $db->table('cardapios')
    ->where('empresa_id', $empresaId)
    ->where('MONTH(data)', $mes)
    ->where('YEAR(data)', $ano)
    ->orderBy('data', 'DESC')
    ->get()
    ->getResult();

if (empty($cardapios)) {
    echo "✗ <span style='color:red;'>Nenhum cardápio encontrado para este mês</span><br><br>";

    // Buscar qualquer cardápio da empresa
    echo "<strong>Buscando qualquer cardápio da empresa...</strong><br>";
    $todosCardapios = $db->table('cardapios')
        ->where('empresa_id', $empresaId)
        ->orderBy('data', 'DESC')
        ->limit(10)
        ->get()
        ->getResult();

    if (empty($todosCardapios)) {
        echo "✗ <span style='color:red;'>Nenhum cardápio cadastrado para esta empresa</span><br>";
        echo "<p><strong>SOLUÇÃO:</strong> Um administrador precisa cadastrar cardápios para esta empresa em <a href='../avaliacoes/gerenciar-cardapios'>Gerenciar Cardápios</a></p>";
    } else {
        echo "✓ Encontrados " . count($todosCardapios) . " cardápios em outros períodos:<br>";
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Data</th><th>Descrição</th></tr>";
        foreach ($todosCardapios as $card) {
            echo "<tr>";
            echo "<td>" . $card->id . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($card->data)) . "</td>";
            echo "<td>" . htmlspecialchars($card->descricao) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "✓ <span style='color:green;'>Encontrados " . count($cardapios) . " cardápios para este mês</span><br><br>";

    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>Data</th><th>Descrição</th><th>Avaliado?</th></tr>";

    $usuarioId = $_SESSION['usuario_id'];

    foreach ($cardapios as $card) {
        // Verificar se já foi avaliado
        $jaAvaliado = $db->table('avaliacoes_cardapio')
            ->where('cardapio_id', $card->id)
            ->where('cliente_id', $usuarioId)
            ->countAllResults() > 0;

        echo "<tr>";
        echo "<td>" . $card->id . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($card->data)) . "</td>";
        echo "<td>" . htmlspecialchars($card->descricao) . "</td>";
        echo "<td>" . ($jaAvaliado ? '✓ Sim' : '✗ Não') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr><h2>4. Total de Cardápios no Sistema</h2>";
$totalCardapios = $db->table('cardapios')->countAllResults();
echo "Total de cardápios cadastrados: <strong>$totalCardapios</strong><br>";

if ($totalCardapios == 0) {
    echo "<p style='color:red;'><strong>PROBLEMA:</strong> Não há nenhum cardápio cadastrado no sistema!</p>";
    echo "<p><strong>SOLUÇÃO:</strong> Um administrador precisa cadastrar cardápios em <a href='../avaliacoes/gerenciar-cardapios'>Gerenciar Cardápios</a></p>";
}

echo "<hr>";
echo "<p style='color: red;'><strong>DELETE este arquivo após o teste!</strong></p>";
?>
