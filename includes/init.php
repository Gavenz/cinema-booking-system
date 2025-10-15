<!-- Start a session and create connection to database -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
?>
