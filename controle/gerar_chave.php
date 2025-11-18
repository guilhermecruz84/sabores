<?php
/**
 * Gerador de Chave de Encriptação para CodeIgniter 4
 *
 * INSTRUÇÕES:
 * 1. Faça upload deste arquivo para a pasta raiz do projeto no servidor
 * 2. Acesse via navegador: https://saboresemmovimento.com.br/controle/gerar_chave.php
 * 3. Copie a chave gerada
 * 4. Cole no arquivo .env na linha: encryption.key =
 * 5. DELETE este arquivo do servidor após usar!
 */

// Gera uma chave aleatória de 32 bytes (256 bits)
$key = random_bytes(32);

// Converte para hexadecimal
$hexKey = bin2hex($key);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Chave de Encriptação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .key-box {
            background: #f9f9f9;
            border: 2px solid #4CAF50;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            color: #155724;
        }
        .button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background: #45a049;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        ol {
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Chave de Encriptação Gerada</h1>

        <div class="success">
            <strong>✅ Chave gerada com sucesso!</strong>
        </div>

        <h2>Sua chave de encriptação:</h2>
        <div class="key-box" id="keyBox">
            hex2bin:<?= $hexKey ?>
        </div>

        <button class="button" onclick="copyKey()">📋 Copiar Chave</button>

        <h2>Instruções:</h2>
        <ol>
            <li>Clique no botão acima para <strong>copiar a chave</strong></li>
            <li>No cPanel, vá para o <strong>Gerenciador de Arquivos</strong></li>
            <li>Navegue até: <code>/public_html/controle/</code></li>
            <li>Edite o arquivo <code>.env</code></li>
            <li>Encontre a linha: <code>encryption.key = </code></li>
            <li>Cole a chave copiada após o <code>=</code></li>
            <li>Salve o arquivo</li>
            <li><strong>IMPORTANTE:</strong> Delete este arquivo <code>gerar_chave.php</code> do servidor!</li>
        </ol>

        <div class="warning">
            <strong>⚠️ ATENÇÃO:</strong><br>
            • Mantenha esta chave em segredo!<br>
            • NÃO compartilhe esta chave publicamente<br>
            • DELETE este arquivo após copiar a chave<br>
            • Se você perder esta chave, não conseguirá descriptografar dados já encriptados
        </div>

        <h2>Exemplo de como deve ficar no .env:</h2>
        <div class="key-box">
encryption.key = hex2bin:<?= $hexKey ?>
        </div>
    </div>

    <script>
        function copyKey() {
            const keyText = document.getElementById('keyBox').textContent.trim();

            // Cria um elemento temporário para copiar
            const tempInput = document.createElement('textarea');
            tempInput.value = keyText;
            document.body.appendChild(tempInput);
            tempInput.select();

            try {
                document.execCommand('copy');
                alert('✅ Chave copiada para a área de transferência!');
            } catch (err) {
                alert('❌ Erro ao copiar. Por favor, copie manualmente.');
            }

            document.body.removeChild(tempInput);
        }
    </script>
</body>
</html>
