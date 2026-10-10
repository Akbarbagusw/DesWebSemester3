<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['password_confirmation'] ?? '';

$errors = [];

if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}

if ($username === '') {
    $errors[] = 'Username wajib diisi.';
} elseif (!preg_match('/^[a-zA-Z0-9_\-]{3,30}$/', $username)) {
    $errors[] = 'Username hanya boleh berisi huruf, angka, garis bawah, atau tanda hubung (3-30 karakter).';
} else {
    $check = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
    $check->execute(['username' => $username]);
    if ($check->fetch()) {
        $errors[] = 'Username sudah terdaftar.';
    }
}

if (strlen($password) < 6) {
    $errors[] = 'Password minimal 6 karakter.';
}

if ($password !== $confirm) {
    $errors[] = 'Konfirmasi password tidak cocok.';
}

if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = [
        'nama' => $nama,
        'username' => $username,
    ];
    header('Location: register.php');
    exit;
}

$stmt = $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (:nama, :username, :password, 'petugas')");
$stmt->execute([
    'nama' => $nama,
    'username' => $username,
    'password' => password_hash($password, PASSWORD_DEFAULT),
]);

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Registrasi berhasil. Silakan login untuk melanjutkan.'];
header('Location: login.php');
exit;
