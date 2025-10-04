<?php
// /pages/cancel_booking.php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/../includes/init.php';

if (!isset($_SESSION['user_id'])) { header('Location: /cinema-booking-system/pages/login.php'); exit; }
$user_id = (int)$_SESSION['user_id'];

$booking_id = (int)($_GET['booking_id'] ?? 0);
if (!$booking_id) { header('Location: /cinema-booking-system/pages/my_bookings.php'); exit; }

$bstmt = $pdo->prepare("SELECT user_id, booking_status FROM booking WHERE id=?");
$bstmt->execute([$booking_id]);
$b = $bstmt->fetch(PDO::FETCH_ASSOC);
if (!$b) { header('Location: /cinema-booking-system/pages/my_bookings.php'); exit; }
if ((int)$b['user_id'] !== $user_id) { echo "Not authorized."; exit; }

if ($b['booking_status'] !== 'pending') {
  echo "Only pending bookings can be cancelled here.";
  exit;
}

$pdo->beginTransaction();
try {
  $upd = $pdo->prepare("UPDATE booking SET booking_status='cancelled' WHERE id=? AND booking_status='pending'");
  $upd->execute([$booking_id]);

  $del = $pdo->prepare("DELETE FROM booking_items WHERE booking_id=?");
  $del->execute([$booking_id]);

  $pdo->commit();
  header('Location: /cinema-booking-system/pages/my_bookings.php');
  exit;
} catch (Throwable $e) {
  $pdo->rollBack();
  echo "Cancel failed: " . htmlspecialchars($e->getMessage());
}
