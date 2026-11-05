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
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($username === '') {
    $error = 'Username is required.';
  } elseif ($password === '') {
    $error = 'Password is required.';
  } elseif ($password !== $confirm_password) {
    $error = 'Passwords do not match.';
  } else {
    $result = mysqli_execute_query(
      $conn,
      'SELECT id FROM users WHERE username = ?',
      [$username]
    );

    $existing_user = mysqli_fetch_assoc($result);

    if ($existing_user) {
      $error = 'That username is already taken.';
    } else {
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      mysqli_execute_query(
        $conn,
        'INSERT INTO users (username, password_hash, is_admin)
         VALUES (?, ?, ?)',
        [$username, $password_hash, 0]
      );

      header('Location: login.php');
      exit;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | UXID 241 Cookbook</title>
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
    <h1>Create Account</h1>

    <?php if ($error !== ''): ?>
      <p><?= e($error); ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">

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

      <label for="confirm_password">Confirm Password</label>
      <input
        type="password"
        id="confirm_password"
        name="confirm_password"
        required
      >

      <button type="submit">Create Account</button>

    </form>

    <p>
      Already have an account?
      <a href="login.php">Log in</a>
    </p>
  </main>

</body>
</html>