<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "
SELECT s.id AS showtime_id, s.starts_at, h.name AS hall_name,
       m.title, m.rating, m.runtime_min
FROM showtimes s
JOIN movies m ON m.id = s.movies_id
JOIN halls  h ON h.id = s.hall_id
WHERE s.starts_at >= NOW()
ORDER BY s.starts_at ASC
";
$rows = $pdo->query($sql)->fetchAll();
?>
<h2>Upcoming Showtimes</h2>
<div class="grid">
  <?php foreach ($rows as $r): ?>
    <div class="card">
      <h3><?= htmlspecialchars($r['title']) ?></h3>
      <p><strong>Starts:</strong> <?= htmlspecialchars(date('D, d M Y H:i', strtotime($r['starts_at']))) ?></p>
      <p><strong>Hall:</strong> <?= htmlspecialchars($r['hall_name']) ?></p>
      <p><strong>Rating:</strong> <?= htmlspecialchars($r['rating'] ?? '—') ?> &middot; <?= (int)$r['runtime_min'] ?> min</p>
      <a class="button" href="/cinema-booking-system/pages/book.php?showtime_id=<?= (int)$r['showtime_id'] ?>">Select seats</a>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
