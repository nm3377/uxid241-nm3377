<?php

declare(strict_types=1);

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
    <title>UXID 241 Cookbook Assignment 3</title>
</head>
<body>
    <form action="index.php" method="GET">
    <label for="q">Search recipes:</label>

    <input
        type="search"
        id="q"
        name="q"
        value="<?= e($search); ?>"
        required 
    >

    <button type="submit">Search</button>
    </form>

    <h2>Available Recipes:</h2>
    <?php if (empty($recipes)): ?>

    <p>No recipes found for "<?= e($search); ?>".</p>

    <?php else: ?>
        <?php foreach ($recipes as $recipe):?>
            <h3>Title: <?= e($recipe['title']) . '<br>'; ?></h3> 
            <h4>Description: <?= e($recipe['description']); ?></h4> 
            <h5>Ingredients: <?= e($recipe['ingredients']); ?></h5>
            <?php $steps = explode('*', $recipe['steps']); ?>
            <p>Steps:</p>
                <ol>
                    <?php foreach ($steps as $step): ?>
                    <li><?= e($step); ?></li>
                    <?php endforeach; ?>
                </ol>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>