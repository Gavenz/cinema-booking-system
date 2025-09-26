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
    <a href="/cinema-booking-system/pages/index.php">Home</a> |
    <a href="/cinema-booking-system/pages/movies.php">Movies</a> |
    <a href="/cinema-booking-system/pages/account.php">My Bookings</a>
  </nav>
</header>
<main>