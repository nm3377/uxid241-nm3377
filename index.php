<?php

declare(strict_types=1);

session_start();

require 'database.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$search = trim($_GET['q'] ?? '');

if ($search === '') {
  $result = mysqli_query(
    $conn,
    'SELECT id, title, description, ingredients, steps FROM recipes'
  );
} else {
  $search_term = '%' . $search . '%';

  $result = mysqli_execute_query(
    $conn,
    'SELECT id, title, description, ingredients, steps
     FROM recipes
     WHERE title LIKE ?
        OR description LIKE ?
        OR ingredients LIKE ?
        OR steps LIKE ?',
    [$search_term, $search_term, $search_term, $search_term]
  );
}

$recipes = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head> 
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UXID 241 Cookbook</title>
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

  <?php if ($_SESSION['is_admin'] ?? false): ?>
    <p class="admin_label">ADMIN</p>
  <?php endif; ?>

  <main>

    <form class="search_form" action="index.php" method="GET">
      <label for="q">Search:</label>

      <input
        type="search"
        id="q"
        name="q"
        value="<?= e($search); ?>"
        required
      >

      <button type="submit">Search</button>
    </form>

    <h2 class="recipes_heading">Available Recipes:</h2>

    <?php if (empty($recipes)): ?>

      <p class="no_results">
        No recipes found for "<?= e($search); ?>".
      </p>

    <?php else: ?>

      <div class="recipe_grid">

        <?php foreach ($recipes as $recipe): ?>

          <article class="recipe_card">

            <img
              class="recipe_image"
              src="placeholder.jpg"
              alt=""
            >

            <div class="recipe_content">
              <h3><?= e($recipe['title']); ?></h3>
              <p><?= e($recipe['description']); ?></p>
            </div>

          </article>

        <?php endforeach; ?>

      </div>

    <?php endif; ?>

  </main>

</body>
</html>