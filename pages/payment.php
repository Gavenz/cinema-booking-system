<?php
/**
 * payment.php
 *
 * Handles simulated payment and final booking confirmation.
 *
 * Responsibilities:
 * - Validates that the booking belongs to the logged-in user and is payable.
 * - Accepts selected payment method (e.g., Visa/Mastercard/PayNow).
 * - Wraps operations in a database transaction:
 *   - Inserts a new row into the payments table.
 *   - Updates the booking status to 'paid/confirmed' and sets timestamps.
 * - Triggers a confirmation email to the user via mail.php / PHPMailer.
 * - Redirects to a confirmation / "My Bookings" page with a success message.
 *
 * Supports Functional Requirement F12 / F14 (Payment Page).
 */
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/mail.php';

if (!isset($_SESSION['user'])) {
  flash_warn('Please log in first.');
  header('Location: ' . url('pages/login.php'));
  exit;
}
$uid       = (int)$_SESSION['user']['id'];
$activeNav = 'showtimes';

// booking_id comes from checkout OR cart
$bookingId = (int)($_GET['booking_id'] ?? $_GET['id'] ?? $_POST['booking_id'] ?? 0);
if ($bookingId <= 0) {
  flash_error('Missing booking.');
  header('Location: ' . url('pages/cart.php'));
  exit;
}

/* -------------------- Load booking summary (for GET render) -------------------- */
$st = $pdo->prepare("
  SELECT b.id, b.user_id, b.showtime_id, b.qty, b.total_amount, b.booking_status,
         b.expires_at, b.created_at,
         s.starts_at, h.name AS hall_name,
         m.title, m.runtime_min, m.rating
  FROM booking b
  JOIN showtimes s ON s.id = b.showtime_id
  JOIN halls h     ON h.id = s.hall_id
  JOIN movies m    ON m.id = s.movies_id
  WHERE b.id = :bid AND b.user_id = :uid
  LIMIT 1
");
$st->execute([':bid'=>$bookingId, ':uid'=>$uid]);
$bk = $st->fetch(PDO::FETCH_ASSOC);
if (!$bk) {
  flash_error('Booking not found.');
  header('Location: ' . url('pages/cart.php'));
  exit;
}

// If already processed, bounce gracefully
if ($bk['booking_status'] !== 'pending') {
  flash_info('This booking is already processed.');
  header('Location: ' . url('pages/bookings.php'));
  exit;
}

// Expired? (soft check for GET)
if (!empty($bk['expires_at']) && strtotime($bk['expires_at']) <= time()) {
  flash_error('This pending booking has expired. Please reselect seats.');
  header('Location: ' . url('pages/booking.php?showtime_id='.$bk['showtime_id']));
  exit;
}

// Load line items (for display + email)
$it = $pdo->prepare("
  SELECT bi.seat_id, bi.price_id, bi.line_amount,
         CONCAT(se.row_label, se.col_num) AS seat_label,
         p.ticket_type
  FROM booking_items bi
  JOIN seats   se ON se.id = bi.seat_id
  JOIN pricing p  ON p.id  = bi.price_id
  WHERE bi.booking_id = :bid
  ORDER BY se.row_label, se.col_num
");
$it->execute([':bid'=>$bookingId]);
$items = $it->fetchAll(PDO::FETCH_ASSOC);

/* ----------------------------- POST: pay/confirm ------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
  csrf_check('POST');   // verify token centrally

  $method = $_POST['method'] ?? 'card_visa'; // card_visa|card_master|gpay|applepay
  $valid  = ['card_visa','card_master','gpay','applepay'];
  if (!in_array($method, $valid, true)) $method = 'card_visa';

  try {
    $pdo->beginTransaction();

    // Lock the row to prevent races & re-check status/expiry atomically
    $lock = $pdo->prepare("
      SELECT booking_status, expires_at, total_amount
      FROM booking
      WHERE id = :bid AND user_id = :uid
      FOR UPDATE
    ");
    $lock->execute([':bid'=>$bookingId, ':uid'=>$uid]);
    $row = $lock->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      throw new Exception('Booking not found.');
    }
    if ($row['booking_status'] !== 'pending') {
      throw new Exception('This booking has already been processed.');
    }
    if (!empty($row['expires_at']) && strtotime($row['expires_at']) <= time()) {
      throw new Exception('This booking has expired.');
    }

    // Mark as confirmed/paid (protect again with WHERE condition)
    $up = $pdo->prepare("
      UPDATE booking
         SET booking_status = 'confirmed',
             paid_at        = NOW(),
             expires_at     = NULL
       WHERE id = :bid
         AND user_id = :uid
         AND booking_status = 'pending'
         AND (expires_at IS NULL OR expires_at > NOW())
    ");
    $up->execute([':bid'=>$bookingId, ':uid'=>$uid]);

    if ($up->rowCount() !== 1) {
      throw new Exception('Unable to confirm this booking (it may have expired).');
    }

    // Insert mock payment record
    $insP = $pdo->prepare("
      INSERT INTO payments (booking_id, amount, method, status, transaction_ref, created_at)
      VALUES (:bid, :amt, :method, 'succeeded', :ref, NOW())
    ");
    $insP->execute([
      ':bid'    => $bookingId,
      ':amt'    => (float)$row['total_amount'],
      ':method' => $method,
      ':ref'    => strtoupper($method) . '-' . bin2hex(random_bytes(5)),
    ]);

    $pdo->commit();

    // Send confirmation email (best-effort)
    $toEmail = $_SESSION['user']['email'] ?? '';
    if ($toEmail) {
      $toName  = $_SESSION['user']['username'] ?? 'Guest';
      send_booking_email($pdo, $bookingId, $toEmail, $toName);
    }

    flash_success('Payment successful. Booking confirmed!');
    header('Location: ' . url('pages/bookings.php'));
    exit;

  } catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('PAYMENT ERROR: '.$e->getMessage());
    flash_error($e->getMessage(),);
    header('Location: ' . url('pages/payment.php?booking_id='.$bookingId));
    exit;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Payment — Big Premiere Point</title>
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
    .paybox{margin-top:14px;display:grid;gap:10px}
    .method{display:flex;align-items:center;gap:10px}
    .note{color:#8b8ba1}
    .field{display:grid;gap:6px}
    .input{padding:10px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);color:#fff}
    .actions{display:flex;gap:10px;align-items:center;margin-top:16px;flex-wrap:wrap}
    .hide{display:none}
  </style>
</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <div class="wrap">
    <h2>Payment</h2>
    <div class="card">
      <div class="row">
        <div>
          <div style="font-weight:900"><?= htmlspecialchars($bk['title']) ?></div>
          <div class="pill gold"><?= htmlspecialchars($bk['rating'] ?? '') ?></div>
          <div class="pill"><?= (int)$bk['runtime_min'] ?>m</div>
        </div>
        <div style="flex:1"></div>
        <div>
          <div class="pill"><?= htmlspecialchars($bk['hall_name']) ?></div>
          <div class="pill"><?= local_time_label($bk['starts_at'], 'D, j M Y • g:i A') ?></div>
        </div>
      </div>

      <div class="mt"><strong>Seats:</strong>
        <?php foreach ($items as $r): ?>
          <span class="pill"><?= htmlspecialchars($r['seat_label']) ?></span>
        <?php endforeach; ?>
      </div>

      <div class="mt"><strong>Total:</strong> <span class="pill">$<?= number_format((float)$bk['total_amount'],2) ?></span></div>

      <form method="post" class="paybox">
        <?= csrf_field() ?>
        <input type="hidden" name="booking_id" value="<?= (int)$bookingId ?>">

        <div class="method">
          <label><input type="radio" name="method" value="card_visa" checked> Visa</label>
          <label><input type="radio" name="method" value="card_master"> MasterCard</label>
          <label><input type="radio" name="method" value="gpay"> Google&nbsp;Pay</label>
          <label><input type="radio" name="method" value="applepay"> Apple&nbsp;Pay</label>
        </div>

        <!-- Mock card fields, shown for Visa/Master only -->
        <div id="cardFields" class="field">
          <div class="field">
            <label>Card number</label>
            <input class="input" placeholder="4111 1111 1111 1111">
          </div>
          <div class="field" style="display:flex;gap:10px">
            <div style="flex:1">
              <label>Expiry (MM/YY)</label>
              <input class="input" placeholder="12/29">
            </div>
            <div style="flex:1">
              <label>CVC</label>
              <input class="input" placeholder="123">
            </div>
          </div>
          <div class="note">Mock form only — any values accepted.</div>
        </div>

        <div class="actions">
          <button class="btn btn-lg" type="submit" name="pay" value="1">Pay now</button>
          <a class="btn small ghost" href="<?= url('pages/cart.php') ?>">Back</a>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>

  <script>
    // Toggle card fields only for Visa/Master
    (function(){
      const radios = document.querySelectorAll('input[name="method"]');
      const card = document.getElementById('cardFields');
      function refresh(){
        const val = document.querySelector('input[name="method"]:checked')?.value;
        card.classList.toggle('hide', !(val==='card_visa' || val==='card_master'));
      }
      radios.forEach(r => r.addEventListener('change', refresh));
      refresh();
    })();
  </script>
</body>
</html>
