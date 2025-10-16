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
      <a href="<?= url('') ?>#movies"   class="<?= $activeNav==='movies'   ? 'active' : '' ?>">Movies</a>
      <a href="<?= url('') ?>#theatres" class="<?= $activeNav==='theatres' ? 'active' : '' ?>">Find a Theatre</a>
      <a href="<?= url('brandon/food.php') ?>" class="<?= $activeNav==='food' ? 'active' : '' ?>">Food &amp; Drinks</a>

      <div class="has-dropdown" aria-haspopup="true">
        <button class="more-trigger" aria-expanded="false" aria-controls="more-menu">
          More
          <svg class="chev" width="14" height="14" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <div id="more-menu" class="dropdown" role="menu" aria-label="More">
          <a role="menuitem" href="<?= url('') ?>#merch">Merchandise</a>
          <a role="menuitem" href="<?= url('') ?>#gifts">Gift Cards</a>
          <a role="menuitem" href="<?= url('brandon/aboutus.php') ?>#about">About Us</a>
        </div>
      </div>
    </nav>

    <div class="search-wrap" role="search">
      <svg class="icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"
              fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input id="searchInput" type="search" placeholder="Search movies, genres…" aria-label="Search movies" />
    </div>

    <div>
      <?php if ($isAuthed): ?>
        <span style="margin-right:8px; color: var(--muted); font-weight:700;">
          Hi, <?= htmlspecialchars($userName) ?>
        </span>
        <a href="<?= url('pages/bookings.php') ?>" class="btn ghost" aria-label="View my bookings">My Bookings</a>
        <form action="<?= url('auth/logout.php') ?>" method="post" style="display:inline">
          <button class="btn" type="submit" aria-label="Log out">Logout</button>
        </form>
      <?php else: ?>
        <a class="btn" href="<?= url('pages/login.php') ?>" aria-label="Open login">Login</a>
      <?php endif; ?>
    </div>
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
