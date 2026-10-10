<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nama = trim($_POST['nama'] ?? '');
$no_anggota = trim($_POST['no_anggota'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_hp = trim($_POST['no_hp'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
    header('Location: list.php');
    exit;
}

$errors = [];

if ($nama === '') {
    $errors[] = 'Nama lengkap wajib diisi.';
} elseif (strlen($nama) < 3) {
    $errors[] = 'Nama lengkap minimal harus 3 karakter.';
} elseif (!preg_match('/^[a-zA-Z\s\.\',\-]+$/', $nama)) {
    $errors[] = 'Nama lengkap hanya boleh berisi huruf, spasi, dan tanda baca nama.';
}

if ($no_anggota === '') {
    $errors[] = 'Nomor anggota wajib diisi.';
} elseif (!preg_match('/^[A-Za-z0-9\-]{3,15}$/', $no_anggota)) {
    $errors[] = 'Nomor anggota harus berupa alfanumerik 3-15 karakter (contoh: AG-001).';
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format alamat email tidak valid (contoh: anggota@domain.com).';
}

if ($no_hp === '') {
    $errors[] = 'Nomor HP wajib diisi.';
} elseif (!preg_match('/^(\+62|62|08)[0-9]{8,13}$/', $no_hp)) {
    $errors[] = 'Nomor HP harus berupa format telepon Indonesia yang valid (contoh: 081234567890 atau +628123456789).';
}

if ($tanggal_lahir !== '') {
    $birthTime = strtotime($tanggal_lahir);
    if (!$birthTime || $birthTime > time()) {
        $errors[] = 'Tanggal lahir tidak valid dan tidak boleh melebihi tanggal hari ini.';
    }
}

$validJK = ['Perempuan', 'Laki-laki'];
if ($jenis_kelamin !== '' && !in_array($jenis_kelamin, $validJK, true)) {
    $errors[] = 'Pilihan jenis kelamin tidak valid.';
}

if ($alamat !== '' && strlen($alamat) < 5) {
    $errors[] = 'Alamat terlalu pendek, mohon isi minimal 5 karakter.';
}

if (!empty($errors)) {
    $_SESSION['old_anggota'] = $_POST;
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];
    header('Location: ubah.php?id=' . $id);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE anggota
     SET nama = :nama,
         no_anggota = :no_anggota,
         alamat = :alamat,
         no_hp = :no_hp
     WHERE id = :id"
);

try {
    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => strtoupper($no_anggota),
        'alamat' => $alamat !== '' ? $alamat : null,
        'no_hp' => $no_hp,
        'id' => $id,
    ]);

    $_SESSION['flash'] = [
        'type' => 'success',
        'pesan' => "Data anggota '{$nama}' ({$no_anggota}) berhasil diperbarui."
    ];
    header('Location: list.php');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23505' || stripos($e->getMessage(), 'unique') !== false) {
        $_SESSION['old_anggota'] = $_POST;
        $_SESSION['flash'] = [
            'type' => 'error',
            'pesan' => 'No. Anggota sudah dipakai, gunakan nomor lain.'
        ];
        header('Location: ubah.php?id=' . $id);
        exit;
    }
    throw $e;
}
