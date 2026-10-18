<?php
    declare(strict_types=1);

    require 'database.php';
    
    $result = mysqli_query($conn, 'SELECT title, description, step1 from recipes');
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
    <?php endforeach; ?>
</body>
</html>