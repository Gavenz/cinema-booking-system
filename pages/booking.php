<?php
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['user'])) {
  flash_set('auth','Please log in to select seats','warning');
  header('Location: ' . url('pages/login.php?next='.urlencode($_SERVER['REQUEST_URI'] ?? url('pages/showtimes.php'))));
  exit;
}

$activeNav = 'showtimes';
$uid = (int)$_SESSION['user']['id'];

// --- Read showtime_id ---
$showtimeId = isset($_GET['showtime_id']) ? (int)$_GET['showtime_id'] : 0;
if ($showtimeId <= 0) {
  flash_set('error','Missing or invalid showtime','error');
  header('Location: ' . url('pages/showtimes.php'));
  exit;
}

// --- Load showtime + movie + hall ---
$st = $pdo->prepare("
  SELECT s.id, s.starts_at, s.hall_id,
         m.id AS movie_id, m.title, m.runtime_min, m.rating, m.poster_url,
         h.name AS hall_name, h.rows_count, h.cols_count
  FROM showtimes s
  JOIN movies m ON m.id = s.movies_id
  JOIN halls  h ON h.id = s.hall_id
  WHERE s.id = :sid
  LIMIT 1
");
$st->execute([':sid' => $showtimeId]);
$show = $st->fetch(PDO::FETCH_ASSOC);
if (!$show) {
  flash_set('error','Showtime not found','error');
  header('Location: ' . url('pages/showtimes.php'));
  exit;
}

// --- Load hall seats ---
$st = $pdo->prepare("
  SELECT id, row_label, col_num
  FROM seats
  WHERE halls_id = :hid
  ORDER BY row_label ASC, col_num ASC
");
$st->execute([':hid' => (int)$show['hall_id']]);
$seats = $st->fetchAll(PDO::FETCH_ASSOC);

// --- Seats already taken/held for this showtime ---
// Replace your “taken seats” query with this:
$stTaken = $pdo->prepare("
  SELECT bi.seat_id
  FROM booking_items bi
  JOIN booking b ON b.id = bi.booking_id
  WHERE bi.showtime_id = :sid
    AND (
         b.booking_status = 'confirmed'
      OR (b.booking_status = 'pending' AND (b.expires_at IS NULL OR b.expires_at > NOW()))
    )
");
$stTaken->execute([':sid' => $showtimeId]);
$unavailable = array_fill_keys($stTaken->fetchAll(PDO::FETCH_COLUMN, 0), true);


// --- Handle POST (create booking) ---
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $chosen = array_map('intval', $_POST['seats'] ?? []);
  $chosen = array_values(array_unique(array_filter($chosen)));

  if (count($chosen) === 0) {
    $errors[] = 'Please select at least one seat.';
  } else {
    // Grab an active price (Adult preferred; fallback to any active)
    $price = $pdo->query("SELECT id, amount FROM pricing WHERE is_active=1 AND ticket_type='Adult' LIMIT 1")->fetch();
    if (!$price) {
      $price = $pdo->query("SELECT id, amount FROM pricing WHERE is_active=1 LIMIT 1")->fetch();
    }
    if (!$price) {
      $errors[] = 'No pricing configured.';
    } else {
      $priceId = (int)$price['id'];
      $amount  = (float)$price['amount'];
      $qty     = count($chosen);
      $total   = $qty * $amount;

      try {
        $pdo->beginTransaction();

        // Double-check none of the seats just got taken (race safety):
        $in = implode(',', array_fill(0, $qty, '?'));
        $chk = $pdo->prepare("
          SELECT bi.seat_id
          FROM booking_items bi
          JOIN booking b ON b.id = bi.booking_id
          WHERE bi.showtime_id = ?
            AND bi.seat_id IN ($in)
            AND (
              b.booking_status = 'confirmed'
              OR (b.booking_status='pending' AND (b.expires_at IS NULL OR b.expires_at > NOW()))
            )
          LIMIT 1
        ");
        $chk->execute(array_merge([$showtimeId], $chosen));
        if ($chk->fetch()) {
          throw new Exception('One or more selected seats were just taken. Please choose different seats.');
        }

        // Create booking (confirm directly for demo; you can switch to 'pending' + checkout later)
        $insB = $pdo->prepare("
          INSERT INTO booking (user_id, showtime_id, qty, total_amount, booking_status, paid_at)
          VALUES (:uid, :sid, :qty, :total, 'confirmed', NOW())
        ");
        $insB->execute([
          ':uid'   => $uid,
          ':sid'   => $showtimeId,
          ':qty'   => $qty,
          ':total' => $total,
        ]);
        $bookingId = (int)$pdo->lastInsertId();

        // Insert items
        $insI = $pdo->prepare("
          INSERT INTO booking_items (booking_id, showtime_id, seat_id, price_id, line_amount)
          VALUES (?, ?, ?, ?, ?)
        ");
        foreach ($chosen as $seatId) {
          $insI->execute([$bookingId, $showtimeId, $seatId, $priceId, $amount]);
        }

        $pdo->commit();
        flash_set('success', 'Booking confirmed! 🎉', 'success');
        header('Location: ' . url('pages/bookings.php'));
        exit;

      } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $errors[] = $e->getMessage();
      }
    }
  }
}

// --- Prepare grid meta ---
$cols = (int)($show['cols_count'] ?? 10);
$rowsByLabel = [];
foreach ($seats as $s) {
  $rowsByLabel[$s['row_label']][] = $s;
}
$whenLabel = local_time_label($show['starts_at'], 'D, j M Y • g:i A' ?? 'Asia/Singapore');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Select seats — <?= htmlspecialchars($show['title']) ?></title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>?v=2">

  <style>
    .wrap { max-width:1100px; margin:18px auto 40px; padding:0 20px; }
    .head { display:flex; gap:16px; align-items:center; flex-wrap:wrap; margin-bottom:10px; }
    .title { font-weight:800; font-size:1.15rem; }
    .muted { color: var(--muted); }
    .pill { display:inline-block; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.16); font-weight:700; }
    .pill.good { background:#0f2a12; border-color:#1f6b2d; }
    .pill.bad  { background:#2a0f12; border-color:#6b1f2d; }
    .pill--rating { background:#2a2a07; color:var(--accent-2); border-color:rgba(245,197,24,.25); }
    .pill--hall    { background: rgba(59,130,246,.15); border-color: rgba(59,130,246,.35); color:#cfe3ff; }  /* blue */
    .pill--when     { background: rgba(99,102,241,.15); border-color: rgba(99,102,241,.35); color:#dcdcff; }  /* indigo */


    .screen {
      text-align:center; margin: 10px 0 18px;
      padding: 10px; border-radius: 10px;
      background: linear-gradient(to bottom, rgba(255,255,255,.25), rgba(255,255,255,.06));
      border: 1px solid rgba(255,255,255,.18);
      font-weight:800; letter-spacing:.6px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(<?= max(1,$cols) ?>, minmax(32px, 1fr));
      gap: 8px;
      justify-items: center;
      padding: 14px;
      border-radius: 14px;
      background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border: 1px solid rgba(255,255,255,.12);
    }
    .row-label { grid-column: 1 / -1; text-align:left; margin-top:12px; color:#b9b9c9; font-weight:800; }
    .seat {
      display:inline-block; width:38px; height:38px; line-height:36px; text-align:center;
      border-radius:8px; font-weight:800; cursor:pointer; user-select:none;
      border:1px solid rgba(255,255,255,.18); background:rgba(255,255,255,.06);
    }
    .seat input { display:none; }
    .seat:hover { filter:brightness(1.08); }
    .seat.selected { outline: 2px solid #fff; }
    .seat.unavailable { background:#2a0f12; border-color:#6b1f2d; color:#d9a0aa; cursor:not-allowed; opacity:.7; }

    .legend { display:flex; gap:10px; align-items:center; margin: 10px 0 16px; }
    .legend .box { width:16px; height:16px; border-radius:4px; border:1px solid rgba(255,255,255,.18); background:rgba(255,255,255,.06); }
    .legend .box.unavail { background:#2a0f12; border-color:#6b1f2d; }

    .actions { display:flex; gap:10px; align-items:center; margin-top:12px; }
    .spacer { flex:1; }
  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <main class="wrap">
    <div class="head">
      <div class="title"><?= htmlspecialchars($show['title']) ?></div>
      <span class="pill pill--hall"><?= htmlspecialchars($show['hall_name']) ?></span>
      <span class="pill pill--when"><?= $whenLabel ?></span>
      <?php if (!empty($show['rating'])): ?>
        <span class="pill pill--rating"><?= htmlspecialchars($show['rating']) ?></span>
      <?php endif; ?>
      <?php if (!empty($show['runtime_min'])): ?>
        <span class="pill"><?= (int)$show['runtime_min'] ?>m</span>
      <?php endif; ?>
    </div>

    <?php if ($errors): ?>
      <div class="flash error">
        <?php foreach ($errors as $e): ?>
          <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="screen">SCREEN</div>

<form method="post" action="<?= url('pages/checkout.php') ?>">
  <!-- REQUIRED: tell checkout which showtime these seats belong to -->
  <input type="hidden" name="showtime_id" value="<?= (int)$showtimeId ?>">

  <!-- OPTIONAL: default price tier (Adult=1) -->
  <input type="hidden" name="price_id" value="1">

  <div class="legend">
    <span class="box"></span> Available
    <span class="box unavail"></span> Unavailable
  </div>

  <?php foreach ($rowsByLabel as $label => $seatsInRow): ?>
    <div class="row-label">Row <?= htmlspecialchars($label) ?></div>
    <div class="grid" role="group" aria-label="Row <?= htmlspecialchars($label) ?>">
      <?php foreach ($seatsInRow as $s):
        $sid = (int)$s['id'];
        $isTaken = isset($unavailable[$sid]);
      ?>
        <label class="seat <?= $isTaken ? 'unavailable' : '' ?>">
          <?php if ($isTaken): ?>
            <input type="checkbox" disabled aria-disabled="true">
            <?= (int)$s['col_num'] ?>
          <?php else: ?>
            <input type="checkbox" name="seats[]" value="<?= $sid ?>">
            <?= (int)$s['col_num'] ?>
          <?php endif; ?>
        </label>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

  <div class="actions">
    <a class="btn ghost" href="<?= url('pages/showtimes.php') ?>">Back</a>
    <span class="spacer"></span>
    <button class="btn" type="submit">Confirm seats</button>
  </div>
</form>
  </main>

  <script>
    // Basic “selected” highlight for available seats
    document.addEventListener('change', (e) => {
      if (e.target.name === 'seats[]') {
        e.target.closest('.seat')?.classList.toggle('selected', e.target.checked);
      }
    });
  </script>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
