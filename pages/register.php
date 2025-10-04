<?php
ini_set('display_errors',1); ini_set('display_startup_errors',1); error_reporting(E_ALL);
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!preg_match('/^[A-Za-z0-9_]{3,20}$/',$username)) $errors[]='Username must be 3–20 chars (letters/digits/underscore).';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))       $errors[]='Invalid email.';
  if (strlen($password) < 6)                            $errors[]='Password must be at least 6 chars.';

  if (!$errors) {
    // check duplicates
    $chk = $pdo->prepare("SELECT id FROM users WHERE username=:u OR email=:e LIMIT 1");
    $chk->execute([':u'=>$username, ':e'=>$email]);
    if ($chk->fetch()) {
      $errors[] = 'Username or email already exists.';
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $ins = $pdo->prepare("INSERT INTO users (username,email,password_hash,role) VALUES (:u,:e,:h,'user')");
      $ins->execute([':u'=>$username, ':e'=>$email, ':h'=>$hash]);
      $_SESSION['user_id'] = (int)$pdo->lastInsertId();
      $_SESSION['username'] = $username;
      $_SESSION['role'] = 'user';
      header('Location: /cinema-booking-system/pages/showtimes.php');
      exit;
    }
  }
}
?>
<h2>Create an Account</h2>
<div class="card">
  <?php if ($errors): ?>
    <ul style="color:#ff8a8a;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
  <?php endif; ?>
  <form method="post">
    <div class="form-row"><label>Username</label><input class="input" name="username" required></div>
    <div class="form-row"><label>Email</label><input class="input" type="email" name="email" required></div>
    <div class="form-row"><label>Password</label><input class="input" type="password" name="password" required></div>
    <button class="button" type="submit">Register</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
