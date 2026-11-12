<?php

declare(strict_types=1);

require 'admin_guard.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$result = mysqli_query(
  $conn,
  'SELECT id, title, description, ingredients, steps, image FROM recipes'
);

$recipes = mysqli_fetch_all($result, MYSQLI_ASSOC);

$title = '';
$description = '';
$ingredients = '';
$steps = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $ingredients = trim($_POST['ingredients'] ?? '');
  $steps = trim($_POST['steps'] ?? '');

  if ($title === '') {
    $errors[] = 'Title is required.';
  }

  if ($description === '') {
    $errors[] = 'Description is required.';
  }

  if ($ingredients === '') {
    $errors[] = 'Ingredients are required.';
  }

  if ($steps === '') {
    $errors[] = 'Steps are required.';
  }

  if (empty($errors)) {
    mysqli_execute_query(
      $conn,
      'INSERT INTO recipes (title, description, ingredients, steps)
       VALUES (?, ?, ?, ?)',
      [$title, $description, $ingredients, $steps]
    );

    header('Location: manage_recipes.php');
    exit;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Recipes | UXID 241 Cookbook</title>
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
      <a href="manage_recipes.php">Manage Recipes</a>
      <a href="logout.php">Logout</a>
    </nav>

  </div>
</header>

<p class="admin_label">ADMIN</p>
<main>

  <h2>Create Recipe</h2>

  <?php foreach ($errors as $error): ?>
    <p><?= e($error); ?></p>
  <?php endforeach; ?>

  <form action="manage_recipes.php" method="POST">

    <label for="title">Title</label>
    <input
      type="text"
      id="title"
      name="title"
      value="<?= e($title); ?>"
      required
    >

    <label for="description">Description</label>
    <textarea
      id="description"
      name="description"
      required
    ><?= e($description); ?></textarea>

    <label for="ingredients">Ingredients</label>
    <textarea
      id="ingredients"
      name="ingredients"
      required
    ><?= e($ingredients); ?></textarea>

    <label for="steps">Steps</label>
    <textarea
      id="steps"
      name="steps"
      required
    ><?= e($steps); ?></textarea>

    <button type="submit">Create Recipe</button>

  </form>
<p>FORMATTING NOTE: Separate each step with an asterisk (*).</p>

<h2>Manage Existing Recipes</h2>

<div class="recipe_grid">

  <?php foreach ($recipes as $recipe): ?>

    <article class="recipe_card">

      <img
        class="recipe_image"
        src="<?= e($recipe['image']); ?>"
        alt="<?= e($recipe['title']); ?>"
      >      

      <div class="recipe_content">

        <h3><?= e($recipe['title']); ?></h3>
        <p><?= e($recipe['description']); ?></p>

        <div class="recipe_actions">

          <a href="edit_recipe.php?id=<?= (int) $recipe['id']; ?>">
            Edit
          </a>

          <form
            action="delete_recipe.php"
            method="POST"
            onsubmit="return confirm('Are you sure you want to delete this recipe?');"
          >
            <input
              type="hidden"
              name="id"
              value="<?= (int) $recipe['id']; ?>"
            >

            <button type="submit">Delete</button>
          </form>

        </div>

      </div>

    </article>

  <?php endforeach; ?>

</div>

</main>

</body>
</html>