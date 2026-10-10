<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/remember_me.php';
revoke_remember_token();

$flash = ['type' => 'success', 'pesan' => 'Anda telah logout.'];
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();
session_start();
$_SESSION['flash'] = $flash;

header('Location: login.php');
exit;
