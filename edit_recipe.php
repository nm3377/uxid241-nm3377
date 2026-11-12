<?php

declare(strict_types=1);

require 'admin_guard.php';

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$id = (int) ($_GET['id'] ?? 0);

$result = mysqli_execute_query(
  $conn,
  'SELECT id, title, description, ingredients, steps
   FROM recipes
   WHERE id = ?',
  [$id]
);

$recipe = mysqli_fetch_assoc($result);

if (!$recipe) {
  http_response_code(404);
  exit('Recipe not found.');
}

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
      'UPDATE recipes
       SET title = ?,
           description = ?,
           ingredients = ?,
           steps = ?
       WHERE id = ?',
      [$title, $description, $ingredients, $steps, $id]
    );

    header('Location: manage_recipes.php');
    exit;
  }

  $recipe['title'] = $title;
  $recipe['description'] = $description;
  $recipe['ingredients'] = $ingredients;
  $recipe['steps'] = $steps;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Recipe | UXID 241 Cookbook</title>
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

    <h2>Edit Recipe</h2>

    <?php foreach ($errors as $error): ?>
      <p><?= e($error); ?></p>
    <?php endforeach; ?>

    <form
      action="edit_recipe.php?id=<?= (int) $recipe['id']; ?>"
      method="POST"
    >

      <label for="title">Title</label>
      <input
        type="text"
        id="title"
        name="title"
        value="<?= e($recipe['title']); ?>"
        required
      >

      <label for="description">Description</label>
      <textarea
        id="description"
        name="description"
        required
      ><?= e($recipe['description']); ?></textarea>

      <label for="ingredients">Ingredients</label>
      <textarea
        id="ingredients"
        name="ingredients"
        required
      ><?= e($recipe['ingredients']); ?></textarea>

      <label for="steps">Steps</label>
      <p>Separate each step with an asterisk (*).</p>
      <textarea
        id="steps"
        name="steps"
        required
      ><?= e($recipe['steps']); ?></textarea>

      <button type="submit">Save Changes</button>

    </form>

  </main>

</body>
</html>