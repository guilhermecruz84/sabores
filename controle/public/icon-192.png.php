<?php
// Gerar ícone PNG 192x192 dinamicamente
header('Content-Type: image/png');
header('Cache-Control: public, max-age=31536000');

$size = 192;
$image = imagecreatetruecolor($size, $size);

// Gradiente rosa para roxo
for ($y = 0; $y < $size; $y++) {
    $r = 245 + (($y / $size) * (240 - 245));
    $g = 87 + (($y / $size) * (147 - 87));
    $b = 108 + (($y / $size) * (251 - 108));
    $color = imagecolorallocate($image, $r, $g, $b);
    imagefilledrectangle($image, 0, $y, $size, $y + 1, $color);
}

// Desenhar ícone de estrela (avaliação)
$white = imagecolorallocate($image, 255, 255, 255);

// Estrela central
$centerX = $size / 2;
$centerY = $size / 2;
$starSize = 60;

// Pontos da estrela (5 pontas)
$points = [];
for ($i = 0; $i < 10; $i++) {
    $angle = deg2rad(($i * 36) - 90);
    $radius = ($i % 2 == 0) ? $starSize : $starSize / 2.5;
    $points[] = $centerX + ($radius * cos($angle));
    $points[] = $centerY + ($radius * sin($angle));
}

imagefilledpolygon($image, $points, 10, $white);

// Texto "Avaliador" na parte inferior
$pink = imagecolorallocate($image, 255, 255, 255);
imagestring($image, 3, ($size / 2) - 35, $size - 25, 'AVALIADOR', $pink);

imagepng($image);
imagedestroy($image);
