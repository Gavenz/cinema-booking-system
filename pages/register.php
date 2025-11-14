<?php
/**
 * register.php
 *
 * User registration page.
 *
 * Responsibilities:
 * - Displays a registration form (name, email, username, password, etc.).
 * - Validates input and checks CSRF token on POST.
 * - Ensures uniqueness of username/email and validates password rules.
 * - Hashes the password securely before inserting into the users table.
 * - Logs the user in automatically or redirects them to the login page.
 *
 * Supports Functional Requirement F8 (Register Page).
 */

ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/flash.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$errors = [];
$next   = $_GET['next'] ?? $_POST['next'] ?? url('pages/showtimes.php');
// --- Handle POST: CSRF check and basic form validation ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  if (function_exists('csrf_check')) { csrf_check(); }

  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $pw       = $_POST['password'] ?? '';
  $pw2      = $_POST['password_confirm'] ?? '';

  // --- Basic validation ---
  if ($username === '' || $email === '' || $pw === '' || $pw2 === '') {
    $errors[] = 'Please fill in all fields.';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
  }
  if ($pw !== $pw2) {
    $errors[] = 'Passwords do not match.';
  }
  // Example minimum policy: at least 8 chars
  if (strlen($pw) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
  }
  // Username quick sanity (letters, numbers, underscore, dash)
  if (!preg_match('/^[A-Za-z0-9_\-]{3,32}$/', $username)) {
    $errors[] = 'Username must be 3–32 chars (letters, numbers, _ or -).';
  }

// --- Check for duplicate email/username in the database ---
  if (!$errors) {
    $st = $pdo->prepare("SELECT 1 FROM users WHERE email = ? OR username = ? LIMIT 1");
    $st->execute([$email, $username]);
    if ($st->fetchColumn()) {
      $errors[] = 'Email or username is already taken.';
    }
  }

  // --- Create user ---
  if (!$errors) {
    // --- Insert new user with hashed password into users table ---
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $role = 'user';

    $ins = $pdo->prepare("
      INSERT INTO users (username, email, password_hash, role, created_at)
      VALUES (:username, :email, :hash, :role, :created_at)
    ");
    $ok = $ins->execute([
      ':username'   => $username,
      ':email'      => $email,
      ':hash'       => $hash,
      ':role'       => $role,
      ':created_at' => date('Y-m-d H:i:s'),
    ]);

    if ($ok) {
      // Auto-login (optional but friendly)
      session_regenerate_id(true);
      $_SESSION['user'] = [
        'id'       => (int)$pdo->lastInsertId(),
        'username' => $username,
        'email'    => $email,
        'role'     => $role,
      ];

      flash_success('Account created! Welcome, '.$username.' 👋');
      // Safety: only allow local redirects
      header('Location: ' . url('pages/showtimes.php'));
      exit;
      // If you prefer to honor $next only when it's a local path:
      // if (preg_match('#^/#', parse_url($next, PHP_URL_PATH) ?? '')) {
      //   header('Location: ' . $next);
      // } else {
      //   header('Location: ' . url('pages/showtimes.php'));
      // }
      // exit;
    } else {
      $errors[] = 'Something went wrong. Please try again.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="stylesheet" href="<?= url('assets/styles.css') ?>">
  <meta charset="UTF-8" />
  <title>Register — Big Premiere Point</title>
  <base href="<?= rtrim(BASE_URL, '/') ?>/" />
  <style>
    :root{
      --bg:#0b0b0f;--panel:#12121a;--muted:#8b8ba1;--text:#f3f3f8;
      --accent:#e50914;--accent-2:#f5c518;--ring:0 0 0 2px rgba(229,9,20,.45);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;
      color:var(--text);
      background:radial-gradient(1200px 600px at 20% -10%,#1b1b28 0%,transparent 60%),
                 radial-gradient(800px 500px at 100% 0%,#231c24 0%,transparent 60%),
                 var(--bg);
      overflow-x:hidden;
    }
    .login-page{
      max-width:720px;margin:32px auto;
      background:rgba(255,255,255,.04);
      border:1px solid rgba(255,255,255,.12);
      border-radius:14px;padding:18px 16px;
    }
    .btn{
      appearance:none;border:0;background:var(--accent);color:#fff;font-weight:700;
      padding:10px 14px;border-radius:999px;cursor:pointer;box-shadow:0 10px 20px rgba(229,9,20,.25)
    }
    .btn:hover{filter:brightness(1.05)}
    .input{
      width:100%;padding:10px 12px;border-radius:10px;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:var(--text);outline:none
    }
    .input:focus{box-shadow:var(--ring);border-color:rgba(255,255,255,.35)}
    .muted{color:var(--muted)}
    a{color:var(--accent);text-decoration:none;font-weight:700}
  </style>
</head>
<body class="page-register">
  <?php include __DIR__ ."/../includes/header.php"; ?>

  <main class="section">
    <div class="login-page">
      <h2>Create your account</h2>

      <?php if (!empty($errors)): ?>
        <div class="flash error">
          <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <div class="card auth-card">
        <form method="post" novalidate>
          <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
          <?php echo csrf_field(); ?>
          <div class="form-row" style="display:grid;gap:10px">
            <label>
              <span class="muted" style="display:block;margin-bottom:6px">Username</span>
              <input class="input" name="username" required
                     value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </label>
            <label>
              <span class="muted" style="display:block;margin-bottom:6px">Email</span>
              <input class="input" type="email" name="email" required autocomplete="email"
                     value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </label>
            <label>
              <span class="muted" style="display:block;margin-bottom:6px">Password</span>
              <input class="input" type="password" name="password" required autocomplete="new-password">
            </label>
            <label>
              <span class="muted" style="display:block;margin-bottom:6px">Confirm Password</span>
              <input class="input" type="password" name="password_confirm" required autocomplete="new-password">
            </label>

            <button class="btn" type="submit" style="width:fit-content">Create account</button>
          </div>
        </form>
      </div>

      <div style="margin-top:12px">
        <span class="muted">Already have an account?</span>
        <a href="<?= url('pages/login.php') ?>?next=<?= urlencode($next) ?>">Login</a>
      </div>
    </div>
  </main>

  <?php include __DIR__ ."/../includes/footer.php"; ?>
</body>
</html>
