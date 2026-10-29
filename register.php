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

$fullName = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedName = $_POST['full_name'] ?? '';
    $postedEmail = $_POST['email'] ?? '';
    $postedPassword = $_POST['password'] ?? '';
    $postedConfirmation = $_POST['confirm_password'] ?? '';
    $postedCsrfToken = $_POST['csrf_token'] ?? '';
    $fullName = is_string($postedName) ? trim($postedName) : '';
    $email = is_string($postedEmail) ? strtolower(trim($postedEmail)) : '';
    $password = is_string($postedPassword) ? $postedPassword : '';
    $confirmPassword = is_string($postedConfirmation) ? $postedConfirmation : '';
    $csrfToken = is_string($postedCsrfToken) ? $postedCsrfToken : '';

    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $errors[] = 'Your form session expired. Please try again.';
    }

    if ($fullName === '' || preg_match('/\A.{1,100}\z/us', $fullName) !== 1) {
        $errors[] = 'Enter a name that is no more than 100 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
        $errors[] = 'Enter a valid email address.';
    }

    if (strlen($password) < 8 || strlen($password) > 72) {
        $errors[] = 'Choose a password between 8 and 72 bytes.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'The password and confirmation do not match.';
    }

    if ($errors === []) {
        try {
            require __DIR__ . '/config.php';

            $statement = $pdo->prepare(
                'INSERT INTO users (full_name, email, password) VALUES (:full_name, :email, :password)'
            );
            $statement->execute([
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $exception) {
            error_log('Registration database error: ' . $exception->getMessage());

            if ($exception->getCode() === '23000') {
                $errors[] = 'An account with that email address already exists.';
            } else {
                $errors[] = 'We could not create your account right now. Please try again later.';
            }
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
    <title>Create an account</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js" defer></script>
</head>
<body>
<main class="auth-card">
    <h1>Create an account</h1>
    <p class="intro">Fill in your details to get started.</p>

    <?php if ($errors !== []): ?>
        <div class="message error" role="alert">
            <p>Please check the following:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php" id="register-form">
        <input type="hidden" name="csrf_token" value="<?= escape($_SESSION['csrf_token']) ?>">

        <label for="full_name">Full name</label>
        <input id="full_name" name="full_name" type="text" autocomplete="name"
               maxlength="100" value="<?= escape($fullName) ?>" required>

        <label for="email">Email address</label>
        <input id="email" name="email" type="email" autocomplete="email"
               maxlength="150" value="<?= escape($email) ?>" required>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="new-password"
               minlength="8" maxlength="72" required>

        <label for="confirm_password">Confirm password</label>
        <input id="confirm_password" name="confirm_password" type="password"
               autocomplete="new-password" minlength="8" maxlength="72" required>
        <p id="password-hint" class="field-hint" aria-live="polite"></p>

        <button type="submit">Create account</button>
    </form>

    <p class="switch-link">Already have an account? <a href="login.php">Log in</a></p>
</main>
</body>
</html>
