<?php if(!isset($pageTitle)) $pageTitle='Cinema Booking System'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/cinema-booking-system/assets/styles.css">
</head>
<body>
<header>
  <h1>Cinema Booking System</h1>
<nav>
  <a href="/cinema-booking-system/pages/showtimes.php">Movies</a>
  <?php if (!isset($_SESSION['user_id'])): ?>
    <a href="/cinema-booking-system/pages/register.php">Register</a>
    <a href="/cinema-booking-system/pages/login.php">Login</a>
  <?php else: ?>
    <a href="/cinema-booking-system/pages/my_bookings.php">My Bookings</a>
    <span>Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
    <a href="/cinema-booking-system/pages/logout.php">Logout</a>
  <?php endif; ?>
</nav>
</header>
<main>