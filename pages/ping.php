<?php
// pages/ping.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../includes/db.php';  // uses your PDO code

try {
  // simple SELECT to prove we can talk to MySQL and the cinema DB
  $stmt = $pdo->query("SELECT COUNT(*) AS c FROM movies");
  $row = $stmt->fetch();
  echo "<h1>DB OK ✅</h1>";
  echo "<p>movies rows: <strong>".(int)$row['c']."</strong></p>";
} catch (Throwable $e) {
  echo "<h1>DB ERROR ❌</h1>";
  echo "<pre>".htmlspecialchars($e->getMessage())."</pre>";
}
