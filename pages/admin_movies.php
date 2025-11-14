<?php
/**
 * admin_movies.php
 *
 * Admin interface for managing movies.
 *
 * Responsibilities:
 * - Restricts access to admin users only.
 * - Lists existing movies with options to add, edit or delete records.
 * - Handles form submissions for creating/updating movie details.
 *
 * Supports Functional Requirement F17 (Admin Manage Movies Page).
 */

require_once __DIR__ . '/../includes/init.php';
// --- Guard: Allow access only to admin users ---
require_admin();

$action = $_GET['action'] ?? 'list';

$RATINGS = ['G','PG','PG-13','NC-16','M18','R21'];// --- Fetch all movies to display in management table ---

// --- Fetch all movies to display in management table ---
function movie_by_id(PDO $db, int $id): ?array {
  $st = $db->prepare("SELECT * FROM movies WHERE id=? LIMIT 1");
  $st->execute([$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}
// --- Handle POST for creating/updating/deleting movie records ---
function handle_create(PDO $db, array $RATINGS) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $title   = trim($_POST['title'] ?? '');
  $desc    = trim($_POST['description'] ?? '');
  $runtime = (int)($_POST['runtime_min'] ?? 0);
  $rating  = $_POST['rating'] ?? null;
  $poster  = trim($_POST['poster_url'] ?? '');

  $errs = [];
  if ($title === '') $errs[] = 'Title is required';
  if ($runtime <= 0) $errs[] = 'Runtime must be positive';
  if ($rating !== '' && $rating !== null && !in_array($rating, $RATINGS, true)) $errs[] = 'Invalid rating';

  if ($errs) { foreach ($errs as $e) { flash_now('error', $e); } return; }

  $st = $db->prepare("INSERT INTO movies (title, description, runtime_min, rating, poster_url) VALUES (?,?,?,?,?)");
  $st->execute([$title, $desc ?: null, $runtime ?: null, $rating ?: null, $poster ?: null]);

  flash_success('Movie created.');
  header('Location: ' . url('pages/admin_movies.php?action=list'));
  exit;
}

function handle_update(PDO $db, array $RATINGS) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $id      = (int)($_POST['id'] ?? 0);
  $title   = trim($_POST['title'] ?? '');
  $desc    = trim($_POST['description'] ?? '');
  $runtime = (int)($_POST['runtime_min'] ?? 0);
  $rating  = $_POST['rating'] ?? null;
  $poster  = trim($_POST['poster_url'] ?? '');

  $errs = [];
  if ($id <= 0) $errs[] = 'Invalid id';
  if ($title === '') $errs[] = 'Title is required';
  if ($runtime <= 0) $errs[] = 'Runtime must be positive';
  if ($rating !== '' && $rating !== null && !in_array($rating, $RATINGS, true)) $errs[] = 'Invalid rating';

  if ($errs) { foreach ($errs as $e) { flash_now('error', $e); } return; }

  $st = $db->prepare("UPDATE movies SET title=?, description=?, runtime_min=?, rating=?, poster_url=? WHERE id=?");
  $st->execute([$title, $desc ?: null, $runtime ?: null, $rating ?: null, $poster ?: null, $id]);

  flash_success('Movie updated.');
  header('Location: ' . url('pages/admin_movies.php?action=list'));
  exit;
}

function handle_delete(PDO $db) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $id = (int)($_POST['id'] ?? 0);
  if ($id <= 0) { flash_now('error', 'Invalid id'); return; }


  // NOTE: Your FK is ON DELETE CASCADE from movies -> showtimes -> booking_items/booking.
  $st = $db->prepare("DELETE FROM movies WHERE id=?");
  $st->execute([$id]);

  flash_success('Movie deleted (showtimes and dependent rows will have cascaded).');
  header('Location: ' . url('pages/admin_movies.php?action=list'));
  exit;
}

// dispatch POST
if ($action === 'create') handle_create($pdo, $RATINGS);
if ($action === 'edit')   handle_update($pdo, $RATINGS);
if ($action === 'delete') handle_delete($pdo);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin · Movies</title>
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>">
  <style>
    table{width:100%;border-collapse:collapse}th,td{padding:.5rem;border-bottom:1px solid #eee;text-align:left}
    .btn{display:inline-block;padding:.45rem .8rem;border-radius:8px;border:1px solid #ddd;text-decoration:none}
    .btn-danger{border-color:#fca5a5}
    .inline{display:inline}
    .field{margin:.6rem 0}
    .field label{display:block;margin-bottom:.25rem;font-weight:600}
    .card{border:1px solid #e5e7eb;border-radius:12px;padding:1rem;background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));box-shadow:0 1px 2px rgba(0,0,0,.04);max-width:720px}
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="container" style="max-width:1080px;margin:2rem auto">
  <h1>Movies (CRUD)</h1>
    <div class="admin-toolbar" style="margin: .5rem 0 1.25rem; display:flex; gap:.5rem; align-items:center;">
    <a class="btn ghost" href="<?= url('pages/admin.php') ?>">← Back to Dashboard</a>
    <?php
    // Optional breadcrumb:
    $here = basename(__FILE__) === 'admin_movies.php' ? 'Movies' : 'Showtimes';
    ?>
    <span style="opacity:.7; font-weight:700;">/ <?= htmlspecialchars($here) ?></span>
    </div>
  <?php include __DIR__ . '/../includes/flash.php'; ?>

  <?php if ($action === 'list'): ?>
    <p><a class="btn" href="<?= url('pages/admin_movies.php?action=create') ?>">+ New Movie</a></p>
    <?php
      // list with showtime count
      $st = $pdo->query("
        SELECT m.*, COUNT(s.id) AS showtimes_count
        FROM movies m
        LEFT JOIN showtimes s ON s.movies_id = m.id
        GROUP BY m.id
        ORDER BY m.id DESC
      ");
      $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Title</th><th>Rating</th><th>Runtime</th><th>Showtimes</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['rating'] ?? '-') ?></td>
            <td><?= (int)$r['runtime_min'] ?> min</td>
            <td>
              <?= (int)$r['showtimes_count'] ?>
              <a class="btn" href="<?= url('pages/admin_showtimes.php?action=list&movie='.(int)$r['id']) ?>">Manage</a>
            </td>
            <td>
              <a class="btn" href="<?= url('pages/admin_movies.php?action=edit&id='.(int)$r['id']) ?>">Edit</a>
              <form class="inline" method="post" action="<?= url('pages/admin_movies.php?action=delete') ?>"
                    onsubmit="return confirm('Delete this movie and all its showtimes?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php elseif ($action === 'create'): ?>
    <div class="card">
      <h2>Create Movie</h2>
      <form method="post">
        <?= csrf_field() ?>
        <div class="field"><label>Title</label><input name="title" required></div>
        <div class="field"><label>Description</label><textarea name="description" rows="4"></textarea></div>
        <div class="field"><label>Runtime (minutes)</label><input type="number" name="runtime_min" min="1" required></div>
        <div class="field">
          <label>Rating</label>
          <select name="rating">
            <option value="">—</option>
            <?php foreach ($RATINGS as $r): ?><option value="<?= $r ?>"><?= $r ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="field"><label>Poster URL</label><input name="poster_url" placeholder="assets/images/yourposter.jpg"></div>
        <button class="btn" type="submit">Create</button>
        <a class="btn" href="<?= url('pages/admin_movies.php?action=list') ?>">Cancel</a>
      </form>
    </div>

  <?php elseif ($action === 'edit'):
      $id = (int)($_GET['id'] ?? 0);
      $movie = $id ? movie_by_id($pdo, $id) : null;
      if (!$movie): ?>
        <p>Movie not found. <a class="btn" href="<?= url('pages/admin_movies.php?action=list') ?>">Back</a></p>
      <?php else: ?>
        <div class="card">
          <h2>Edit Movie #<?= (int)$movie['id'] ?></h2>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
            <div class="field"><label>Title</label><input name="title" value="<?= htmlspecialchars($movie['title']) ?>" required></div>
            <div class="field"><label>Description</label><textarea name="description" rows="4"><?= htmlspecialchars($movie['description'] ?? '') ?></textarea></div>
            <div class="field"><label>Runtime (minutes)</label><input type="number" name="runtime_min" min="1" value="<?= (int)$movie['runtime_min'] ?>" required></div>
            <div class="field">
              <label>Rating</label>
              <select name="rating">
                <option value="">—</option>
                <?php foreach ($RATINGS as $r): ?>
                  <option value="<?= $r ?>" <?= ($movie['rating'] === $r ? 'selected' : '') ?>><?= $r ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field"><label>Poster URL</label><input name="poster_url" value="<?= htmlspecialchars($movie['poster_url'] ?? '') ?>"></div>
            <button class="btn" type="submit">Save</button>
            <a class="btn" href="<?= url('pages/admin_movies.php?action=list') ?>">Cancel</a>
          </form>
        </div>
      <?php endif; ?>
  <?php endif; ?>
</main>
</body>
</html>
