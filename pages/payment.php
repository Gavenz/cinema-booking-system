<?php
// /pages/payment.php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['user_id'])) { header('Location: /cinema-booking-system/pages/login.php'); exit; }
$user_id = (int)$_SESSION['user_id'];

$booking_id = (int)($_GET['booking_id'] ?? 0);
if (!$booking_id) { echo "Invalid booking."; exit; }

// Load booking + showtime + movie (note: starts_at column)
$bstmt = $pdo->prepare("
  SELECT b.*, m.title, st.starts_at
  FROM booking b
  JOIN showtimes st ON st.id=b.showtime_id
  JOIN movies m ON m.id=st.movies_id
  WHERE b.id=? AND b.user_id=?
");
$bstmt->execute([$booking_id, $user_id]);
$b = $bstmt->fetch(PDO::FETCH_ASSOC);
if (!$b) { echo "Booking not found."; exit; }

if ($b['booking_status'] !== 'pending') {
  echo "This booking is not pending. Status: ".htmlspecialchars($b['booking_status']);
  exit;
}

// Load seats
$sstmt = $pdo->prepare("
  SELECT s.row_label, s.col_num, bi.line_amount
  FROM booking_items bi
  JOIN seats s ON s.id=bi.seat_id
  WHERE bi.booking_id=?
  ORDER BY s.row_label, s.col_num
");
$sstmt->execute([$booking_id]);
$seats = $sstmt->fetchAll(PDO::FETCH_ASSOC);

$amount = number_format((float)$b['total_amount'], 2);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment — Booking #<?= $booking_id ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Arial;margin:0;background:#0b1220;color:#e6edf3}
    .wrap{max-width:860px;margin:24px auto;padding:0 16px}
    .card{background:#0f1629;border:1px solid #1f2b44;border-radius:12px;padding:16px}
    .methods{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}
    .method{padding:10px;border:1px solid #24324e;border-radius:10px;cursor:pointer}
    button{background:#2563eb;color:#fff;border:0;border-radius:10px;padding:10px 14px;cursor:pointer}
    a{color:#8ab4f8}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>Complete Payment</h2>
      <p><strong>Movie:</strong> <?=htmlspecialchars($b['title'])?></p>
      <p><strong>Showtime:</strong> <?=htmlspecialchars($b['starts_at'])?></p>
      <p><strong>Seats:</strong>
        <?php foreach($seats as $s){ echo htmlspecialchars($s['row_label'].$s['col_num']).' '; } ?>
      </p>
      <p><strong>Amount:</strong> $<?=$amount?></p>

      <form method="post" action="/cinema-booking-system/pages/process_payment.php">
        <input type="hidden" name="booking_id" value="<?=$booking_id?>">
        <input type="hidden" name="amount" value="<?=$amount?>">
        <div class="methods">
          <label class="method"><input type="radio" name="method" value="apple_pay" required> Apple&nbsp;Pay (fake)</label>
          <label class="method"><input type="radio" name="method" value="google_pay"> Google&nbsp;Pay (fake)</label>
          <label class="method"><input type="radio" name="method" value="card_visa"> Visa **** 4242</label>
          <label class="method"><input type="radio" name="method" value="card_master"> Mastercard **** 5555</label>
        </div>
        <div style="margin-top:12px">
          <button type="submit">Pay now (simulate)</button>
          <a style="margin-left:12px" href="/cinema-booking-system/pages/cancel_booking.php?booking_id=<?=$booking_id?>">Cancel booking</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
