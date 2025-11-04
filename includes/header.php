<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/flash.php';

$activeNav = $activeNav ?? null;
$isAuthed  = isset($_SESSION['user']);
$userName  = $isAuthed
  ? ($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? 'User')
  : null;
?>
<header class="nav" role="banner">
  <div class="nav-inner">
    <a class="brand" href="<?= url('brandon/cinemahomepage.php') ?>" aria-label="Big Premiere Point Home">
      <div class="logo" aria-hidden="true"></div>
      <div class="brand-title">Big Premiere Point</div>
    </a>

    <nav class="nav-links" aria-label="Primary">
      <a href="<?= url('brandon/movie.php') ?>"   class="<?= $activeNav==='movies'   ? 'active' : '' ?>">Movies</a>
      <a href="<?= url('brandon/theatre.php') ?>" class="<?= $activeNav==='theatres' ? 'active' : '' ?>">Find a Theatre</a>
      <a href="<?= url('pages/showtimes.php') ?>" class="<?= $activeNav==='showtimes' ? 'active' : '' ?>">Showtimes</a>
      <a href="<?= url('brandon/food.php') ?>" class="<?= $activeNav==='food' ? 'active' : '' ?>">Food &amp; Drinks</a>
      <a href="<?= url('brandon/aboutus.php') ?>" class ="<?= $activeNav==="about" ? 'active': ''?>">About Us</a>
    </nav>

    <div class="search-wrap" role="search">
      <svg class="icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input id="searchInput" type="search" placeholder="Search movies, genres…" aria-label="Search movies" />
    </div>

    <?php
      $csrf = $_SESSION['csrf'] ?? '';
    ?>
    <div class="user-area has-dropdown" aria-haspopup="true">
      <?php if ($isAuthed): ?>
        <button class="more-trigger" aria-expanded="false" aria-controls="user-menu">
          Hi, <?= htmlspecialchars($userName) ?>
          <svg class="chev" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <div id="user-menu" class="dropdown" role="menu" aria-label="Account">
          <?php if (is_admin()):?>
            <div style="border-top:1px solid rgba(255,255,255,.08); margin:6px 0;"></div>
            <a role="menuitem" href="<?= url('pages/admin.php') ?>">Admin Dashboard</a>
            <a role="menuitem" href="<?= url('pages/admin_movies.php?action=list') ?>">Manage Movies</a>
            <a role="menuitem" href="<?= url('pages/admin_showtimes.php?action=list') ?>">Manage Showtimes</a>
          <?php endif;?>
          
          <?php if (is_user()):?>
            <a role="menuitem" href="<?= url('pages/bookings.php') ?>">My Bookings</a>
            <a role="menuitem" href="<?= url('pages/cart.php') ?>">Cart</a>
          <?php endif;?>

          <div style="border-top:1px solid rgba(255,255,255,.08); margin:6px 0;"></div>
          <form action="<?= url('pages/logout.php') ?>" method="post" role="menuitem">
            <?php if ($csrf): ?>
              <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <?php endif; ?>
            <button class="btn small" type="submit" style="width:100%; text-align:center">Logout</button>
          </form>
        </div>
      <?php else: ?>
        <a class="btn" href="<?= url('pages/login.php') ?>">Login</a>
      <?php endif; ?>
    </div>
</header>

<!-- Minimal, shared flash styles (safe to keep here so all pages get them) -->
<style>
  .flash-wrap{max-width:960px;margin:12px auto 0;padding:0 20px}
  .flash{
    border:1px solid rgba(255,255,255,.15);
    border-radius:10px;
    padding:10px 12px;
    margin:8px 0;
    background:rgba(255,255,255,.06);
    color:#f3f3f8;
    font-weight:700;
  }
  .flash.success{border-color:rgba(52,199,89,.35);background:rgba(52,199,89,.18)}
  .flash.error{border-color:rgba(255,69,58,.35);background:rgba(255,69,58,.18)}
  .flash.info{border-color:rgba(100,210,255,.35);background:rgba(100,210,255,.18)}
</style>

<div class="flash-wrap" aria-live="polite">
  <?php flash_render(); ?>
</div>
