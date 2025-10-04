<?php
// /pages/process_payment.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/tickets.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /cinema-booking-system/pages/login.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

$booking_id = (int)($_POST['booking_id'] ?? 0);
$method     = trim($_POST['method'] ?? '');
$amount     = (float)($_POST['amount'] ?? 0);

if (!$booking_id || !$method) {
    echo "Invalid request.";
    exit;
}

// --- Load booking ---
$bstmt = $pdo->prepare("SELECT * FROM booking WHERE id=? AND user_id=?");
$bstmt->execute([$booking_id, $user_id]);
$b = $bstmt->fetch(PDO::FETCH_ASSOC);
if (!$b) { echo "Booking not found."; exit; }
if ($b['booking_status'] !== 'pending') { echo "Not pending."; exit; }

// --- Simulate gateway success ---
$pdo->beginTransaction();
try {
    $txref = 'TESTTX-' . bin2hex(random_bytes(6));
    $pstmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, method, status, transaction_ref)
                            VALUES (?, ?, ?, 'succeeded', ?)");
    $pstmt->execute([$booking_id, $amount, $method, $txref]);
    $payment_id = (int)$pdo->lastInsertId();

    $upd = $pdo->prepare("UPDATE booking
                          SET booking_status='confirmed', paid_at=NOW(), payment_id=?
                          WHERE id=? AND booking_status='pending'");
    $upd->execute([$payment_id, $booking_id]);

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    echo "Payment error: " . htmlspecialchars($e->getMessage());
    exit;
}

/* -------------------------------------------------------------------
   NEW SECTION: Build QR + Email confirmation
------------------------------------------------------------------- */
try {
    // Fetch booking meta (movie, showtime, user email)
    $metaStmt = $pdo->prepare("
        SELECT b.id AS booking_id, b.total_amount, b.user_id,
               m.title, st.starts_at, u.email
        FROM booking b
        JOIN showtimes st ON st.id = b.showtime_id
        JOIN movies m     ON m.id = st.movies_id
        JOIN users u      ON u.id = b.user_id
        WHERE b.id=? AND b.user_id=?
    ");
    $metaStmt->execute([$booking_id, $user_id]);
    $meta = $metaStmt->fetch(PDO::FETCH_ASSOC);

    // Debug log what we got
    error_log("📧 Booking meta: " . json_encode($meta));

    // Get seats
    $seatsStmt = $pdo->prepare("
        SELECT s.row_label, s.col_num
        FROM booking_items bi
        JOIN seats s ON s.id = bi.seat_id
        WHERE bi.booking_id=?
        ORDER BY s.row_label, s.col_num
    ");
    $seatsStmt->execute([$booking_id]);
    $seatLabels = array_map(fn($r) => $r['row_label'].$r['col_num'], $seatsStmt->fetchAll(PDO::FETCH_ASSOC));

    // Generate QR and save under storage/qr/
    $qrPath = __DIR__ . '/../storage/qr/booking-' . (int)$booking_id . '.png';
    generateTicketQrForBooking((int)$booking_id, $qrPath);

    // Build email body
    $ticketLink = ticketUrl((int)$booking_id);
    $html = '<h2>Booking Confirmed #' . (int)$booking_id . '</h2>'
          . '<p><strong>Movie:</strong> ' . htmlspecialchars($meta['title'] ?? '') . '</p>'
          . '<p><strong>Showtime:</strong> ' . htmlspecialchars($meta['starts_at'] ?? '') . '</p>'
          . '<p><strong>Seats:</strong> ' . htmlspecialchars(implode(', ', $seatLabels)) . '</p>'
          . '<p><strong>Amount Paid:</strong> $' . number_format((float)($meta['total_amount'] ?? 0), 2) . '</p>'
          . '<p>Show the attached QR at entry, or open this ticket link:<br>'
          . '<a href="' . htmlspecialchars($ticketLink) . '">' . htmlspecialchars($ticketLink) . '</a></p>';

    // Use real user email if present, else fallback for demo
    $toEmail = !empty($meta['email']) ? $meta['email'] : 'test@example.test';
    error_log("📧 About to send email");
    error_log("📧 To: $toEmail");
    error_log("📧 Subject: Your Booking #" . (int)$booking_id);
    error_log("📧 Attachment path: $qrPath exists=" . (file_exists($qrPath) ? "yes" : "no"));

    $ok = sendBookingEmail($toEmail, 'Your Booking #' . (int)$booking_id, $html, $qrPath);
    error_log("📧 sendBookingEmail returned: " . ($ok ? "true" : "false") . " for $toEmail");

} catch (Throwable $e) {
    error_log('Email/QR error for booking ' . (int)$booking_id . ': ' . $e->getMessage());
}
/* ------------------------------------------------------------------- */

// Redirect to booking success page
header("Location: /cinema-booking-system/pages/booking_success.php?booking_id=".$booking_id);
exit;
