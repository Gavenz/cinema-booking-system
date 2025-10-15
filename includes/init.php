<!-- Start a session and create connection to database -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// --- Flash messages (store in session, read once) ---
function flash_set(string $msg, string $type = 'info'): void {
  $_SESSION['__flash'][] = ['m' => $msg, 't' => $type];
}
function flash_get(): array {
  $msgs = $_SESSION['__flash'] ?? [];
  unset($_SESSION['__flash']);
  return $msgs;
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
?>
