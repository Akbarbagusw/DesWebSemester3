<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/remember_me.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$attemptsKey = hash('sha256', strtolower($username));
$attempts = $_SESSION['login_attempts'][$attemptsKey] ?? ['count' => 0, 'locked_until' => 0];

if ($attempts['locked_until'] > time()) {
    $remainingMinutes = (int) ceil(($attempts['locked_until'] - time()) / 60);
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => "Terlalu banyak percobaan login. Coba lagi dalam {$remainingMinutes} menit.",
    ];
    header('Location: login.php');
    exit;
}

if ($attempts['locked_until'] > 0) {
    $attempts = ['count' => 0, 'locked_until' => 0];
}

if ($username === '' || $password === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username dan password wajib diisi.'];
    header('Location: login.php');
    exit;
}

require __DIR__ . '/../includes/koneksi.php';

$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    $attempts['count']++;
    if ($attempts['count'] >= 5) {
        $attempts['locked_until'] = time() + 900;
    }
    $_SESSION['login_attempts'][$attemptsKey] = $attempts;

    if ($attempts['locked_until'] > time()) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'Terlalu banyak percobaan login. Percobaan login dikunci selama 15 menit.',
        ];
        header('Location: login.php');
        exit;
    }

    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
    header('Location: login.php');
    exit;
}

unset($_SESSION['login_attempts'][$attemptsKey]);
if (empty($_SESSION['login_attempts'])) {
    unset($_SESSION['login_attempts']);
}
set_authenticated_user($user);
revoke_remember_token();
if (isset($_POST['remember_me']) && $_POST['remember_me'] === '1') {
    create_remember_token($pdo, (int) $user['id']);
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Login berhasil. Selamat datang di dashboard.'];
header('Location: ../index.php');
exit;
