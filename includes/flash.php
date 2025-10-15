<?php
// includes/flash.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set a flash message to be shown on the next request.
if (!function_exists('flash_set')) {
function flash_set(string $key, string $message, string $type = 'info'): void {
    $_SESSION['flash'][$key] = ['m' => $message, 't' => $type]; // m=message, t=type
}}
 
// Get all flashes (and clear them). Returns an array like:
// [ ['m'=>'Welcome back!','t'=>'success'], ... ]
if (!function_exists('flash_get')) {
function flash_get(): array {
    $out = array_values($_SESSION['flash'] ?? []);
    unset($_SESSION['flash']);
    return $out;
}}
 
// Convenience renderer if you ever want to echo directly.
if (!function_exists('flash_render')) {
function flash_render(): void {
    foreach (flash_get() as $f) {
        $t = htmlspecialchars($f['t']);
        $m = htmlspecialchars($f['m']);
        echo "<div class=\"flash {$t}\">{$m}</div>";
    }
}}
