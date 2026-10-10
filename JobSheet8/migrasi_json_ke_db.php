<?php
$page_title = "Migrasi Data JSON ke DB";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$jsonPath = __DIR__ . '/data/buku.json';
$errors = [];
$logs = [];
$stats = [
    'sukses' => 0,
    'dilewati' => 0,
    'gagal' => 0,
];

if (!file_exists($jsonPath)) {
    $errors[] = "File arsip JSON tidak ditemukan di: {$jsonPath}.";
    $daftarBuku = [];
} else {
    $jsonData = file_get_contents($jsonPath);
    $daftarBuku = json_decode($jsonData, true);
    if (!is_array($daftarBuku)) {
        $errors[] = "Format isi file data/buku.json tidak valid.";
        $daftarBuku = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($daftarBuku)) {
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE judul = :judul AND pengarang = :pengarang");
    $insertStmt = $pdo->prepare(
        "INSERT INTO buku (judul, pengarang, tahun, isbn, stok, kategori)
        VALUES (:judul, :pengarang, :tahun, :isbn, :stok, :kategori)
        RETURNING id"
    );

    foreach ($daftarBuku as $buku) {
        $judul = trim($buku['judul'] ?? '');
        $pengarang = trim($buku['pengarang'] ?? '');
        $tahun = (int) ($buku['tahun'] ?? 2024);
        $isbn = !empty($buku['isbn']) ? trim($buku['isbn']) : null;
        $stok = (int) ($buku['stok'] ?? 0);
        $kategori = !empty($buku['kategori']) ? trim($buku['kategori']) : 'Umum';

        try {
            $checkStmt->execute(['judul' => $judul, 'pengarang' => $pengarang]);
            if ($checkStmt->fetchColumn() > 0) {
                $logs[] = [
                    'status' => 'warning',
                    'pesan' => "Dilewati: Buku '{$judul}' ({$pengarang}) sudah ada di database."
                ];
                $stats['dilewati']++;
                continue;
            }

            $insertStmt->execute([
                'judul'     => $judul,
                'pengarang' => $pengarang,
                'tahun'     => $tahun,
                'isbn'      => $isbn,
                'stok'      => $stok,
                'kategori'  => $kategori,
            ]);
            $newId = $insertStmt->fetchColumn();
            $logs[] = [
                'status' => 'success',
                'pesan' => "Berhasil migrasi: '{$judul}' oleh {$pengarang} (ID Database: {$newId})."
            ];
            $stats['sukses']++;
        } catch (PDOException $e) {
            $logs[] = [
                'status' => 'error',
                'pesan' => "Gagal migrasi '{$judul}': " . $e->getMessage()
            ];
            $stats['gagal']++;
        }
    }
}
?>

<section>
    <h2>Migrasi Data Lama dari JSON ke Database PostgreSQL</h2>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error" style="margin-top: 1rem;">
            <strong>Perhatian:</strong>
            <ul style="margin-left: 1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($logs)): ?>
        <div class="flash flash-info" style="margin-top: 1rem;">
            <strong>Ringkasan Hasil Migrasi:</strong>
            <p style="margin: 0.5rem 0;">
                Berhasil: <strong><?php echo $stats['sukses']; ?></strong> |
                Dilewati (Sudah Ada): <strong><?php echo $stats['dilewati']; ?></strong> |
                Gagal: <strong><?php echo $stats['gagal']; ?></strong>
            </p>
            <ul style="margin-left: 1.25rem; font-size: 0.9rem; line-height: 1.6;">
                <?php foreach ($logs as $log): ?>
                    <li>
                        <span class="badge badge-<?php echo $log['status']; ?>">
                            <?php echo strtoupper($log['status']); ?>
                        </span>
                        <?php echo htmlspecialchars($log['pesan']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="migrasi_json_ke_db.php" style="margin: 1.5rem 0;">
        <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-size: 1rem;" <?php echo empty($daftarBuku) ? 'disabled' : ''; ?>>
            Jalankan Migrasi Data JSON ke PostgreSQL
        </button>
        <a href="buku/list.php" class="btn btn-secondary" style="padding: 0.65rem 1.25rem; font-size: 1rem;">
            Lihat Katalog Buku
        </a>
    </form>
</section>

<section>
    <h2>Data pada File Arsip (<code>data/buku.json</code>)</h2>
    <?php if (empty($daftarBuku)): ?>
        <p style="color: #666;">Tidak ada data atau file JSON belum tersedia.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Tahun</th>
                        <th>ISBN</th>
                        <th>Stok</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftarBuku as $b): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($b['judul'] ?? '-'); ?></strong></td>
                            <td><?php echo htmlspecialchars($b['pengarang'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($b['tahun'] ?? '-'); ?></td>
                            <td><code><?php echo htmlspecialchars(!empty($b['isbn']) ? $b['isbn'] : '-'); ?></code></td>
                            <td><?php echo htmlspecialchars($b['stok'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($b['kategori'] ?? 'Umum'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
