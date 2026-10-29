run<?php
declare(strict_types=1);

require __DIR__ . '/session.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userName = htmlspecialchars(
    (string) ($_SESSION['user_name'] ?? 'there'),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$userEmail = htmlspecialchars(
    (string) ($_SESSION['user_email'] ?? ''),
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);
$csrfToken = htmlspecialchars(
    (string) ($_SESSION['csrf_token'] ?? ''),
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
<body class="dashboard-body">
<div class="dashboard">
    <header class="dashboard-header">
        <a class="brand" href="dashboard.php">My Account</a>
        <nav class="dashboard-nav" aria-label="Main navigation">
            <a class="nav-link active" href="dashboard.php" aria-current="page">Dashboard</a>
            <form method="post" action="logout.php">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                <button type="submit" class="nav-logout">Log out</button>
            </form>
        </nav>
    </header>

    <main class="dashboard-main">
        <section class="welcome-panel">
            <p class="eyebrow">YOUR DASHBOARD</p>
            <h1>Welcome, <?= $userName ?>!</h1>
            <p class="intro">You’re signed in. Here are your account details.</p>
        </section>

        <section class="account-panel" aria-labelledby="account-heading">
            <h2 id="account-heading">Account information</h2>
            <dl class="account-details">
                <div>
                    <dt>Full name</dt>
                    <dd><?= $userName ?></dd>
                </div>
                <div>
                    <dt>Email address</dt>
                    <dd><?= $userEmail ?></dd>
                </div>
            </dl>
        </section>
    </main>
</div>
<script>
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</body>
</html>
