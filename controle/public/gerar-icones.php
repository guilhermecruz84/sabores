<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Ícones - Avaliador Sabores</title>
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
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #f5576c;
            text-align: center;
        }
        .icon-preview {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 30px 0;
        }
        canvas {
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        button {
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-download {
            background: #f5576c;
            color: white;
        }
        .btn-download:hover {
            background: #d94558;
            transform: translateY(-2px);
        }
        .info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .success {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍽️ Gerador de Ícones - Avaliador Sabores</h1>

        <p style="text-align: center; color: #666;">
            Clique nos botões abaixo para baixar os ícones necessários para o PWA
        </p>

        <div class="icon-preview">
            <div>
                <p style="text-align: center; margin-bottom: 10px; font-weight: bold;">192x192 px</p>
                <canvas id="icon192" width="192" height="192"></canvas>
            </div>
            <div>
                <p style="text-align: center; margin-bottom: 10px; font-weight: bold;">512x512 px</p>
                <canvas id="icon512" width="512" height="512" style="max-width: 256px; max-height: 256px;"></canvas>
            </div>
        </div>

        <div class="buttons">
            <button class="btn-download" onclick="downloadIcon(192)">
                ⬇️ Baixar icon-192x192.png
            </button>
            <button class="btn-download" onclick="downloadIcon(512)">
                ⬇️ Baixar icon-512x512.png
            </button>
        </div>

        <div class="info">
            <strong>📋 Instruções:</strong>
            <ol>
                <li>Baixe os 2 ícones clicando nos botões acima</li>
                <li>Crie a pasta <code>public/assets/icons/</code> no servidor</li>
                <li>Faça upload dos ícones para essa pasta via FTP</li>
                <li>Certifique-se que os nomes estão corretos:
                    <ul>
                        <li><code>icon-192x192.png</code></li>
                        <li><code>icon-512x512.png</code></li>
                    </ul>
                </li>
            </ol>
        </div>

        <div class="success" id="successMsg">
            ✅ Ícone baixado com sucesso! Faça upload para o servidor.
        </div>
    </div>

    <script>
        // Função para desenhar o ícone
        function drawIcon(canvas, size) {
            const ctx = canvas.getContext('2d');

            // Fundo gradiente
            const gradient = ctx.createLinearGradient(0, 0, size, size);
            gradient.addColorStop(0, '#f5576c');
            gradient.addColorStop(1, '#f093fb');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, size, size);

            // Desenhar ícone de utensílios (garfo e faca)
            ctx.fillStyle = 'white';
            ctx.font = `bold ${size * 0.5}px Arial`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('🍽️', size / 2, size / 2);

            // Texto "Avaliador"
            ctx.font = `bold ${size * 0.12}px Arial`;
            ctx.fillStyle = 'white';
            ctx.fillText('AVALIADOR', size / 2, size * 0.85);
        }

        // Desenhar os ícones ao carregar a página
        window.onload = function() {
            drawIcon(document.getElementById('icon192'), 192);
            drawIcon(document.getElementById('icon512'), 512);
        };

        // Função para baixar o ícone
        function downloadIcon(size) {
            const canvas = document.getElementById('icon' + size);
            const link = document.createElement('a');
            link.download = `icon-${size}x${size}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();

            // Mostrar mensagem de sucesso
            const successMsg = document.getElementById('successMsg');
            successMsg.style.display = 'block';
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
