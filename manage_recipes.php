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

  if (
    empty($_POST) &&
    empty($_FILES) &&
    ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0
  ) {
    $errors[] = 'The uploaded file is too large.';
    
  } else {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $steps = trim($_POST['steps'] ?? '');
  } 

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

  if (
    !isset($_FILES['image']) ||
    $_FILES['image']['error'] !== UPLOAD_ERR_OK
  ) {
    $errors[] = 'A valid recipe image is required.';
  }

  $tmp_name = '';
  $mime_type = '';

  $allowed_types = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
  ];

  if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] === UPLOAD_ERR_OK
  ) {
    $tmp_name = $_FILES['image']['tmp_name'];

    $max_file_size = 5 * 1024 * 1024;

    if ($_FILES['image']['size'] > $max_file_size) {
      $errors[] = 'Image must be 5 MB or smaller.';
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE); // this is the part that actually checks if it's the right file type! 
    $mime_type = $finfo->file($tmp_name);

    if (!isset($allowed_types[$mime_type])) { // using the last thing, if this is false, this is where the image gets rejected.
      $errors[] = 'Image must be a JPEG or PNG file.';
    }

    if (getimagesize($tmp_name) === false) { // this is just a more general check less related to sanitizing malicious stuff and more to make sure the code can actually interpret it as an image. 
      $errors[] = 'Uploaded file is not a valid image.';
    }
  }
  

  if (empty($errors)) {
    $extension = $allowed_types[$mime_type];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;

    $upload_directory = __DIR__ . '/assets/uploads/';
    $destination = $upload_directory . $filename;

    if (move_uploaded_file($tmp_name, $destination)) {
      $image_path = 'assets/uploads/' . $filename;

      mysqli_execute_query(
        $conn,
        'INSERT INTO recipes
         (title, description, ingredients, steps, image)
         VALUES (?, ?, ?, ?, ?)',
        [$title, $description, $ingredients, $steps, $image_path]
      );

      header('Location: manage_recipes.php');
      exit;
    }

    $errors[] = 'Image could not be saved.';
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

  <form
    action="manage_recipes.php"
    method="POST"
    enctype="multipart/form-data" <?php //Without this line, the file won't successfully arrive in $_Files ?>
  >
    

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

    <label for="image">Recipe Image</label>
    <input
      type="file"
      id="image"
      name="image"
      accept=".jpg,.jpeg,.png,image/jpeg,image/png"
      required
    >    

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