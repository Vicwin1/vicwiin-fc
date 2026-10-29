<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = htmlspecialchars(
    (string) ($_SESSION['user_name'] ?? 'there'),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="auth-card">
    <h1>Hello, <?= $userName ?>!</h1>
    <p class="intro">You are logged in.</p>
    <form method="post" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(
            (string) ($_SESSION['csrf_token'] ?? ''),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        ) ?>">
        <button type="submit" class="secondary-button">Log out</button>
    </form>
</main>
</body>
</html>
