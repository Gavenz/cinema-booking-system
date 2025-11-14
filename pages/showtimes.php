<?php
/**
 * showtimes.php
 *
 * Showtimes listing page for selected movies.
 *
 * Responsibilities:
 * - Lists upcoming showtimes for movies (date, time, hall, location).
 * - Retrieves showtime data from the database using prepared statements.
 * - Allows users to click through to the booking/seat selection page.
 *
 * Supports Functional Requirement F9 (Showtime Page).
 */

require_once __DIR__ . '/../includes/init.php';

$activeNav = 'showtimes';

$tz   = new DateTimeZone($APP_TZ ?? 'Asia/Singapore');
$base = new DateTimeImmutable('today', $tz);

// Selected day (YYYY-MM-DD)
$day   = $_GET['day'] ?? $base->format('Y-m-d');
$label = (new DateTimeImmutable($day, $tz))->format('D, j M Y');

// --- Fetch showtimes (joined with movies and halls) from the database ---
$rows = db_showtimes_by_day($pdo, $day);

// Group by movie
$byMovie = [];
foreach ($rows as $r) {
  $mid = (int)$r['movie_id'];
  if (!isset($byMovie[$mid])) {
    $byMovie[$mid] = [
      'movie' => [
        'id'          => $mid,
        'title'       => $r['title'],
        'runtime_min' => (int)($r['runtime_min'] ?? 0),
        'age_rating'  => $r['age_rating'] ?? '',
      ],
      'times' => [],
    ];
  }
  $byMovie[$mid]['times'][] = [
    'id'        => (int)$r['showtime_id'],
    'starts_at' => $r['starts_at'],
  ];
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Showtimes — Big Premiere Point</title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>?v=1">
  <!-- Page-scoped styles for tabs + list (no posters) -->
  <style>
  /* Showtimes page-only styles */
  .wrap { max-width:1100px; margin:18px auto 40px; padding:0 20px; }

  .tabs {
    display:flex; gap:8px; justify-content:center; flex-wrap:wrap; margin:10px 0 16px;
  }
  .tab {
    padding:8px 14px; border-radius:999px; border:1px solid rgba(255,255,255,.16);
    background:rgba(255,255,255,.06); color:#f3f3f8; text-decoration:none; font-weight:800;
  }
  .tab:hover { filter:brightness(1.1); }
  .tab.active { background:var(--accent); border-color:var(--accent); }

  .note { text-align:center; color:#b9b9c9; font-weight:700; margin-bottom:18px; }

  .movie {
    background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
    border:1px solid rgba(255,255,255,.12);
    border-radius:14px;
    padding:14px 16px;
    margin-bottom:14px;
  }
  .movie-head { display:flex; gap:12px; align-items:baseline; flex-wrap:wrap; }
  .movie-title { font-size:1.05rem; font-weight:800; }

  .badge {
    display:inline-flex; align-items:center; gap:6px; padding:3px 8px; border-radius:999px;
    font-size:.8rem; font-weight:800; border:1px solid rgba(255,255,255,.18);
    background:rgba(255,255,255,.06); color:#f3f3f8;
  }
  .badge.rating { background:#2a2a07; color:var(--accent-2); border-color:rgba(245,197,24,.25); }

  .meta { color:#9ea0b5; font-size:.9rem; }

  .chips { margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; }
  .chip {
    display:inline-block; padding:8px 12px; border-radius:8px;
    background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.16);
    color:#fff; text-decoration:none; font-weight:700;
  }
  .chip:hover { filter:brightness(1.08); }

  .empty {
    margin:18px auto; text-align:center; color:#9ea0b5;
    border:1px dashed rgba(255,255,255,.18); border-radius:12px; padding:16px;
    max-width:680px;
  }
  </style>

</head>
<body>
  <?php require __DIR__ . '/../includes/header.php'; ?>

  <div class="wrap">
    <!-- Day Tabs (centered) -->
    <nav class="tabs" role="tablist" aria-label="Select day">
      <?php for ($i=0; $i<7; $i++):
        $d      = $base->modify("+$i day");
        $dval   = $d->format('Y-m-d');
        $active = $dval === $day;
        $text   = $i===0 ? 'Today' : ($i===1 ? 'Tomorrow' : $d->format('D'));
      ?>
        <a class="tab <?= $active ? 'active' : '' ?>"
           href="<?= url('pages/showtimes.php?day='.$dval) ?>"
           role="tab" aria-selected="<?= $active ? 'true' : 'false' ?>">
          <?= htmlspecialchars($text) ?>
        </a>
      <?php endfor; ?>
    </nav>

    <div class="note">Showtimes for <strong><?= htmlspecialchars($label) ?></strong></div>

    <?php if (!$byMovie): ?>
      <div class="empty">No sessions scheduled for this day.</div>
    <?php else: ?>
      <?php foreach ($byMovie as $group): ?>
        <section class="movie">
          <div class="movie-head">
            <div class="movie-title"><?= htmlspecialchars($group['movie']['title']) ?></div>
            <?php if (!empty($group['movie']['age_rating'])): ?>
              <span class="badge rating"><?= htmlspecialchars($group['movie']['age_rating']) ?></span>
            <?php endif; ?>
            <?php if (!empty($group['movie']['runtime_min'])): ?>
              <span class="badge"><?= (int)$group['movie']['runtime_min'] ?>m</span>
            <?php endif; ?>
            <span class="meta">• Choose a time</span>
          </div>
          <!-- Render showtime cards/rows with 'Book' links to booking.php -!>
          <div class="chips">
            <?php foreach ($group['times'] as $t): ?>
              <?php $timeLabel = local_time_label($t['starts_at'], 'g:i A'); ?>
              <a class="chip" href="<?= url('pages/booking.php?showtime_id='.$t['id']) ?>">
                <?= htmlspecialchars($timeLabel) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <?php require __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
