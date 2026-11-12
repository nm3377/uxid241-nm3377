<?php

declare(strict_types=1);

require 'admin_guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: manage_recipes.php');
  exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
  header('Location: manage_recipes.php');
  exit;
}

mysqli_execute_query(
  $conn,
  'DELETE FROM recipes WHERE id = ?',
  [$id]
);

header('Location: manage_recipes.php');
exit;