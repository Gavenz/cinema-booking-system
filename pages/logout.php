<?php
require_once __DIR__ . '/../includes/init.php'; // already pulls in flash.php

// Only accept POST + valid CSRF
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  flash_error('Invalid request method.');
  header('Location: ' . url('brandon/cinemahomepage.php'));
  exit;
}
csrf_check('POST'); // centralized verifier (reads POST['csrf'] / header)

// ---- Destroy current session ----
if (session_status() === PHP_SESSION_ACTIVE) {
  // Clear all session variables
  $_SESSION = [];

  // Delete the session cookie
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $p['path'],
      $p['domain'],
      $p['secure'],
      $p['httponly']
    );
  }

  // Kill the session storage
  session_destroy();
}

// ---- Start a fresh session for flash + rotate id ----
session_start();
session_regenerate_id(true);

flash_success('You have been logged out.');
header('Location: ' . url('brandon/cinemahomepage.php'));
exit;
