<?php
declare(strict_types=1);

require __DIR__ . '/session.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$postedCsrfToken = $_POST['csrf_token'] ?? '';
$csrfToken = is_string($postedCsrfToken) ? $postedCsrfToken : '';
if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    exit('Invalid form submission. Please return to the dashboard and try again.');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $cookieParameters = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $cookieParameters['path'],
        'domain' => $cookieParameters['domain'],
        'secure' => $cookieParameters['secure'],
        'httponly' => $cookieParameters['httponly'],
        'samesite' => $cookieParameters['samesite'] ?? 'Lax',
    ]);
}

session_destroy();
header('Location: login.php');
exit;
