<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Permintaan hapus tidak valid.'];
    header('Location: list.php');
    exit;
}

require __DIR__ . '/../includes/koneksi.php';

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM anggota WHERE id = :id');
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil dihapus.'];
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota tidak ditemukan atau sudah dihapus.'];
}

header('Location: list.php');
exit;
