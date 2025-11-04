<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['user'])) {
  flash_warn('Please log in to view your bookings');
  header('Location: ' . url('pages/login.php?next='.urlencode(url('pages/bookings.php'))));
  exit;
}

$uid = (int)($_SESSION['user']['id'] ?? 0);

// pull recent bookings for the user
$sql = "
  SELECT b.id, b.qty, b.total_amount, b.booking_status, b.created_at,
         s.starts_at, m.title, h.name AS hall_name, GROUP_CONCAT(CONCAT(se.row_label, se.col_num)
                      ORDER BY se.row_label, se.col_num SEPARATOR ', ') AS seat_labels
  FROM booking b
  JOIN showtimes s ON s.id = b.showtime_id
  JOIN movies m    ON m.id = s.movies_id
  JOIN halls h ON h.id = s.hall_id
  JOIN booking_items bi on bi.booking_id = b.id
  JOIN seats se ON se.id = bi.seat_id
  WHERE b.user_id = :uid
  GROUP BY b.id
  ORDER BY b.created_at DESC
";
$st = $pdo->prepare($sql);
$st->execute([':uid' => $uid]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

// which nav to highlight
$activeNav = 'null'; // or set to null if you don't want any tab highlighted
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>My Bookings — Big Premiere Point</title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Global site styles (header, footer, colors, buttons, etc.) -->
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>?v=2">
  <!-- Page-scoped cosmetics only -->
  <style>
    .bookings-wrap { max-width: 1100px; margin: 18px auto 40px; padding: 0 20px; }
    .bookings-list { display: grid; gap: 12px; }
    .booking-card{
      background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.12);
      border-radius:14px;
      padding:14px 16px;
    }
    .booking-title{ font-weight:800; font-size:1.05rem; }
    .booking-meta{ color:#9ea0b5; font-size:.95rem; margin-top:4px; display:flex; gap:10px; flex-wrap:wrap; }
    .pill{
      display:inline-flex; align-items:center; gap:6px; padding:3px 10px; border-radius:999px;
      background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.18); font-weight:800; font-size:.8rem;
    }
        /* base pill already exists; add color variants */
    .pill--time    { background: rgba(59,130,246,.15); border-color: rgba(59,130,246,.35); color:#cfe3ff; }  /* blue */
    .pill--qty     { background: rgba(99,102,241,.15); border-color: rgba(99,102,241,.35); color:#dcdcff; }  /* indigo */
    .pill--total   { background: rgba(229, 9, 20,.18); border-color: rgba(229, 9, 20,.45); color:#ffd6da; }   /* accent red */
    .pill--booked  { background: rgba(148,163,184,.15); border-color: rgba(148,163,184,.35); color:#e6eaf0; } /* slate/grey */

    .pill--success { background: rgba(34,197,94,.16);  border-color: rgba(34,197,94,.38);  color:#d7f8e3; }   /* green */
    .pill--warning { background: rgba(245,158,11,.16); border-color: rgba(245,158,11,.38); color:#ffe9c7; }   /* amber */
    .pill--danger  { background: rgba(239,68,68,.16);  border-color: rgba(239,68,68,.42);  color:#ffd7d7; }   /* red */
    .pill--muted   { background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.18); color:#eaeaf2; } /* neutral */

  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <main class="bookings-wrap">
    <h2 style="margin:10px 0 16px;">My Bookings</h2>

    <div class="bookings-list">
      <?php if (!$rows): ?>
        <div class="booking-card" style="color:#9ea0b5;">No bookings yet.</div>
      <?php else: foreach ($rows as $r): ?>
        <article class="booking-card">
          <div class="booking-title"><?= htmlspecialchars($r['title']) ?></div>

      <!-- adding in all the color rules-->
      <?php
        $status = $r['booking_status'] ?? 'pending';
        $statusClass = match ($status) {
          'confirmed' => 'pill--success',
          'pending'   => 'pill--warning',
          'expired'   => 'pill--danger',
          'cancelled' => 'pill--muted',
          default     => 'pill--muted',
        };
        ?>
          
          <div class="booking-meta">
            <span class="pill pill--time">
              Showtime: <?= local_time_label($r['starts_at'], 'D, j M Y • g:i A') ?>
            </span>
            <span class="pill pill--qty">Qty: <?= (int)$r['qty'] ?></span>
            <span class="pill pill--total">Total: $<?= number_format((float)$r['total_amount'], 2) ?></span>
            <span class="pill <?= $statusClass ?>">Status: <?= htmlspecialchars($status) ?></span>
            <span class="pill pill--booked">
              Booked: <?= local_time_label($r['created_at'], 'D, j M Y • g:i A') ?>
            </span>
            <span class="pill">Seats: <?= htmlspecialchars($r['seat_labels'] ?: '—') ?></span>
            <span class="pill">Hall: <?= htmlspecialchars($r['hall_name']) ?></span>
          </div>
        </article>
      <?php endforeach; endif; ?>
    </div>
  </main>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
