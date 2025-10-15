<?php
// includes/flash.php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * Set a flash message to show on the next request (after redirect).
 * $key is just a namespace so you can overwrite a category if needed.
 * Types: info | success | error (you can add more in CSS)
 */
function flash_set(string $key, string $message, string $type='info'): void {
  $_SESSION['flash'][$key] = ['msg' => $message, 'type' => $type];
}

/**
 * Render and clear all flash messages.
 * Call once in your layout (we do it in header.php).
 */
function flash_render(): void {
  if (empty($_SESSION['flash'])) return;
  foreach ($_SESSION['flash'] as $key => $data) {
    $cls = htmlspecialchars($data['type']); // info|success|error
    $msg = htmlspecialchars($data['msg']);
    echo "<div class=\"flash {$cls}\">{$msg}</div>";
    unset($_SESSION['flash'][$key]);
  }
}
