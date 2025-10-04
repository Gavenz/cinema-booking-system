<?php
// /pages/book.php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

if (!isset($_GET['showtime_id'])) { http_response_code(400); die('Missing showtime_id'); }
$showtime_id = (int)$_GET['showtime_id'];

/* Load showtime + hall + movie */
$st = $pdo->prepare("
  SELECT s.id, s.starts_at, h.id AS hall_id, h.name AS hall_name, h.rows_count, h.cols_count,
         m.title
  FROM showtimes s
  JOIN halls  h ON h.id = s.hall_id
  JOIN movies m ON m.id = s.movies_id
  WHERE s.id = :sid
");
$st->execute([':sid'=>$showtime_id]);
$si = $st->fetch();
if (!$si) { http_response_code(404); die('Showtime not found'); }

/* Seats taken for this showtime (pending or confirmed) */
$takenStmt = $pdo->prepare("
  SELECT bi.seat_id
  FROM booking_items bi
  JOIN booking b ON b.id = bi.booking_id
  WHERE bi.showtime_id = :sid AND b.booking_status IN ('pending','confirmed')
");
$takenStmt->execute([':sid'=>$showtime_id]);
$taken = array_fill_keys(array_column($takenStmt->fetchAll(), 'seat_id'), true);

/* All seats for the hall */
$seatsStmt = $pdo->prepare("
  SELECT id, row_label, col_num
  FROM seats
  WHERE halls_id = :hid
  ORDER BY row_label, col_num
");
$seatsStmt->execute([':hid'=>$si['hall_id']]);

/* Pricing options */
$pricing = $pdo->query("SELECT id, ticket_type, amount FROM pricing WHERE is_active=1 ORDER BY amount ASC")->fetchAll();
?>
<h2><?= htmlspecialchars($si['title']) ?> — <?= htmlspecialchars(date('D, d M Y H:i', strtotime($si['starts_at']))) ?> (<?= htmlspecialchars($si['hall_name']) ?>)</h2>

<div class="card">
  <form method="post" action="/cinema-booking-system/pages/do_book.php">
    <input type="hidden" name="showtime_id" value="<?= (int)$showtime_id ?>">

    <div style="display:grid;gap:6px;justify-content:start;">
      <?php
      $currentRow = null;
      while ($s = $seatsStmt->fetch()):
        $sid = (int)$s['id']; $row = $s['row_label']; $col = (int)$s['col_num'];
        if ($row !== $currentRow) {
          if ($currentRow !== null) echo "</div>";
          echo "<div style='display:flex;align-items:center;gap:6px;'><strong style='width:20px;'>".htmlspecialchars($row)."</strong>";
          $currentRow = $row;
        }
        $disabled = isset($taken[$sid]) ? 'disabled' : '';
        echo "<label style='display:inline-flex;align-items:center;gap:4px;border:1px solid #333;padding:4px 6px;border-radius:6px;opacity:" . ($disabled?'0.5':'1') . "'>";
        echo "<input type='checkbox' name='seat_ids[]' value='{$sid}' {$disabled}>";
        echo htmlspecialchars((string)$col);
        echo "</label>";
      endwhile;
      if ($currentRow !== null) echo "</div>";
      ?>
    </div>

    <div class="form-row" style="margin-top:12px;">
      <label>Ticket type (applies to all selected seats)</label>
      <select class="input" name="price_id" required>
        <?php foreach ($pricing as $p): ?>
          <option value="<?= (int)$p['id'] ?>" data-amt="<?= htmlspecialchars($p['amount']) ?>">
            <?= htmlspecialchars($p['ticket_type']) ?> — $<?= htmlspecialchars($p['amount']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <strong>Selected seats:</strong> <span id="seatCount">0</span> &nbsp;|&nbsp;
      <strong>Estimated total:</strong> $<span id="estTotal">0.00</span>
    </div>

    <button class="button" type="submit">Proceed</button>
  </form>
</div>

<script>
const seatInputs = Array.from(document.querySelectorAll('input[name="seat_ids[]"]'));
const priceSel = document.querySelector('select[name="price_id"]');
const seatCountEl = document.getElementById('seatCount');
const estTotalEl = document.getElementById('estTotal');
function recalc(){
  const count = seatInputs.filter(i=>i.checked).length;
  const amt = parseFloat(priceSel.selectedOptions[0].dataset.amt || '0');
  seatCountEl.textContent = count;
  estTotalEl.textContent = (count * amt).toFixed(2);
}
seatInputs.forEach(i=>i.addEventListener('change', recalc));
priceSel.addEventListener('change', recalc);
recalc();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
