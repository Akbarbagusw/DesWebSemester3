<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/koneksi.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$judul = trim($_POST['judul'] ?? '');
$pengarang = trim($_POST['pengarang'] ?? '');
$tahun = $_POST['tahun'] ?? '';
$isbn = trim($_POST['isbn'] ?? '');
$stok = $_POST['stok'] ?? '';
$kategori = trim($_POST['kategori'] ?? '');

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID buku tidak valid.'];
    header('Location: list.php');
    exit;
}

$errors = [];

if ($judul === '') {
    $errors[] = 'Judul wajib diisi.';
} elseif (strlen($judul) < 2) {
    $errors[] = 'Judul buku minimal harus 2 karakter.';
}

if ($pengarang === '') {
    $errors[] = 'Pengarang wajib diisi.';
} elseif (strlen($pengarang) < 2) {
    $errors[] = 'Nama pengarang minimal harus 2 karakter.';
}

if (!is_numeric($tahun) || (int) $tahun < 1900 || (int) $tahun > 2026) {
    $errors[] = 'Tahun harus di antara 1900-2026.';
}

if (!is_numeric($stok) || (int) $stok < 0) {
    $errors[] = 'Stok tidak boleh negatif.';
}

if ($isbn !== '') {
    if (!preg_match('/^[0-9\-]+$/', $isbn)) {
        $errors[] = 'ISBN hanya boleh berisi angka dan tanda hubung (misal: 978-602-03-8591-4).';
    } else {
        $digitsOnly = str_replace('-', '', $isbn);
        $digitCount = strlen($digitsOnly);
        if ($digitCount !== 10 && $digitCount !== 13) {
            $errors[] = 'ISBN harus memiliki 10 atau 13 digit angka (saat ini terdeteksi ' . $digitCount . ' digit).';
        }
    }
}

if (!empty($errors)) {
    $_SESSION['old_buku'] = $_POST;
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => implode(' ', $errors)
    ];
    header('Location: ubah.php?id=' . $id);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE buku
     SET judul = :judul,
         pengarang = :pengarang,
         tahun = :tahun,
         isbn = :isbn,
         stok = :stok,
         kategori = :kategori
     WHERE id = :id"
);
$stmt->execute([
    'judul' => $judul,
    'pengarang' => $pengarang,
    'tahun' => (int) $tahun,
    'isbn' => $isbn !== '' ? $isbn : null,
    'stok' => (int) $stok,
    'kategori' => $kategori !== '' ? $kategori : 'Umum',
    'id' => $id,
]);

$_SESSION['flash'] = [
    'type' => 'success',
    'pesan' => "Buku '{$judul}' berhasil diperbarui."
];
header('Location: list.php');
exit;
