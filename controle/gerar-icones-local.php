<?php
/**
 * Execute este arquivo LOCALMENTE para gerar os ícones PNG
 * Depois faça upload dos arquivos gerados para o servidor
 */

echo "Gerando ícones PNG...\n\n";

// Função para criar ícone
function criarIcone($size, $filename) {
    $image = imagecreatetruecolor($size, $size);

    // Gradiente rosa para roxo
    for ($y = 0; $y < $size; $y++) {
        $r = 245 + (($y / $size) * (240 - 245));
        $g = 87 + (($y / $size) * (147 - 87));
        $b = 108 + (($y / $size) * (251 - 108));
        $color = imagecolorallocate($image, $r, $g, $b);
        imagefilledrectangle($image, 0, $y, $size, $y + 1, $color);
    }

    // Cor branca
    $white = imagecolorallocate($image, 255, 255, 255);

    // Desenhar estrela
    $centerX = $size / 2;
    $centerY = $size / 2;
    $starSize = $size / 3;

    $points = [];
    for ($i = 0; $i < 10; $i++) {
        $angle = deg2rad(($i * 36) - 90);
        $radius = ($i % 2 == 0) ? $starSize : $starSize / 2.5;
        $points[] = $centerX + ($radius * cos($angle));
        $points[] = $centerY + ($radius * sin($angle));
    }

    imagefilledpolygon($image, $points, 10, $white);

    // Texto "AVALIADOR"
    $fontSize = ($size == 192) ? 3 : 5;
    $textY = $size - ($size / 8);
    $textX = ($size / 2) - (($fontSize == 3) ? 35 : 60);
    imagestring($image, $fontSize, $textX, $textY, 'AVALIADOR', $white);

    // Salvar
    $path = __DIR__ . '/public/' . $filename;
    imagepng($image, $path);
    imagedestroy($image);

    echo "✅ Criado: {$filename} (" . filesize($path) . " bytes)\n";
}

// Criar os dois ícones
criarIcone(192, 'icon-192.png');
criarIcone(512, 'icon-512.png');

echo "\n✅ Ícones gerados com sucesso!\n";
echo "\nArquivos criados em:\n";
echo "  - public/icon-192.png\n";
echo "  - public/icon-512.png\n";
echo "\nAgora faça upload desses arquivos para:\n";
echo "  /public_html/controle/public/icon-192.png\n";
echo "  /public_html/controle/public/icon-512.png\n";
