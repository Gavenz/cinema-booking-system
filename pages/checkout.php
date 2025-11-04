<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/mail.php';


if (!isset($_SESSION['user'])) {
  flash_warn('Please log in first.');
  header('Location: ' . url('pages/login.php?next='.urlencode(url('pages/checkout.php'))));
  exit;
}
$uid = (int)$_SESSION['user']['id'];
$activeNav = 'showtimes';

// CSRF
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$CSRF = $_SESSION['csrf'];

// Inputs
$showtimeId = (int)($_GET['showtime_id'] ?? $_POST['showtime_id'] ?? 0);
$seatIdsParam = $_POST['seats'] ?? ($_GET['seats'] ?? []);
$seatIds      = array_values(array_filter(array_map('intval', (array)$seatIdsParam)));

// If we arrive here without seats (GET or initial POST), send them back
if (!$seatIds && !isset($_POST['confirm']) && !isset($_POST['add_to_cart'])) {
  flash_error('Please select at least one seat first.');
  header('Location: ' . url('pages/booking.php?showtime_id='.$showtimeId));
  exit;
}


// ---- Load pricing options ----
$prices = $pdo->query("SELECT id, ticket_type, amount FROM pricing WHERE is_active=1 ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
if (!$prices) {
  flash_error('No pricing configured.');
  header('Location: ' . url('pages/showtimes.php'));
  exit;
}
$priceMap = [];
foreach ($prices as $p) $priceMap[(int)$p['id']] = $p;

// ---- Load showtime + movie ----
$st = $pdo->prepare("
  SELECT s.id, s.starts_at, h.name AS hall_name,
         m.title, m.runtime_min, m.rating
  FROM showtimes s
  JOIN halls h   ON h.id = s.hall_id
  JOIN movies m  ON m.id = s.movies_id
  WHERE s.id = :sid
");
$st->execute([':sid'=>$showtimeId]);
$show = $st->fetch(PDO::FETCH_ASSOC);
if (!$show) {
  flash_error('Showtime not found.');
  header('Location: ' . url('pages/showtimes.php'));
  exit;
}

// ---- Map seat id -> label for summary ----
$seatMap = [];
if ($seatIds) {
  $in = implode(',', array_fill(0, count($seatIds), '?'));
  $q  = $pdo->prepare("SELECT id, row_label, col_num FROM seats WHERE id IN ($in)");
  $q->execute($seatIds);
  foreach ($q as $r) $seatMap[(int)$r['id']] = $r['row_label'].$r['col_num'];
}

// ---- Helpers ----
function seats_taken(PDO $pdo, int $showtimeId, array $seatIds): array {
  if (!$seatIds) return [];
  $in = implode(',', array_fill(0, count($seatIds), '?'));
  $sql = "SELECT bi.seat_id
          FROM booking_items bi
          JOIN booking b ON b.id = bi.booking_id
          WHERE bi.showtime_id = ?
            AND bi.seat_id IN ($in)
            AND (
              b.booking_status='confirmed'
              OR (b.booking_status='pending' AND (b.expires_at IS NULL OR b.expires_at > NOW()))
            )";
  $args = array_merge([$showtimeId], $seatIds);
  $chk  = $pdo->prepare($sql);
  $chk->execute($args);
  return $chk->fetchAll(PDO::FETCH_COLUMN, 0);
}

// Collect per-seat price selection from POST (seat_price[<seat_id>] = price_id)
$seatPricePosted = [];
if (!empty($_POST['seat_price']) && is_array($_POST['seat_price'])) {
  foreach ($_POST['seat_price'] as $sid => $pid) {
    $sid = (int)$sid;
    $pid = (int)$pid;
    if (in_array($sid, $seatIds, true) && isset($priceMap[$pid])) {
      $seatPricePosted[$sid] = $pid;
    }
  }
}

// Default every seat to first price if nothing posted yet
$defaultPriceId = (int)$prices[0]['id'];
$seatPrice = [];
foreach ($seatIds as $sid) {
  $seatPrice[$sid] = $seatPricePosted[$sid] ?? $defaultPriceId;
}

// Compute totals helper (sum line amounts)
function compute_total(array $seatPrice, array $priceMap): float {
  $sum = 0.0;
  foreach ($seatPrice as $pid) $sum += (float)$priceMap[$pid]['amount'];
  return $sum;
}
$total = compute_total($seatPrice, $priceMap);

// ---- POST: Confirm & Pay / Add to Cart ----
if ($_SERVER['REQUEST_METHOD']==='POST' && (isset($_POST['confirm']) || isset($_POST['add_to_cart']))) {
  if (!hash_equals($CSRF, $_POST['csrf'] ?? '')) {
    flash_error('Bad CSRF token.');
    header('Location: ' . url('pages/checkout.php?showtime_id='.$showtimeId));
    exit;
  }
  if (!$seatIds) {
    flash_error('Please select at least one seat.');
    header('Location: ' . url('pages/booking.php?showtime_id='.$showtimeId));
    exit;
  }

  // Validate every seat has a valid price
  foreach ($seatIds as $sid) {
    if (!isset($seatPrice[$sid]) || !isset($priceMap[(int)$seatPrice[$sid]])) {
      flash_error('Invalid ticket type selection for one or more seats.');
      header('Location: ' . url('pages/checkout.php?showtime_id='.$showtimeId));
      exit;
    }
  }

  // Re-check availability
  $taken = seats_taken($pdo, $showtimeId, $seatIds);
  if ($taken) {
    $labels = array_map(fn($x)=>$seatMap[(int)$x] ?? ('#'.$x), $taken);
    flash_error('Some seats just got taken: '.implode(', ',$labels));
    header('Location: ' . url('pages/booking.php?showtime_id='.$showtimeId));
    exit;
  }

  $qty   = count($seatIds);
  $total = compute_total($seatPrice, $priceMap);

  try {
    $pdo->beginTransaction();

    $status  = isset($_POST['confirm']) ? 'pending' : 'pending'; // both routes go pending
    $expires = "DATE_ADD(NOW(), INTERVAL 15 MINUTE)";

    $sqlB = "INSERT INTO booking (user_id, showtime_id, qty, total_amount, booking_status, expires_at, created_at)
            VALUES (:uid, :sid, :qty, :total, 'pending', $expires, NOW())";
    $insB = $pdo->prepare($sqlB);
    $insB->execute([':uid'=>$uid, ':sid'=>$showtimeId, ':qty'=>$qty, ':total'=>$total]);

$bookingId = (int)$pdo->lastInsertId();

    // Lines: per-seat price
    $insI = $pdo->prepare("
      INSERT INTO booking_items (booking_id, showtime_id, seat_id, price_id, line_amount)
      VALUES (:bid, :sid, :seat, :pid, :amt)
    ");
    foreach ($seatIds as $sid) {
      $pid = (int)$seatPrice[$sid];
      $amt = (float)$priceMap[$pid]['amount'];
      $insI->execute([
        ':bid'=>$bookingId, ':sid'=>$showtimeId, ':seat'=>$sid,
        ':pid'=>$pid, ':amt'=>$amt
      ]);
    }

    // Mock payment if confirmed
    if ($status === 'confirmed') {
      $insP = $pdo->prepare("
        INSERT INTO payments (booking_id, amount, method, status, transaction_ref)
        VALUES (:bid, :amt, 'card_visa', 'succeeded', :ref)
      ");
      $insP->execute([
        ':bid'=>$bookingId, ':amt'=>$total,
        ':ref'=>'TESTTX-'.bin2hex(random_bytes(6))
      ]);
    }

    $pdo->commit();

        // If user clicked "Confirm & Pay", go straight to payment for the created booking
    if (isset($_POST['confirm'])) {
      header('Location: ' . url('pages/payment.php?booking_id='.$bookingId));
      exit;
    }
      flash_success('Added to your list. You can confirm it from the cart.');
    header('Location: ' . url('pages/cart.php'));
    exit;

  } catch (PDOException $e) {
    $pdo->rollBack();
    if (stripos($e->getMessage(), 'uq_showtime_seat') !== false) {
      flash_error('One or more seats were just booked by someone else. Please pick again.');
      header('Location: ' . url('pages/booking.php?showtime_id='.$showtimeId));
      exit;
    }
    throw $e;
  }
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Checkout — Big Premiere Point</title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>?v=1">
  <style>
    .wrap{max-width:900px;margin:22px auto;padding:0 20px}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));
          border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:16px}
    .row{display:flex;gap:16px;flex-wrap:wrap}
    .pill{display:inline-flex;align-items:center;border:1px solid rgba(255,255,255,.16);
          background:rgba(255,255,255,.06);border-radius:999px;padding:6px 10px;font-weight:800}
    .pill.gold{background:#2a2a07;border-color:rgba(245,197,24,.25);color:#f5c518}
    .mt{margin-top:10px}
    .total{font-size:1.25rem;font-weight:900}
    .btn-lg{font-size:1.05rem;padding:12px 18px}

    /* Select styling: readable text */
    .select{
      min-width: 210px;
      padding:10px 12px;border-radius:10px;
      background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.15);
      color:var(--text);outline:none;font-weight:800
    }
    .select:focus{ box-shadow: var(--ring); border-color: rgba(255,255,255,.35); }
    select.select option { color:#0b0b0f; background:#ffffff; }

    .seat-line{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-top:6px}
    .seat-tag{font-weight:900}
    .actions{display:flex;gap:10px;align-items:center;margin-top:14px;flex-wrap:wrap}
    /* Ticket type select (match site inputs/buttons) */
        .seat-price{
        appearance:none;          /* hide native arrow */
        -webkit-appearance:none;
        -moz-appearance:none;
        min-width:210px;
        padding:10px 12px;
        border-radius:10px;
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.15);
        color:var(--text);
        font-weight:800;
        outline:none;
        }
        .seat-price:focus{
        box-shadow:var(--ring);
        border-color:rgba(255,255,255,.35);
        }
        /* dropdown menu itself should be light so items are readable */
        .seat-price option{ color:#0b0b0f; background:#fff; }
  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <div class="wrap">
    <h2>Checkout</h2>

    <div class="card">
      <div class="row">
        <div>
          <div style="font-weight:900"><?= htmlspecialchars($show['title']) ?></div>
          <div class="pill gold"><?= htmlspecialchars($show['rating'] ?? '') ?></div>
          <div class="pill"><?= (int)$show['runtime_min'] ?>m</div>
        </div>
        <div style="flex:1"></div>
        <div>
          <div class="pill"><?= htmlspecialchars($show['hall_name']) ?></div>
          <div class="pill"><?= local_time_label($show['starts_at'], 'D, j M Y • g:i A') ?></div>
        </div>
      </div>

      <form method="post" class="mt">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">
        <input type="hidden" name="showtime_id" value="<?= (int)$showtimeId ?>">
        <?php foreach ($seatIds as $sid): ?>
          <input type="hidden" name="seats[]" value="<?= (int)$sid ?>">
        <?php endforeach; ?>

        <strong>Seats &amp; ticket type:</strong>
        <?php if ($seatIds): foreach ($seatIds as $sid): ?>
          <div class="seat-line">
            <span class="pill seat-tag"><?= htmlspecialchars($seatMap[$sid] ?? ('#'.$sid)) ?></span>
            <select class="seat-price" name="seat_price[<?= (int)$sid ?>]" required>
              <?php foreach ($prices as $p): $pid=(int)$p['id']; ?>
                <option value="<?= $pid ?>" 
                    data-amount = "<?= htmlspecialchars($p['amount']) ?>" 
                    <?= ($seatPrice[$sid]===$pid?'selected':'') ?>>
                  <?= htmlspecialchars($p['ticket_type']) ?> — $<?= number_format((float)$p['amount'],2) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endforeach; else: ?>
          <div class="pill">No seats selected</div>
        <?php endif; ?>

        <div class="mt">
        <!-- live breakdown goes here -->
        <span id="breakdown"></span>
        <!-- always show a Total pill -->
        <span class="pill" style="font-weight:900">Total: <span id="total">$<?= number_format($total,2) ?></span></span>
        </div>
        <!-- expose the price map for JS (safe, it’s just type & amount) -->
        <script id="priceMap" type="application/json"><?= json_encode($priceMap, JSON_UNESCAPED_UNICODE) ?></script>
        <?php $hasSeats = !empty($seatIds); ?>
        <div class="actions">
          <button class="btn btn-lg" name="confirm" value="1" type="submit">Confirm &amp; Pay</button>
          <button class="btn btn-lg ghost" name="add_to_cart" value="1" type="submit">Add to Cart</button>
          <a class="btn small ghost" href="<?= url('pages/booking.php?showtime_id='.$showtimeId) ?>">Back</a>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>

    <script>
    (function(){
    const selects   = Array.from(document.querySelectorAll('.seat-price')); // one per seat
    const totalEl   = document.getElementById('total');
    const breakdown = document.getElementById('breakdown');
    const mapEl     = document.getElementById('priceMap');
    const priceMap  = mapEl ? JSON.parse(mapEl.textContent || '{}') : {};

    function fmt(n){ return '$' + n.toFixed(2); }

    function recalc(){
        let total = 0;
        const counts = {}; // pid -> qty

        for (const sel of selects) {
        const opt = sel.selectedOptions[0];
        const pid = sel.value;
        const amt = parseFloat(opt?.dataset.amount || '0') || 0;
        total += amt;
        counts[pid] = (counts[pid] || 0) + 1;
        }

        if (totalEl) totalEl.textContent = fmt(total);

        if (breakdown) {
        breakdown.innerHTML = Object.entries(counts).map(([pid, qty]) => {
            const p = priceMap[pid];
            if (!p) return '';
            return `<span class="pill">${p.ticket_type} × ${qty} ($${(+p.amount).toFixed(2)} each)</span>`;
        }).join(' ');
        }
    }

    selects.forEach(sel => sel.addEventListener('change', recalc));
    recalc();
    })();
    </script>

</body>
</html>
