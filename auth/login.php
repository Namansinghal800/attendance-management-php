<?php
require_once __DIR__ . '/../db.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } else {
        $pdo = get_db();
        $stmt = $pdo->prepare('SELECT id, password FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: ' . BASE_URL . '/public/dashboard.php');
            exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Attendance App</title>
<link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<div class="container small">
  <h1>Login</h1>
  <?php if ($error): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
  <form method="post" class="card">
      <label>Username
          <input type="text" name="username" required>
      </label>
      <label>Password
          <input type="password" name="password" required>
      </label>
      <button type="submit">Login</button>
  </form>
  <p class="muted">Haven't set it up yet? Run <a href="<?php echo BASE_URL; ?>/setup.php">Setup</a>.</p>
</div>
</body>
</html>
