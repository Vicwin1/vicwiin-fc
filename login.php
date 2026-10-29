<?php
declare(strict_types=1);

require __DIR__ . '/session.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedEmail = $_POST['email'] ?? '';
    $postedPassword = $_POST['password'] ?? '';
    $postedCsrfToken = $_POST['csrf_token'] ?? '';
    $email = is_string($postedEmail) ? strtolower(trim($postedEmail)) : '';
    $password = is_string($postedPassword) ? $postedPassword : '';
    $csrfToken = is_string($postedCsrfToken) ? $postedCsrfToken : '';

    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Your form session expired. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150 || $password === '') {
        $error = 'Enter your email address and password.';
    } else {
        try {
            require __DIR__ . '/config.php';
            $statement = $pdo->prepare(
                'SELECT id, full_name, email, password FROM users WHERE email = :email'
            );
            $statement->execute(['email' => $email]);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                header('Location: dashboard.php');
                exit;
            }

            $error = 'The email address or password is incorrect.';
        } catch (PDOException $exception) {
            error_log('Login database error: ' . $exception->getMessage());
            $error = 'We could not log you in right now. Please try again later.';
        }
    }
}

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="auth-card">
    <h1>Welcome back</h1>
    <p class="intro">Log in to your account.</p>

    <?php if (isset($_GET['registered'])): ?>
        <p class="message success" role="status">Your account is ready. Please log in.</p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="message error" role="alert"><?= escape($error) ?></p>
    <?php endif; ?>

    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="email"
               maxlength="150" value="<?= escape($email) ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <button type="submit">Log in</button>
    </form>

    <p class="switch-link">New here? <a href="register.php">Create an account</a></p>
</main>
</body>
</html>
