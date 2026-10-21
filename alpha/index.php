<?php
    declare(strict_types=1);

    require 'database.php';
    
    $result = mysqli_query($conn, 'SELECT title, description, ingredients, step1, step2, step3, step4, step5, step6 from recipes'); 
    // currently, I have all the steps being called upon, even though I only have steps 1
    // and 2 displayed on the page, this is intentional so that i dont make the same mistake
    // i did when intially trying to include ingredients in this alpha
    $recipes = mysqli_fetch_all($result, MYSQLI_ASSOC);

   function e(string $value): string { 
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); 
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UXID 241 Cookbook Assignment 3</title>
</head>
<body>
    
    <h2>Available Recipes:</h2>

    <?php 
        foreach ($recipes as $recipe):?>
        <h3>Title: <?= e($recipe['title']) . '<br>'; ?></h3> 
        <h4>Description: <?= e($recipe['description']); ?></h4> 
        <h5>Ingredients: <?= e($recipe['ingredients']); ?></h5>
        <p>Step 1: <?= e($recipe['step1']); ?></p>
        <p>Step 2: <?= e($recipe['step2']); ?></p> 
    <?php endforeach; ?>
    
</body>
</html>