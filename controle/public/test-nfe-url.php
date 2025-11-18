<!DOCTYPE html>
<html>
<head>
    <title>Test NFe URLs</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        pre { background: #f0f0f0; padding: 10px; border-radius: 3px; overflow-x: auto; }
        a { color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>🔍 Teste de URLs - NFe</h1>

    <div class="box">
        <h2>Informações do Servidor</h2>
        <pre><?php
        echo "PHP Version: " . phpversion() . "\n";
        echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
        echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
        echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
        echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
        echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
        ?></pre>
    </div>

    <div class="box">
        <h2>Verificação do Arquivo Nfe.php</h2>
        <?php
        $nfeFile = __DIR__ . '/../app/Controllers/Operacional/Nfe.php';

        if (file_exists($nfeFile)) {
            echo '<p class="success">✅ Arquivo encontrado: ' . $nfeFile . '</p>';

            $content = file_get_contents($nfeFile);

            // Verificar a constante BASE
            if (preg_match("/private const BASE = '([^']+)'/", $content, $matches)) {
                $baseConst = $matches[1];
                echo '<p><strong>Constante BASE atual:</strong> <code>' . htmlspecialchars($baseConst) . '</code></p>';

                if ($baseConst === 'operacional/nfe') {
                    echo '<p class="success">✅ BASE está correto: <code>operacional/nfe</code></p>';
                } else {
                    echo '<p class="error">❌ BASE está errado! Deveria ser <code>operacional/nfe</code></p>';
                    echo '<p class="info">👉 Você precisa fazer upload do arquivo Nfe.php atualizado!</p>';
                }
            } else {
                echo '<p class="error">❌ Não conseguiu encontrar a constante BASE</p>';
            }

            // Verificar se método review existe
            if (strpos($content, 'public function review') !== false) {
                echo '<p class="success">✅ Método review() existe</p>';
            } else {
                echo '<p class="error">❌ Método review() não encontrado</p>';
            }

        } else {
            echo '<p class="error">❌ Arquivo não encontrado: ' . $nfeFile . '</p>';
        }
        ?>
    </div>

    <div class="box">
        <h2>Teste de URLs Geradas</h2>
        <?php
        // Simular baseURL
        $baseURL = 'https://www.saboresemmovimento.com.br/controle/';
        $testBase = 'operacional/nfe';
        $importId = 5;

        $testURL = $baseURL . $testBase . '/review/' . $importId;

        echo '<p><strong>URL que deveria ser gerada:</strong></p>';
        echo '<pre>' . htmlspecialchars($testURL) . '</pre>';

        echo '<p><strong>Testar esta URL:</strong></p>';
        echo '<p><a href="' . $testURL . '" target="_blank">' . htmlspecialchars($testURL) . '</a></p>';
        ?>
    </div>

    <div class="box">
        <h2>Verificação de Rotas</h2>
        <?php
        $routesFile = __DIR__ . '/../app/Config/Routes.php';

        if (file_exists($routesFile)) {
            echo '<p class="success">✅ Arquivo Routes.php encontrado</p>';

            $routes = file_get_contents($routesFile);

            if (strpos($routes, "nfe/review/(:num)") !== false) {
                echo '<p class="success">✅ Rota nfe/review/(:num) está definida</p>';
            } else {
                echo '<p class="error">❌ Rota nfe/review/(:num) NÃO encontrada</p>';
            }

            if (preg_match("/namespace.*App\\\\Controllers\\\\Operacional/", $routes)) {
                echo '<p class="success">✅ Namespace Operacional está configurado</p>';
            } else {
                echo '<p class="error">❌ Namespace Operacional não encontrado</p>';
            }

        } else {
            echo '<p class="error">❌ Arquivo Routes.php não encontrado</p>';
        }
        ?>
    </div>

    <div class="box">
        <h2>Links de Teste</h2>
        <ul>
            <li><a href="/controle/operacional/nfe">Lista de NFe</a></li>
            <li><a href="/controle/operacional/nfe/review/1">Review ID 1</a></li>
            <li><a href="/controle/operacional/nfe/review/5">Review ID 5</a></li>
        </ul>
    </div>

    <div class="box">
        <h2>Ações Necessárias</h2>
        <ol>
            <li>Verifique se a constante BASE está como <code>operacional/nfe</code></li>
            <li>Se não estiver, faça upload do arquivo atualizado:
                <pre>/controle/app/Controllers/Operacional/Nfe.php</pre>
            </li>
            <li>Limpe o cache do servidor (se houver)</li>
            <li>Teste novamente a importação de NFe</li>
        </ol>
    </div>

    <p><small>Gerado em: <?= date('Y-m-d H:i:s') ?></small></p>
</body>
</html>
