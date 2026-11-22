<?php

declare(strict_types=1);

require 'admin_guard.php';

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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $ingredients = trim($_POST['ingredients'] ?? '');
  $steps = trim($_POST['steps'] ?? '');
  $image_path = $recipe['image'];

  $new_image_uploaded =
  isset($_FILES['image']) &&
  $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

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

  if ($new_image_uploaded) {

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      $errors[] = 'The image could not be uploaded.';
    } else {
      $tmp_name = $_FILES['image']['tmp_name'];

      $max_file_size = 5 * 1024 * 1024;

      if ($_FILES['image']['size'] > $max_file_size) {
        $errors[] = 'Image must be 5 MB or smaller.';
      }

      $finfo = new finfo(FILEINFO_MIME_TYPE);
      $mime_type = $finfo->file($tmp_name);

      $allowed_types = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
      ];

      if (!isset($allowed_types[$mime_type])) {
        $errors[] = 'Image must be a JPEG or PNG file.';
      }

      if (getimagesize($tmp_name) === false) {
        $errors[] = 'Uploaded file is not a valid image.';
      }
    }
  }

  if (empty($errors) && $new_image_uploaded) {
    $extension = $allowed_types[$mime_type];
    $filename = bin2hex(random_bytes(16)) . '.' . $extension;

    $upload_directory = __DIR__ . '/assets/uploads/';
    $destination = $upload_directory . $filename;

    if (move_uploaded_file($tmp_name, $destination)) {
      $image_path = 'assets/uploads/' . $filename;
    } else {
      $errors[] = 'Image could not be saved.';
    }
  }

  if (empty($errors)) {
    mysqli_execute_query(
      $conn,
      'UPDATE recipes
      SET title = ?,
          description = ?,
          ingredients = ?,
          steps = ?,
          image = ?
      WHERE id = ?',
      [
        $title,
        $description,
        $ingredients,
        $steps,
        $image_path,
        $id
      ]
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
      enctype="multipart/form-data"
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

      <label>Current Image</label>

      <img
        class="edit_recipe_image"
        src="<?= e($recipe['image']); ?>"
        alt="<?= e($recipe['title']); ?>"
      >

      <label for="image">Replace Image</label>

      <input
        type="file"
        id="image"
        name="image"
        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
      >      

      <button type="submit">Save Changes</button>

    </form>

  </main>

</body>
</html>