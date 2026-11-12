<?php

declare(strict_types=1);

session_start();

require 'database.php';

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