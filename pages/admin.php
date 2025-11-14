<?php
/**
 * admin.php
 *
 * Admin dashboard page.
 *
 * Responsibilities:
 * - Restricts access to admin users only.
 * - Provides an overview of key metrics (total bookings, upcoming showtimes, etc.).
 * - Links to manage movies, showtimes and reports pages.
 *
 * Supports Functional Requirement F16 (Admin Dashboard Page).
 */

require_once __DIR__ . '/../includes/init.php';
require_admin(); // blocks non-admins
$activeNav = 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard — Big Premiere Point</title>
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>">
  <style>
    body { font-family: system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif; margin:0; }
    main { max-width: 1080px; margin: 2rem auto; padding: 0 1rem; }
    h1 { margin-bottom: .5rem; }
    p.intro { color:#666; margin-bottom:2rem; }

    .grid { display:grid; gap:1.5rem; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); }
    .card {
      background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border: 1px solid rgba(255,255,255,.08);
      border-radius:14px;
      box-shadow:0 1px 2px rgba(0,0,0,.05);
      padding:1.25rem 1rem 1.5rem;
    .card h2 { margin:.2rem 0 .6rem; font-size:1.2rem; }
    .card p { color:wheat; min-height:2.2em; }
    .btn {
      display:inline-block;
      background:#e50914;
      color:#fff;
      text-decoration:none;
      padding:.55rem .9rem;
      border-radius:999px;
      font-weight:600;
      box-shadow:0 6px 12px rgba(229,9,20,.25);
    }
    .btn:hover { filter:brightness(1.05); }
    footer { text-align:center; margin:3rem 0 1rem; color:#888; font-size:.9rem; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<main>
  <h1>Admin Dashboard</h1>
  <p class="intro">Manage movies, showtimes, and other cinema data.</p>

  <div class="grid">
    <!-- Movies card -->
    <div class="card">
      <h2>🎬 Movies</h2>
      <p>Create, edit, or delete movies from the database.</p>
      <a class="btn" href="<?= url('pages/admin_movies.php?action=list') ?>">Manage Movies</a>
    </div>

    <!-- Showtimes card -->
    <div class="card">
      <h2>🕒 Showtimes</h2>
      <p>Add, update, or remove showtimes for existing movies.</p>
      <a class="btn" href="<?= url('pages/admin_showtimes.php?action=list') ?>">Manage Showtimes</a>
    </div>

    <!-- Future expansion -->
    <div class="card">
      <h2>📋 Reports </h2>
      <p>View Revenue and Seat Occupancy</p>
      <a class="btn" href="<?= url('pages/admin_reports.php?action=list') ?>">Manage Reports</a>
    </div>

    <div class="card">
      <h2>👥 Users / Bookings</h2>
      <p>View or monitor user bookings (future feature).</p>
      <a class="btn" href="#" onclick="alert('Not implemented yet');return false;">Coming Soon</a>
    </div>
  </div>

  <footer>
    Logged in as <strong><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?></strong>
  </footer>
</main>
</body>
</html>
