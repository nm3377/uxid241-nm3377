<?php
declare(strict_types=1);

// Getting user input, trimming the fat, and sanitizing input (listed in order of appearance)

function value_trim(string $key): string {
  return trim($_GET[$key] ?? '');
}

function post_value(string $key): string {
  return trim($_POST[$key] ?? '');
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
} // Converts HTML characters into safe text, sanitizing the input. 

// To search for food in the database

$food_searched = value_trim('q'); 

$food_results = [];

$recipes = [
  'fried chicken',
  'chicken fried rice',
  'beef bulgogi',
  'old country steak',
  'mango sticky rice',
];

if ($food_searched !== '') { 
  // only searches for recipes if the result isn't empty and condition is true
  foreach ($recipes as $recipe) {
    if (str_contains(
      strtolower($recipe),
      strtolower($food_searched)
    )) {
      $food_results[] = $recipe;
    }
  }
}

$recipe_name = '';
$email = '';
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $recipe_name = post_value('name');
  $email = post_value('email');

  if ($recipe_name === '') {
    $errors[] = 'Recipe name is required';
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid e-mail address';
  }

  if (empty($errors)) {
    $success = true;
  }
}

?> 

<!DOCTYPE html> <!-- Boiler plate copied over from a previous coding class. -->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>UXID 241 Cookbook</title>
  </head>
  <body>
    <h1>UXID 241 Cookbook</h1>
    <h2> Recipes Search </h2>
    <form action="index.php" method="GET"> 
      <label for="q">Recipe name has:</label>
      <input
        type="search" 
        id="q"
        name="q"
        value="<?= e($food_searched); ?>"
      >
      <!-- tells the browser what kind of input field this is -->
      <!-- labels the HTML element as q , connects the input to the label used in PHP -->
      <!-- name=q sets users search to q aka search -->
      <!-- echo statement to have the url update and include the users search input -->
      <button type="submit">Search</button>
    </form>
  <!-- submits the form once the search button is clicked  -->
    <?php if ($food_searched !=='') : ?>
    <!-- if it isn't empty, fetch results -->
      <p>
        <?= count($food_results); ?> result(s) for
        "<?= e($food_searched); ?>"
      </p>
    <ul>
      <?php foreach ($food_results as $recipe) : ?>
        <li><?= e($recipe); ?></li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <!-- RECIPE SUBMISSION SECTION -->
    <h3> Recipe Submission </h3> 
    <form action="index.php" method="POST">
      <label for="name"> Recipe Name: </label>
      <input type="text" id="name" name="name" required>
      <label for="email"> Your Email: </label>
      <input type="email" id="email" name="email" required>
      <button type="submit"> Submit </button>
    </form>

    <?php if ($success) : ?> <!-- if recipe name and email aren't blank or missing info or are invalid it succeeds -->
      <p> Recipe "<?=e($recipe_name); ?>" submitted by <?=e($email); ?> </p>
    <?php else :
      foreach ($errors as $error) : ?>
      <p><?= e($error);?></p>
    <?php endforeach; ?>
    <?php endif; ?>
  </body>
</html>
