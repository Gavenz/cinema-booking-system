<?php
/**
 * admin_showtimes.php
 *
 * Admin interface for managing showtimes.
 *
 * Responsibilities:
 * - Restricts access to admin users only.
 * - Lists existing showtimes with movie, hall and timing information.
 * - Handles form submissions to create/update/delete showtimes.
 *
 * Supports Functional Requirement F18 (Admin Manage Showtimes Page).
 */

require_once __DIR__ . '/../includes/init.php';
require_admin();

$action   = $_GET['action'] ?? 'list';
$movie_id = isset($_GET['movie']) ? (int)$_GET['movie'] : null;

function movie_options(PDO $db): array {
  return $db->query("SELECT id, title FROM movies ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
}
function hall_options(PDO $db): array {
  return $db->query("SELECT id, name FROM halls ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
}
function showtime_by_id(PDO $db, int $id): ?array {
  $st = $db->prepare("SELECT * FROM showtimes WHERE id=? LIMIT 1");
  $st->execute([$id]);
  $row = $st->fetch(PDO::FETCH_ASSOC);
  return $row ?: null;
}

function handle_create(PDO $db) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $movie_id = (int)($_POST['movies_id'] ?? 0);
  $hall_id  = (int)($_POST['hall_id'] ?? 0);
  $starts   = trim($_POST['starts_at'] ?? '');

  $errs = [];
  if ($movie_id <= 0) $errs[] = 'Movie is required';
  if ($hall_id <= 0) $errs[] = 'Hall is required';
  if ($starts === '') $errs[] = 'Start time is required (YYYY-MM-DD HH:MM:SS)';

  if ($errs) { foreach ($errs as $e) { flash_now('error', $e); } return; }

  $st = $db->prepare("INSERT INTO showtimes (movies_id, hall_id, starts_at) VALUES (?,?,?)");
  $st->execute([$movie_id, $hall_id, $starts]);

  flash_success('Showtime created.');
  $redir = url('pages/admin_showtimes.php?action=list' . ($movie_id ? '&movie='.$movie_id : ''));
  header('Location: ' . $redir);
  exit;
}

function handle_update(PDO $db) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $id       = (int)($_POST['id'] ?? 0);
  $movie_id = (int)($_POST['movies_id'] ?? 0);
  $hall_id  = (int)($_POST['hall_id'] ?? 0);
  $starts   = trim($_POST['starts_at'] ?? '');

  $errs = [];
  if ($id <= 0) $errs[] = 'Invalid id';
  if ($movie_id <= 0) $errs[] = 'Movie is required';
  if ($hall_id <= 0) $errs[] = 'Hall is required';
  if ($starts === '') $errs[] = 'Start time is required';

  if ($id <= 0) { flash_now('error', 'Invalid id'); return; }

  $st = $db->prepare("UPDATE showtimes SET movies_id=?, hall_id=?, starts_at=? WHERE id=?");
  $st->execute([$movie_id, $hall_id, $starts, $id]);

  flash_success('Showtime updated.');
  $redir = url('pages/admin_showtimes.php?action=list' . ($movie_id ? '&movie='.$movie_id : ''));
  header('Location: ' . $redir);
  exit;
}

function handle_delete(PDO $db) {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
  csrf_check();

  $id       = (int)($_POST['id'] ?? 0);
  $movie_id = (int)($_POST['movie'] ?? 0);

  if ($id <= 0) { flash_now('error', 'Invalid id'); return; }

  // NOTE: FK from showtimes -> booking_items and booking is ON DELETE CASCADE (so related lines will be removed).
  $st = $db->prepare("DELETE FROM showtimes WHERE id=?");
  $st->execute([$id]);

  flash_success('Showtime deleted.');
  $redir = url('pages/admin_showtimes.php?action=list' . ($movie_id ? '&movie='.$movie_id : ''));
  header('Location: ' . $redir);
  exit;
}

// dispatch POST
if ($action === 'create') handle_create($pdo);
if ($action === 'edit')   handle_update($pdo);
if ($action === 'delete') handle_delete($pdo);

// data for views
$movies = movie_options($pdo);
$halls  = hall_options($pdo);

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin · Showtimes</title>
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>">
  <style>
    table{width:100%;border-collapse:collapse}th,td{padding:.5rem;border-bottom:1px solid #eee;text-align:left}
    .btn{display:inline-block;padding:.45rem .8rem;border-radius:8px;border:1px solid #ddd;text-decoration:none}
    .btn-danger{border-color:#fca5a5}.inline{display:inline}
    .field{margin:.6rem 0}.field label{display:block;margin-bottom:.25rem;font-weight:600}
    .card{border:1px solid #e5e7eb;border-radius:12px;padding:1rem;background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));box-shadow:0 1px 2px rgba(0,0,0,.04);max-width:720px}
  </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="container" style="max-width:1080px;margin:2rem auto">
  <h1>Showtimes (CRUD)</h1>
    <div class="admin-toolbar" style="margin: .5rem 0 1.25rem; display:flex; gap:.5rem; align-items:center;">
    <a class="btn ghost" href="<?= url('pages/admin.php') ?>">← Back to Dashboard</a>
    <?php
    // Optional breadcrumb:
    $here = basename(__FILE__) === 'admin_movies.php' ? 'Movies' : 'Showtimes';
    ?>
    <span style="opacity:.7; font-weight:700;">/ <?= htmlspecialchars($here) ?></span>
    </div>
  <?php include __DIR__ . '/../includes/flash.php'; ?>
  <?php flash_render();?>

  <?php if ($action === 'list'): ?>
    <form method="get" style="margin:0 0 1rem">
      <input type="hidden" name="action" value="list">
      <label>Filter by movie:
        <select name="movie" onchange="this.form.submit()">
          <option value="">All</option>
          <?php foreach ($movies as $m): ?>
            <option value="<?= (int)$m['id'] ?>" <?= ($movie_id===$m['id']?'selected':'') ?>>
              <?= htmlspecialchars($m['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <a class="btn" href="<?= url('pages/admin_showtimes.php?action=create' . ($movie_id ? '&movie='.$movie_id : '')) ?>">+ New Showtime</a>
    </form>

    <?php
      $sql = "
        SELECT s.id, s.starts_at, m.title AS movie_title, m.id AS movie_id, h.name AS hall_name, h.id AS hall_id
        FROM showtimes s
        JOIN movies m ON m.id = s.movies_id
        JOIN halls  h ON h.id = s.hall_id
      ";
      $args = [];
      if ($movie_id) { $sql .= " WHERE s.movies_id = ? "; $args[] = $movie_id; }
      $sql .= " ORDER BY s.starts_at DESC";
      $st = $pdo->prepare($sql);
      $st->execute($args);
      $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <table>
      <thead><tr><th>ID</th><th>Movie</th><th>Hall</th><th>Starts At</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['movie_title']) ?></td>
            <td><?= htmlspecialchars($r['hall_name']) ?></td>
            <td><?= htmlspecialchars($r['starts_at']) ?></td>
            <td>
              <a class="btn" href="<?= url('pages/admin_showtimes.php?action=edit&id='.(int)$r['id']) ?>">Edit</a>
              <form class="inline" method="post" action="<?= url('pages/admin_showtimes.php?action=delete') ?>"
                    onsubmit="return confirm('Delete this showtime? (Bookings/items will cascade)');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="hidden" name="movie" value="<?= (int)($movie_id ?? 0) ?>">
                <button class="btn btn-danger" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php elseif ($action === 'create'): ?>
    <div class="card">
      <h2>Create Showtime</h2>
      <form method="post">
        <?= csrf_field() ?>
        <div class="field">
          <label>Movie</label>
          <select name="movies_id" required>
            <?php foreach ($movies as $m): ?>
              <option value="<?= (int)$m['id'] ?>" <?= ($movie_id===$m['id']?'selected':'') ?>>
                <?= htmlspecialchars($m['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label>Hall</label>
          <select name="hall_id" required>
            <?php foreach ($halls as $h): ?>
              <option value="<?= (int)$h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
            <label>Starts At</label>
            <input
                type="datetime-local"
                name="starts_at"
                required
                step="900"                 <!-- 15-min steps -->
                min="<?= date('Y-m-d\TH:i') ?>"   <!-- no past times -->
                value="<?= isset($prefill) ? htmlspecialchars(date('Y-m-d\TH:i', strtotime($prefill))) : '' ?>"
            >
            <small>15-min steps, local time.</small>
        </div>
        <button class="btn" type="submit">Create</button>
        <a class="btn" href="<?= url('pages/admin_showtimes.php?action=list' . ($movie_id ? '&movie='.$movie_id : '')) ?>">Cancel</a>
      </form>
    </div>

  <?php elseif ($action === 'edit'):
      $id = (int)($_GET['id'] ?? 0);
      $stx = $id ? showtime_by_id($pdo, $id) : null;
      if (!$stx): ?>
        <p>Showtime not found. <a class="btn" href="<?= url('pages/admin_showtimes.php?action=list') ?>">Back</a></p>
      <?php else: ?>
        <div class="card">
          <h2>Edit Showtime #<?= (int)$stx['id'] ?></h2>
          <form method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$stx['id'] ?>">
            <div class="field">
              <label>Movie</label>
              <select name="movies_id" required>
                <?php foreach ($movies as $m): ?>
                  <option value="<?= (int)$m['id'] ?>" <?= ($stx['movies_id']==$m['id']?'selected':'') ?>>
                    <?= htmlspecialchars($m['title']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label>Hall</label>
              <select name="hall_id" required>
                <?php foreach ($halls as $h): ?>
                  <option value="<?= (int)$h['id'] ?>" <?= ($stx['hall_id']==$h['id']?'selected':'') ?>>
                    <?= htmlspecialchars($h['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label>Starts At</label>
              <input name="starts_at" value="<?= htmlspecialchars($stx['starts_at']) ?>" required>
            </div>
            <button class="btn" type="submit">Save</button>
            <a class="btn" href="<?= url('pages/admin_showtimes.php?action=list&movie='.(int)$stx['movies_id']) ?>">Cancel</a>
          </form>
        </div>
      <?php endif; ?>
  <?php endif; ?>
</main>
</body>
</html>
