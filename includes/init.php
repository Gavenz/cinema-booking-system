<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ----- CSRF helpers -----
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

if (!function_exists('csrf_token')) {
  function csrf_token(): string {
    return $_SESSION['csrf'] ?? '';
  }
}

if (!function_exists('csrf_field')) {
  function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' .
           htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
  }
}
// Your app is under the parent docroot, so use the folder:
//usage: href= "<?= url('pages/bookings.php')?">

define('BASE_URL', '/cinema-booking-system');

function url(string $path=''): string {
  return rtrim(BASE_URL,'/') . '/' . ltrim($path,'/');
}

// pdo connection
$dsn = 'mysql:host=127.0.0.1;dbname=cinema;charset=utf8mb4';
$user = 'root';
$pass = '';
$pdo = new PDO($dsn, $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

//Flash helpers from flash.php
require_once __DIR__ .'/flash.php';


// includes/init.php (early in the file)
date_default_timezone_set('Asia/Singapore');
$pdo->exec("SET time_zone = '+08:00'");
// --- Timezone used for display (keep in one place) ---
$APP_TZ = 'Asia/Singapore';

function local_time_label(string $dt, string $fmt = 'g:i A'): string {
  return date($fmt, strtotime($dt));
}

// Fetch showtimes for a specific calendar day (local day)
function db_showtimes_by_day(PDO $pdo, string $dayYmd): array {
  // If your DB timestamps are already in local time, you can simplify WHERE to DATE(s.starts_at) = :d
  $sql = "
    SELECT 
      s.id           AS showtime_id,
      s.starts_at    AS starts_at,
      m.id           AS movie_id,
      m.title        AS title,
      m.poster_url   AS poster_url,
      m.runtime_min  AS runtime_min,
      m.rating       AS age_rating
    FROM showtimes s
    JOIN movies    m ON m.id = s.movies_id
    WHERE DATE(s.starts_at) = :d
    ORDER BY m.title, s.starts_at
  ";
  $st = $pdo->prepare($sql);
  $st->execute([':d' => $dayYmd]);
  return $st->fetchAll(PDO::FETCH_ASSOC);

}// --- Housekeep: flip overdue pending bookings to 'expired' (run at most 1/min per session)
function housekeep_expired_bookings(PDO $pdo): void {
  // throttle per session to avoid running on every single request
  $now = time();
  if (!empty($_SESSION['last_housekeep']) && ($now - $_SESSION['last_housekeep'] < 60)) {
    return;
  }
  $_SESSION['last_housekeep'] = $now;
    $pdo->exec("
    UPDATE booking
       SET booking_status = 'expired'
     WHERE booking_status = 'pending'
       AND expires_at IS NOT NULL
       AND expires_at <= NOW()
  ");

    $pdo->exec("
      DELETE bi
        FROM booking_items bi
        JOIN booking b ON b.id = bi.booking_id
      WHERE b.booking_status = 'expired'
    ");
    ;
    }

housekeep_expired_bookings($pdo);
?>


