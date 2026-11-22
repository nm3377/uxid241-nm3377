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

$id = (int) ($_GET['id'] ?? 0);

$result = mysqli_execute_query(
  $conn,
  'SELECT id, title, description, ingredients, steps, image
   FROM recipes
   WHERE id = ?',
  [$id]
);

$recipe = mysqli_fetch_assoc($result);

if (!$recipe) {
  http_response_code(404);
  exit('Recipe not found.');
}

$steps = explode('*', $recipe['steps']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($recipe['title']); ?> | UXID 241 Cookbook</title>
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

  <main class="recipe_page">

    <a href="index.php">← Back to recipes</a>

    <h2><?= e($recipe['title']); ?></h2>

    <img
      class="recipe_page_image"
      src="<?= e($recipe['image']); ?>"
      alt="<?= e($recipe['title']); ?>"
    >

    <p><?= e($recipe['description']); ?></p>

    <h3>Ingredients</h3>

    <p><?= nl2br(e($recipe['ingredients'])); ?></p>

    <h3>Steps</h3>

    <ol>
      <?php foreach ($steps as $step): ?>
        <li><?= e(trim($step)); ?></li>
      <?php endforeach; ?>
    </ol>

  </main>

</body>
</html>