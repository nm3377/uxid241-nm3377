<?php

declare(strict_types=1);

session_start();

require 'database.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $result = mysqli_execute_query(
    $conn,
    'SELECT id, username, password_hash, is_admin
     FROM users
     WHERE username = ?',
    [$username]
  );

  $user = mysqli_fetch_assoc($result);

  if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = (bool) $user['is_admin'];

    header('Location: index.php');
    exit;
  }

  $error = 'Invalid username or password.';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | UXID 241 Cookbook</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

  <header class="site_header">
    <div class="header_content">

      <div>
        <h1 class="site_title">UXID 241 COOKBOOK</h1>
        <p class="project_by">Project by: Nick Murray</p>
      </div>

      <nav class="site_nav">
        <a href="index.php">Home</a>

        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </nav>

    </div>
  </header>

  <main>
    <h1>Login</h1>

    <?php if ($error !== ''): ?>
      <p><?= e($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">

      <label for="username">Username</label>
      <input
        type="text"
        id="username"
        name="username"
        value="<?= e($username); ?>"
        required
      >

      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        required
      >

      <button type="submit">Log In</button>

    </form>
  </main>

</body>
</html>