<?php
// /pages/my_bookings.php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
  echo "<p>Please <a href='/cinema-booking-system/pages/login.php'>log in</a> to view your bookings.</p>";
  require_once __DIR__ . '/../includes/footer.php';
  exit;
}

$list = $pdo->prepare("
  SELECT
    b.id AS booking_id,          -- 👈 explicit alias
    b.qty,
    b.total_amount,
    b.booking_status,
    b.created_at,
    s.starts_at,
    m.title,
    h.name AS hall_name
  FROM booking b
  JOIN showtimes s ON s.id = b.showtime_id
  JOIN movies m    ON m.id = s.movies_id
  JOIN halls  h    ON h.id = s.hall_id
  WHERE b.user_id = :uid
  ORDER BY b.created_at DESC
");
$list->execute([':uid' => (int)$_SESSION['user_id']]);

// 👇 force associative keys
$rows = $list->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Bookings</h2>
<?php if (!$rows): ?>
  <p>No bookings yet.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($rows as $r): ?>
      <div class="card">
        <h3><?= htmlspecialchars($r['title']) ?></h3>
        <p><strong>Showtime:</strong> <?= htmlspecialchars(date('D, d M Y H:i', strtotime($r['starts_at']))) ?> (<?= htmlspecialchars($r['hall_name']) ?>)</p>
        <p><strong>Qty:</strong> <?= (int)$r['qty'] ?> &nbsp; <strong>Total:</strong> $<?= number_format((float)$r['total_amount'],2) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($r['booking_status']) ?></p>

        <!-- Use the explicit alias -->
        <a class="button"
           href="/cinema-booking-system/pages/booking_success.php?booking_id=<?= (int)$r['booking_id'] ?>">
          View details
        </a>

        <!-- TEMP DEBUG: see what you're linking to -->
        <!-- <div style="font-size:12px;color:#888"><code>/cinema-booking-system/pages/booking_success.php?booking_id=<?= (int)$r['booking_id'] ?></code></div> -->
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
