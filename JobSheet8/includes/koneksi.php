<?php
date_default_timezone_set('Asia/Jakarta');

$host = "localhost";
$port = "5432";
$db   = "simpus_mini";
$user = "postgres";
$pass = "postgres";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $pdo->exec("
            ALTER TABLE buku ADD COLUMN IF NOT EXISTS tanggal_ditambahkan TIMESTAMP DEFAULT NOW();
            UPDATE buku SET tanggal_ditambahkan = NOW() WHERE tanggal_ditambahkan IS NULL;
        ");
    } catch (PDOException $ex) {
    }
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}