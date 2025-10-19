<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/flash.php';

// Only accept POST + valid CSRF
if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
  header('Location: ' . url('brandon/cinemahomepage.php'));
  exit;
}

// Clear session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

// Fresh session for flash + new CSRF
session_start();
flash_set('ok', 'You have been logged out.', 'success');

// Optional: rotate the session id
session_regenerate_id(true);

header('Location: ' . url('brandon/cinemahomepage.php'));
exit;
