<?php
// includes/tickets.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/** Public URL for viewing a booking */
function ticketUrl(int $bookingId): string {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = '/cinema-booking-system'; // adjust if your base path differs
    return 'http://' . $host . $base . '/pages/booking_success.php?booking_id=' . $bookingId;
}

/** Generate a PNG QR for the booking and save it to $savePath */
function generateTicketQrForBooking(int $bookingId, string $savePath): string {
    $data = ticketUrl($bookingId);

    // Ensure folder exists
    $dir = dirname($savePath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $options = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel'   => QRCode::ECC_H, // high error correction
        'scale'      => 6,             // size multiplier (6*~33px ≈ 200px)
        'margin'     => 2,
    ]);

    $pngData = (new QRCode($options))->render($data);
    file_put_contents($savePath, $pngData);

    return $savePath;
}
