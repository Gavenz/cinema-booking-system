<?php
/**
 * cart.php
 *
 * Shopping cart page for cinema bookings.
 *
 * Responsibilities:
 * - Shows all pending booking items in the user's cart.
 * - Lets users adjust quantities/remove items.
 * - Recalculates subtotals and total amount based on ticket types and pricing.
 * - Provides a button to proceed to checkout/payment.
 *
 * Supports Functional Requirement F13 (Cart Page).
 */
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['user'])) {
  flash_warn('Please log in first.');
  header('Location: ' . url('pages/login.php?next='.urlencode(url('pages/cart.php'))));
  exit;
}
$uid       = (int)$_SESSION['user']['id'];
$activeNav = 'showtimes';

// ---- POST handlers ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check('POST');

  // Normalize selected ids from checkboxes
  $ids = array_values(array_unique(array_filter(array_map('intval', $_POST['ids'] ?? []))));

  // Remove selected (0..many)
  if (isset($_POST['remove'])) {
    if (!$ids) {
      flash_error('Select at least one booking to remove.');
      header('Location: ' . url('pages/cart.php'));
      exit;
    }
// --- Load current user's active cart/booking items from the database ---
    $in   = implode(',', array_fill(0, count($ids), '?'));
    $sql  = "DELETE b FROM booking b
             WHERE b.user_id = ?
               AND b.booking_status = 'pending'
               AND (b.expires_at IS NULL OR b.expires_at > NOW())
               AND b.id IN ($in)";
    $args = array_merge([$uid], $ids);

    $st = $pdo->prepare($sql);
    $st->execute($args);
// --- Handle updates: remove item ---
    flash_success('Removed from your list.');
    header('Location: ' . url('pages/cart.php'));
    exit;
  }

  // Confirm selected (must be exactly one)
  if (isset($_POST['confirm'])) {
    if (count($ids) !== 1) {
      flash_error('Please select exactly one booking to pay.');
      header('Location: ' . url('pages/cart.php'));
      exit;
    }
    $bid = (int)$ids[0];

    // sanity-check still pending & not expired and belongs to user
    $chk = $pdo->prepare("
      SELECT b.id
      FROM booking b
      WHERE b.id = :bid
        AND b.user_id = :uid
        AND b.booking_status = 'pending'
        AND (b.expires_at IS NULL OR b.expires_at > NOW())
      LIMIT 1
    ");
    $chk->execute([':bid'=>$bid, ':uid'=>$uid]);

    if (!$chk->fetch()) {
      flash_error('That booking is no longer available to pay (maybe expired).');
      header('Location: ' . url('pages/cart.php'));
      exit;
    }

    // hand off to payment page
    header('Location: ' . url('pages/payment.php?booking_id=' . $bid));
    exit;
  }
}

// ---- GET: list pending items ----------------------------------------------
$sql = "
  SELECT b.id, b.qty, b.total_amount, b.expires_at,
         s.starts_at, h.name AS hall_name,
         m.title, m.rating, m.runtime_min
  FROM booking b
  JOIN showtimes s ON s.id = b.showtime_id
  JOIN halls h     ON h.id = s.hall_id
  JOIN movies m    ON m.id = s.movies_id
  WHERE b.user_id = :uid
    AND b.booking_status = 'pending'
    AND (b.expires_at IS NULL OR b.expires_at > NOW())
  ORDER BY b.created_at DESC
";
$st = $pdo->prepare($sql);
$st->execute([':uid'=>$uid]);
$items = $st->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Your Cart — Big Premiere Point</title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>?v=1">
  <style>
    .wrap{max-width:1000px;margin:22px auto;padding:0 20px}
    .list{display:grid;gap:12px}
    .item{display:flex;gap:14px;align-items:center;justify-content:space-between;
          border:1px solid rgba(255,255,255,.12);border-radius:14px;
          background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02));padding:12px 14px}
    .title{font-weight:900}
    .pill{display:inline-flex;align-items:center;border:1px solid rgba(255,255,255,.16);
          background:rgba(255,255,255,.06);border-radius:999px;padding:6px 10px;font-weight:800;margin-right:6px}
    .pill.gold{background:#2a2a07;border-color:rgba(245,197,24,.25);color:#f5c518}
    .actions{display:flex;gap:10px;justify-content:flex-end;margin-top:12px}
    .empty{margin:16px 0;color:#8b8ba1}
    .rowtop{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .grow{flex:1}
  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <div class="wrap">
    <h2>Your Cart</h2>

    <form method="post">
      <?= csrf_field() ?>

      <?php if (!$items): ?>
        <div class="empty">Your list is empty. Add a showtime from the seat selection/checkout page.</div>
      <?php else: ?>
        <div class="list">
          <?php foreach ($items as $it): ?>
            <?php $bid = (int)$it['id']; $cid = 'c_'.$bid; ?>
            <div class="item">
              <div class="rowtop">
                <input type="checkbox" id="<?= $cid ?>" name="ids[]" value="<?= $bid ?>" class="rowBox">
                <label for="<?= $cid ?>" class="title" style="cursor:pointer;">
                  <?= htmlspecialchars($it['title']) ?>
                </label>
                <span class="pill gold"><?= htmlspecialchars($it['rating'] ?? '') ?></span>
                <span class="pill"><?= (int)$it['runtime_min'] ?>m</span>
              </div>
              <div class="grow"></div>
              <div>
                <span class="pill"><?= htmlspecialchars($it['hall_name']) ?></span>
                <span class="pill"><?= local_time_label($it['starts_at'], 'D, j M Y • g:i A') ?></span>
                <span class="pill">Qty: <?= (int)$it['qty'] ?></span>
                <span class="pill">Total: $<?= number_format((float)$it['total_amount'], 2) ?></span>
                <?php if (!empty($it['expires_at'])): ?>
                  <span class="pill">Hold until: <?= local_time_label($it['expires_at'], 'g:i A') ?></span>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="actions">
          <button class="btn ghost" id="rmBtn"  name="remove"  value="1" type="submit" disabled>Remove Selected</button>
          <button class="btn"       id="payBtn" name="confirm" value="1" type="submit" disabled>Confirm Selected</button>
        </div>

        <script>
          // Enable/disable buttons based on how many boxes are checked
          const boxes  = Array.from(document.querySelectorAll('.rowBox'));
          const rmBtn  = document.getElementById('rmBtn');
          const payBtn = document.getElementById('payBtn');

          function updateButtons(){
            const n = boxes.filter(b => b.checked).length;
            rmBtn.disabled  = n === 0;  // need at least one to remove
            payBtn.disabled = n !== 1;  // exactly one to confirm/pay
          }
          boxes.forEach(b => b.addEventListener('change', updateButtons));
          updateButtons();
        </script>
      <?php endif; ?>
    </form>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
