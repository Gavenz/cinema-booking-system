<?php
// /pages/booking_success.php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/../includes/init.php';
if (!isset($_SESSION['user_id'])) { header('Location: /cinema-booking-system/pages/login.php'); exit; }
$user_id = (int)$_SESSION['user_id'];

// accept booking_id via GET or POST (and legacy 'id')
$booking_id = 0;
if (isset($_GET['booking_id'])) $booking_id = (int)$_GET['booking_id'];
if (!$booking_id && isset($_GET['id'])) $booking_id = (int)$_GET['id'];
if (!$booking_id && isset($_POST['booking_id'])) $booking_id = (int)$_POST['booking_id'];

// TEMP: if still missing, show what we got (so it doesn't "look like nothing happened")
if (!$booking_id) {
  echo "<pre style='padding:16px'>booking_success DEBUG\n";
  echo "URI: ", htmlspecialchars($_SERVER['REQUEST_URI'] ?? ''), "\n";
  echo "GET: ", htmlspecialchars(json_encode($_GET)), "\n";
  echo "POST: ", htmlspecialchars(json_encode($_POST)), "\n";
  echo "File: ", __FILE__, "\n</pre>";
  exit;
}


$bstmt = $pdo->prepare("
  SELECT b.*, m.title, st.starts_at, p.transaction_ref
  FROM booking b
  JOIN showtimes st ON st.id=b.showtime_id
  JOIN movies m ON m.id=st.movies_id
  LEFT JOIN payments p ON p.id=b.payment_id
  WHERE b.id=? AND b.user_id=?
");
$bstmt->execute([$booking_id, $user_id]);
$b = $bstmt->fetch(PDO::FETCH_ASSOC);
if (!$b) { echo "Not found."; exit; }
if ($b['booking_status'] !== 'confirmed') { echo "Not confirmed yet."; exit; }

$sstmt = $pdo->prepare("
  SELECT s.row_label, s.col_num
  FROM booking_items bi
  JOIN seats s ON s.id=bi.seat_id
  WHERE bi.booking_id=?
  ORDER BY s.row_label, s.col_num
");
$sstmt->execute([$booking_id]);
$seats = $sstmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Booking Confirmed</title>
<style>
body{font-family:system-ui,Segoe UI,Arial;background:#0b1220;color:#e6edf3;margin:0}
.wrap{max-width:720px;margin:24px auto;padding:0 16px}
.card{background:#0f1629;border:1px solid #1f2b44;border-radius:12px;padding:16px}
</style></head>
<body>
  <div class="wrap">
    <div class="card">
      <h2>✅ Booking Confirmed</h2>
      <p><strong>Booking #</strong> <?= (int)$booking_id ?></p>
      <p><strong>Movie:</strong> <?= htmlspecialchars($b['title']) ?></p>
      <p><strong>Showtime:</strong> <?= htmlspecialchars($b['starts_at']) ?></p>
      <p><strong>Seats:</strong>
        <?php foreach($seats as $x){ echo htmlspecialchars($x['row_label'].$x['col_num']).' '; } ?>
      </p>
      <p><strong>Amount Paid:</strong> $<?= number_format((float)$b['total_amount'],2) ?></p>
      <p><strong>Transaction Ref:</strong> <?= htmlspecialchars($b['transaction_ref'] ?? '—') ?></p>
      <p>You’ll also get an email confirmation (optional step).</p>
      <p><a href="/cinema-booking-system/pages/my_bookings.php">Back to My Bookings</a></p>
    </div>
  </div>
</body>
</html>
