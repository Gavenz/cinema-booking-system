<?php
// reserve.php
require_once __DIR__ . '/includes/init.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- Config ---
$LOCK_MINUTES = 10;

// --- Auth stub (replace with your login session) ---
$user_id = $_SESSION['user_id'] ?? 1;

// --- Validate input ---
$showtime_id = (int)($_POST['showtime_id'] ?? 0);
$seats = $_POST['seats'] ?? []; // array of seat_id
$price_ids = $_POST['price_id'] ?? []; // optional parallel array

if (!$showtime_id || empty($seats) || !is_array($seats)) {
  http_response_code(400);
  echo "Missing showtime or seats selection.";
  exit;
}

// Expire old pendings (lightweight cleanup)
$conn->query("UPDATE booking SET booking_status='expired' WHERE booking_status='pending' AND expires_at IS NOT NULL AND expires_at < NOW()");
$conn->query("DELETE bi FROM booking_items bi JOIN booking b ON b.id=bi.booking_id WHERE b.booking_status IN ('expired','cancelled')");

// Fetch base price from showtime (fallback if no price per seat)
$bpstmt = $conn->prepare("SELECT st.base_price, m.title, st.start_time FROM showtimes st JOIN movies m ON m.id=st.movies_id WHERE st.id=?");
$bpstmt->bind_param('i', $showtime_id);
$bpstmt->execute();
$st = $bpstmt->get_result()->fetch_assoc();
if (!$st) { echo "Invalid showtime."; exit; }
$base_price = (float)$st['base_price'];

// Begin transaction
$conn->begin_transaction();

try {
  // A) create pending booking
  $expires_at = date('Y-m-d H:i:s', time() + $LOCK_MINUTES * 60);
  $init = $conn->prepare("INSERT INTO booking (user_id, showtime_id, qty, total_amount, booking_status, expires_at) VALUES (?, ?, 0, 0.00, 'pending', ?)");
  $init->bind_param('iis', $user_id, $showtime_id, $expires_at);
  $init->execute();
  $booking_id = $init->insert_id;

  // B) add booking items (one per seat)
  $bi = $conn->prepare("INSERT INTO booking_items (booking_id, showtime_id, seat_id, price_id, line_amount) VALUES (?, ?, ?, ?, ?)");
  foreach ($seats as $idx => $seat_id_raw) {
    $seat_id = (int)$seat_id_raw;
    $price_id = isset($price_ids[$idx]) ? (int)$price_ids[$idx] : 0;
    $line_amount = $base_price; // or compute per-seat pricing logic

    $bi->bind_param('iiiid', $booking_id, $showtime_id, $seat_id, $price_id, $line_amount);
    $bi->execute(); // May throw on uq_showtime_seat if taken by someone else
  }

  // C) recompute qty + total_amount
  $sum = $conn->prepare("
    UPDATE booking b
    JOIN (
      SELECT booking_id, COUNT(*) qty_calc, COALESCE(SUM(line_amount),0) total_calc
      FROM booking_items WHERE booking_id=? GROUP BY booking_id
    ) x ON x.booking_id = b.id
    SET b.qty = x.qty_calc, b.total_amount = x.total_calc
    WHERE b.id = ?
  ");
  $sum->bind_param('ii', $booking_id, $booking_id);
  $sum->execute();

  // Commit and redirect to payment
  $conn->commit();
  header("Location: /payment.php?booking_id=".$booking_id);
  exit;

} catch (mysqli_sql_exception $e) {
  $conn->rollback();

  // If seat already taken -> message
  if ($e->getCode() === 1062) {
    echo "Sorry, one or more seats were just taken. Please refresh and pick again.";
  } else {
    echo "Error: " . htmlspecialchars($e->getMessage());
  }
}
