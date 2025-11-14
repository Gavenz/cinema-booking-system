<?php
/**
 * flash.php
 *
 * Provides helper functions for one-time "flash" messages stored in the session.
 *
 * Responsibilities:
 * - Define flash_success(), flash_warn(), flash_error() to queue messages.
 * - Define a render/consume function used by header/footer templates to display
 *   notifications after redirects (e.g. login, logout, booking, payment).
 *
 * Supports user feedback across all Functional Requirements by providing a
 * common way to show success/error/warning messages.
 */

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/** Core setter (queued for next request) */
if (!function_exists('flash')) {
  function flash(string $type, string $message): void {
    $_SESSION['flash'] ??= [];
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
  }
}

/** Same-request flash */
if (!function_exists('flash_now')) {
  function flash_now(string $type, string $message): void {
    $GLOBALS['flash_now'] ??= [];
    $GLOBALS['flash_now'][] = ['type' => $type, 'message' => $message];
  }
}

if (!function_exists('flash_success')) {
  function flash_success(string $m): void { flash('success', $m); }
}
if (!function_exists('flash_error')) {
  function flash_error(string $m): void { flash('error', $m); }
}
if (!function_exists('flash_warn')) {
  function flash_warn(string $m): void { flash('warning', $m); }
}
if (!function_exists('flash_info')) {
  function flash_info(string $m): void { flash('info', $m); }
}

/** Renderer */
if (!function_exists('flash_render')) {
  function flash_render(): void {
    $items = array_merge($GLOBALS['flash_now'] ?? [], $_SESSION['flash'] ?? []);
    $GLOBALS['flash_now'] = [];
    unset($_SESSION['flash']);
    if (!$items) return;
    foreach ($items as $it) {
      $type = $it['type'] ?? 'info';
      $msg  = $it['message'] ?? '';
      $cls  = match ($type) {
        'success' => 'flash success',
        'error'   => 'flash error',
        'warning' => 'flash warning',
        default   => 'flash info',
      };
      echo '<div class="'.$cls.'">'.htmlspecialchars($msg).'</div>';
    }
  }
}

/** Backward compatibility */
if (!function_exists('flash_set')) {
  function flash_set(string $keyOrType, string $message, string $maybeType='info'): void {
    $known = ['success','error','warning','info'];
    $type  = in_array($keyOrType, $known, true) ? $keyOrType : $maybeType;
    flash($type, $message);
  }
}
