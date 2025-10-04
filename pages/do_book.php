<?php
// /pages/do_book.php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
if (!isset($_SESSION['user_id'])) { header('Location: /cinema-booking-system/pages/login.php'); exit; }

$showtime_id = (int)($_POST['showtime_id'] ?? 0);
$seat_ids    = array_map('intval', $_POST['seat_ids'] ?? []);
$price_id    = (int)($_POST['price_id'] ?? 0);

if ($showtime_id <= 0 || $price_id <= 0 || empty($seat_ids)) {
  http_response_code(400); die('Missing inputs.');
}

/* price snapshot */
$priceStmt = $pdo->prepare("SELECT id, amount FROM pricing WHERE id = :pid AND is_active = 1");
$priceStmt->execute([':pid'=>$price_id]);
$price = $priceStmt->fetch(PDO::FETCH_ASSOC);
if (!$price) { http_response_code(400); die('Invalid price id.'); }
$amount = (float)$price['amount'];

$pdo->beginTransaction();
try {
  $qty   = count($seat_ids);
  $total = $qty * $amount;

  // 1) create booking (PENDING — confirmation happens after payment)
  $b = $pdo->prepare("
    INSERT INTO booking (user_id, showtime_id, qty, total_amount, booking_status, created_at)
    VALUES (:uid, :sid, :qty, :total, 'pending', NOW())
  ");
  $b->execute([
    ':uid' => (int)$_SESSION['user_id'],
    ':sid' => $showtime_id,
    ':qty' => $qty,
    ':total'=> $total,
  ]);
  $booking_id = (int)$pdo->lastInsertId();

  // 2) add items (one per seat)
  $bi = $pdo->prepare("
    INSERT INTO booking_items (booking_id, showtime_id, seat_id, price_id, line_amount)
    VALUES (:bid, :sid, :seat, :pid, :amt)
  ");
  foreach ($seat_ids as $sid) {
    // UNIQUE(showtime_id, seat_id) will prevent double-selling
    $bi->execute([
      ':bid'=>$booking_id, ':sid'=>$showtime_id,
      ':seat'=>$sid, ':pid'=>$price_id, ':amt'=>$amount
    ]);
  }

  $pdo->commit();

  // → go to payment
  header("Location: /cinema-booking-system/pages/payment.php?booking_id={$booking_id}");
  exit;

} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(409);
  echo "Failed to book seats (maybe just taken): " . htmlspecialchars($e->getMessage());
}
