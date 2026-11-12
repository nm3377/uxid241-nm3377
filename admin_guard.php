<?php

declare(strict_types=1);

session_start();

require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$result = mysqli_execute_query(
  $conn,
  'SELECT is_admin
   FROM users
   WHERE id = ?',
  [$_SESSION['user_id']]
);

$user = mysqli_fetch_assoc($result);

if (!$user || !(bool) $user['is_admin']) {
  header('Location: login.php');
  exit;
}