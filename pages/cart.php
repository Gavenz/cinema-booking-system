<?php
require_once __DIR__ . '/../includes/init.php';
if (!isset($_SESSION['user'])) {
  flash_set('auth','Please log in first.','warning');
  header('Location: ' . url('pages/login.php?next='.urlencode(url('pages/cart.php'))));
  exit;
}
$uid = (int)$_SESSION['user']['id'];
$activeNav = 'showtimes';

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$CSRF = $_SESSION['csrf'];

// --- POST handlers ---
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!hash_equals($CSRF, $_POST['csrf'] ?? '')) {
    flash_set('err','Bad CSRF token.','error');
    header('Location: ' . url('pages/cart.php'));
    exit;
  }
  $ids = array_filter(array_map('intval', $_POST['ids'] ?? []));

  // Remove selected
  if (isset($_POST['remove']) && $ids) {
    $in = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE b FROM booking b
            WHERE b.user_id = ? AND b.booking_status='pending'
              AND (b.expires_at IS NULL OR b.expires_at > NOW())
              AND b.id IN ($in)";
    $args = array_merge([$uid], $ids);
    $st = $pdo->prepare($sql);
    $st->execute($args);
    flash_set('ok','Removed from your list.','success');
    header('Location: ' . url('pages/cart.php'));
    exit;
  }

  // Confirm selected
  if (isset($_POST['confirm']) && $ids) {
    try {
      $pdo->beginTransaction();

      // Lock & fetch the rows we’ll confirm
      $in = implode(',', array_fill(0, count($ids), '?'));
      $sel = $pdo->prepare("
        SELECT b.id, b.total_amount
        FROM booking b
        WHERE b.user_id = ?
          AND b.booking_status='pending'
          AND (b.expires_at IS NULL OR b.expires_at > NOW())
          AND b.id IN ($in)
        FOR UPDATE
      ");
      $sel->execute(array_merge([$uid], $ids));
      $rows = $sel->fetchAll(PDO::FETCH_ASSOC);
      if (!$rows) {
        $pdo->rollBack();
        flash_set('err','Nothing to confirm. Holds may have expired.','error');
        header('Location: ' . url('pages/cart.php'));
        exit;
      }

      // Confirm each & create a (mock) payment
      $upd = $pdo->prepare("UPDATE booking SET booking_status='confirmed', expires_at=NULL WHERE id=:id");
      $insPay = $pdo->prepare("
        INSERT INTO payments (booking_id, amount, method, status, transaction_ref)
        VALUES (:bid, :amt, 'card_visa', 'succeeded', :ref)
      ");
      foreach ($rows as $r) {
        $upd->execute([':id'=>$r['id']]);
        $insPay->execute([
          ':bid'=>$r['id'],
          ':amt'=>$r['total_amount'],
          ':ref'=>'TESTTX-'.bin2hex(random_bytes(6))
        ]);
      }

      $pdo->commit();
      flash_set('ok','Booking(s) confirmed!','success');
      header('Location: ' . url('pages/bookings.php'));
      exit;

    } catch (PDOException $e) {
      $pdo->rollBack();
      flash_set('err','Could not confirm bookings. Please try again.','error');
      header('Location: ' . url('pages/cart.php'));
      exit;
    }
  }
}

// --- GET: list pending items ---
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
    .muted{color:#8b8ba1}
    .rowtop{display:flex;gap:12px;align-items:center;flex-wrap:wrap}
    .grow{flex:1}
    .empty{margin:16px 0;color:#8b8ba1}
  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <div class="wrap">
    <h2>Your Cart</h2>

    <form method="post">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($CSRF) ?>">

      <?php if (!$items): ?>
        <div class="empty">Your list is empty. Add a showtime from the seat selection/checkout page.</div>
      <?php else: ?>
        <div class="list">
          <?php foreach ($items as $it): ?>
            <label class="item">
              <div class="rowtop">
                <input type="checkbox" name="ids[]" value="<?= (int)$it['id'] ?>">
                <div class="title"><?= htmlspecialchars($it['title']) ?></div>
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
            </label>
          <?php endforeach; ?>
        </div>

        <div class="actions">
          <button class="btn ghost" name="remove" value="1" type="submit">Remove Selected</button>
          <button class="btn" name="confirm" value="1" type="submit">Confirm Selected</button>
        </div>
      <?php endif; ?>
    </form>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
