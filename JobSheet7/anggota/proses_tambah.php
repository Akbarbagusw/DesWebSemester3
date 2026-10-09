<?php
session_start();

$nama = trim($_POST['nama'] ?? '');
$no_anggota = trim($_POST['no_anggota'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');

$errors = [];

if ($nama === '') {
    $errors[] = "Nama lengkap wajib diisi.";
} elseif (strlen($nama) < 3) {
    $errors[] = "Nama lengkap minimal harus 3 karakter.";
} elseif (!preg_match('/^[a-zA-Z\s\.\',\-]+$/', $nama)) {
    $errors[] = "Nama lengkap hanya boleh berisi huruf, spasi, dan tanda baca nama.";
}

if ($no_anggota === '') {
    $errors[] = "Nomor anggota wajib diisi.";
} elseif (!preg_match('/^[A-Za-z0-9\-]{3,15}$/', $no_anggota)) {
    $errors[] = "Nomor anggota harus berupa alfanumerik 3-15 karakter (contoh: AG-001).";
}

if ($email === '') {
    $errors[] = "Alamat email wajib diisi.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format alamat email tidak valid (contoh: anggota@domain.com).";
}

if (isset($_SESSION['anggota']) && is_array($_SESSION['anggota'])) {
    foreach ($_SESSION['anggota'] as $existing) {
        if (strcasecmp($existing['no_anggota'] ?? '', $no_anggota) === 0) {
            $errors[] = "Nomor anggota '{$no_anggota}' sudah terdaftar. Gunakan nomor anggota lain.";
            break;
        }
        if (strcasecmp($existing['email'] ?? '', $email) === 0) {
            $errors[] = "Alamat email '{$email}' sudah digunakan oleh anggota lain.";
            break;
        }
    }
}

if ($no_hp === '') {
    $errors[] = "Nomor HP wajib diisi.";
} elseif (!preg_match('/^(\+62|62|08)[0-9]{8,13}$/', $no_hp)) {
    $errors[] = "Nomor HP harus berupa format telepon Indonesia yang valid (contoh: 081234567890 atau +628123456789).";
}

if ($tanggal_lahir !== '') {
    $birthTime = strtotime($tanggal_lahir);
    if (!$birthTime || $birthTime > time()) {
        $errors[] = "Tanggal lahir tidak valid dan tidak boleh melebihi tanggal hari ini.";
    }
}

$validJK = ['Perempuan', 'Laki-laki'];
if ($jenis_kelamin !== '' && !in_array($jenis_kelamin, $validJK, true)) {
    $errors[] = "Pilihan jenis kelamin tidak valid.";
}

if ($alamat !== '' && strlen($alamat) < 5) {
    $errors[] = "Alamat terlalu pendek, mohon isi minimal 5 karakter.";
}

if (!empty($errors)) {
    $_SESSION['old_anggota'] = $_POST;
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];
    header('Location: tambah.php');
    exit;
}

unset($_SESSION['old_anggota']);

if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

$_SESSION['anggota'][] = [
    'no_anggota' => strtoupper($no_anggota),
    'nama' => $nama,
    'email' => strtolower($email),
    'no_hp' => $no_hp,
    'alamat' => $alamat !== '' ? $alamat : '-',
    'tanggal_lahir' => $tanggal_lahir !== '' ? $tanggal_lahir : '-',
    'jenis_kelamin' => $jenis_kelamin !== '' ? $jenis_kelamin : '-',
];

$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => "Anggota baru '{$nama}' (" . strtoupper($no_anggota) . ") berhasil didaftarkan."
];
header('Location: list.php');
exit;
