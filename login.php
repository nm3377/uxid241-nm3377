<?php

declare(strict_types=1);


ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require 'database.php';

// Fallback for servers running PHP older than 8.2 (such as the school's digm
// server), where mysqli_execute_query() doesn't exist yet. It does the same
// job with older mysqli functions: prepare the SQL, bind each value to its ?,
// then run it. On PHP 8.2 and newer the real function exists, so this block
// is skipped.
if (!function_exists('mysqli_execute_query')) {
function mysqli_execute_query(mysqli $mysql, string $query, ?array $params = null): mysqli_result|bool {
// Keep the latest statement alive until the next query, so
// mysqli_affected_rows() and mysqli_insert_id() still report its result
// (closing it would reset them).
static $last_statement = null;
 

$statement = mysqli_prepare($mysql, $query);
$last_statement = $statement;
 

if ($params !== null && $params !== []) {
$types = '';
foreach ($params as $value) {
$types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
}
$values = array_values($params);
mysqli_stmt_bind_param($statement, $types, ...$values);
}
 

mysqli_stmt_execute($statement);
 

// INSERT, UPDATE and DELETE return no rows: report success with true,
// like the real function, without asking for a result.
if (mysqli_stmt_result_metadata($statement) === false) {
return true;
}
 

return mysqli_stmt_get_result($statement);
}
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif ($password === '') {
    $error = 'Password is required.';
  } else {
    $result = mysqli_execute_query(
      $conn,
      'SELECT id, email, password_hash, is_admin
       FROM users
       WHERE email = ?',
      [$email]
    );

    $user = mysqli_fetch_assoc($result);

      if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];

    header('Location: index.php');
    exit;
  }

    $error = 'Invalid email or password.';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UXID 241 Cookbook</title>
    <link rel="stylesheet" href="assets/styles/style.css">
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

        <label for="email">Email</label>
        <input
          type="text"
          id="email"
          name="email"
          value="<?= e($email); ?>"
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