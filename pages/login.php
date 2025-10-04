<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = trim($_POST['id'] ?? '');   // email or username
  $pw = $_POST['password'] ?? '';
  if ($id === '' || $pw === '') $errors[] = 'Please fill in both fields.';

  if (!$errors) {
    $stmt = $pdo->prepare("SELECT id, username, role, password_hash FROM users WHERE email=:id OR username=:id LIMIT 1");
    $stmt->execute([':id'=>$id]);
    if ($u = $stmt->fetch()) {
      if (password_verify($pw, $u['password_hash'])) {
        $_SESSION['user_id'] = (int)$u['id'];
        $_SESSION['username'] = $u['username'];
        $_SESSION['role'] = $u['role'];
        header('Location: /cinema-booking-system/pages/showtimes.php');
        exit;
      }
    }
    $errors[] = 'Invalid credentials.';
  }
}
?>
<h2>Login</h2>
<div class="card">
  <?php if ($errors): ?>
    <ul style="color:#ff8a8a;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
  <?php endif; ?>
  <form method="post">
    <div class="form-row"><label>Email or Username</label><input class="input" name="id" required></div>
    <div class="form-row"><label>Password</label><input class="input" type="password" name="password" required></div>
    <button class="button" type="submit">Login</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
