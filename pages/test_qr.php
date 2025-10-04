<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

echo "<small>FILE: " . __FILE__ . "</small><br>";

try {
    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'   => QRCode::ECC_H,
        'scale'      => 6,
        'margin'     => 2,
    ]);

    $data = 'Hello from chillerlan/php-qrcode';
    $png  = (new QRCode($options))->render($data);

    $path = __DIR__ . '/../storage/qr/test.png';
    @mkdir(dirname($path), 0775, true);
    file_put_contents($path, $png);

    echo "✅ Saved: $path<br>";
    echo "<img src='../storage/qr/test.png' alt='QR'>";
} catch (Throwable $e) {
    echo "❌ " . $e->getMessage();
}
