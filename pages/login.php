<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/flash.php';

$errors = [];
$next   = $_GET['next'] ?? $_POST['next'] ?? url('pages/showtimes.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = trim($_POST['id'] ?? '');   // email or username
  $pw = $_POST['password'] ?? '';

  if ($id === '' || $pw === '') {
    $errors[] = 'Please fill in both fields.';
  } else {
    $stmt = $pdo->prepare(
      "SELECT id, username, email, role, password_hash
         FROM users
        WHERE email = :id OR username = :id
        LIMIT 1"
    );
    $stmt->execute([':id' => $id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($u && password_verify($pw, $u['password_hash'])) {
      if (session_status() !== PHP_SESSION_ACTIVE) session_start();
      session_regenerate_id(true);

      $_SESSION['user'] = [
        'id'       => (int)$u['id'],
        'username' => $u['username'],
        'email'    => $u['email'] ?? null,
        'role'     => $u['role'] ?? 'user',
      ];
      
      if ($ok)
      {
        flash_success('Welcome back, '.$u['username'].'!');
      }
      // Prevent open redirect: ensure we only redirect to a local path
      if (($_SESSION['user']['role'] ?? 'user') === 'admin') {
        header('Location: ' . url('pages/admin.php'));
      } else {
        header('Location: ' . url('pages/showtimes.php'));
      exit;
      } 
    } else {
      flash_now('error', 'Invalid email/username or password.');
      }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login — Big Premiere Point</title>
  <!-- Keeps all relative links like assets/images/... working from any page -->
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

    /* ===== Top Nav (header) ===== */
    .nav{
      position:sticky;top:0;z-index:50;backdrop-filter:saturate(1.1) blur(6px);
      background:linear-gradient(180deg,rgba(10,10,14,.85),rgba(10,10,14,.55) 40%,transparent);
      border-bottom:1px solid rgba(255,255,255,.06);
    }
    .nav-inner{max-width:1300px;margin:0 auto;display:flex;gap:16px;align-items:center;padding:12px 20px}
    .brand{display:flex;align-items:center;gap:10px;text-decoration:none}
    .logo{
      width:34px;height:34px;border-radius:8px;
      background:conic-gradient(from 200deg at 70% 40%,#ff4d4d,#ff1f5a 40%,#a20025 60%,#5e0015 80%,#ff4d4d);
      box-shadow:inset 0 0 12px rgba(0,0,0,.35),0 4px 16px rgba(229,9,20,.35)
    }
    .brand-title{font-weight:800;letter-spacing:.4px;font-size:1.1rem;color:#fff}
    .nav-links{display:flex;gap:14px;align-items:center;flex:1}
    .nav-links a,.more-trigger{
      color:var(--muted);text-decoration:none;font-weight:700;font-size:.95rem;
      padding:6px 10px;border-radius:10px
    }
    .nav-links a.active,.nav-links a:hover,.more-trigger:hover,
    .has-dropdown:focus-within .more-trigger{color:#fff;background:rgba(255,255,255,.06)}
    .search-wrap{
      display:flex;gap:10px;align-items:center;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.09);
      border-radius:999px;padding:6px 10px;width:clamp(200px,35vw,380px)
    }
    .search-wrap input{background:transparent;border:0;outline:none;color:var(--text);flex:1;padding:6px 6px;font-size:.95rem}
    .icon{opacity:.8}
    .btn{
      appearance:none;border:0;background:var(--accent);color:#fff;font-weight:700;
      padding:10px 14px;border-radius:999px;cursor:pointer;box-shadow:0 10px 20px rgba(229,9,20,.25)
    }
    .btn:hover{filter:brightness(1.05)}
    .btn.small{font-size:.9rem;padding:8px 10px}
    .btn.ghost{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15)}
    .input{
      width:100%;padding:10px 12px;border-radius:10px;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:var(--text);outline:none
    }
    .input:focus{box-shadow:var(--ring);border-color:rgba(255,255,255,.35)}
    .has-dropdown{position:relative;display:flex;align-items:center}
    .more-trigger{display:inline-flex;align-items:center;gap:6px;background:transparent;border:0;cursor:pointer}
    .dropdown{
      position:absolute;top:calc(100% + 8px);right:0;min-width:220px;z-index:60;
      background:#12121a;border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px;display:none
    }
    .dropdown a{display:block;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);font-weight:600;font-size:.95rem}
    .dropdown a:hover{background:rgba(255,255,255,.06)}
    .has-dropdown:hover .dropdown,.has-dropdown:focus-within .dropdown{display:block}
    footer{max-width:1300px;margin:14px auto 40px;padding:0 20px;color:var(--muted);display:flex;justify-content:space-between;align-items:center}
    .login-page{
      max-width:720px;margin:32px auto;
      background:rgba(255,255,255,.04);
      border:1px solid rgba(255,255,255,.12);
      border-radius:14px;padding:18px 16px;
    }
    @media (max-width:800px){.nav-links{display:none}}
  </style>
</head>
<body class="page-login">
  <?php require_once __DIR__ . '/../includes/header.php'; ?>

  <main class="section">
    <div class="login-page">
      <h2>Login</h2>

      <?php if (!empty($errors)): ?>
        <div class="flash error">
          <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <div class="card auth-card">
        <form method="post">
          <input type="hidden" name="next" value="<?= htmlspecialchars($next) ?>">
          <div class="form-row" style="display:grid;gap:10px">
            <label>
              <span style="display:block;color:var(--muted);margin-bottom:6px">Email or Username</span>
              <input class="input" name="id" required autocomplete="username"
                     value="<?= htmlspecialchars($_POST['id'] ?? '') ?>">
            </label>
            <label>
              <span style="display:block;color:var(--muted);margin-bottom:6px">Password</span>
              <input class="input" type="password" name="password" required autocomplete="current-password">
            </label>
            <button class="btn" type="submit" style="width:fit-content">Login</button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
