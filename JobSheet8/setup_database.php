<?php
$page_title = "Inisialisasi Database";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$logs = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sqlFiles = [
        'sql/01_buku_anggota.sql' => 'Skema Tabel Dasar (buku & anggota)',
        'sql/02_tambah_kolom_tanggal.sql' => 'Latihan 7.4 #2: Kolom tanggal_ditambahkan',
    ];

    foreach ($sqlFiles as $relPath => $label) {
        $fullPath = __DIR__ . '/' . $relPath;
        if (!file_exists($fullPath)) {
            $errors[] = "File {$relPath} tidak ditemukan.";
            continue;
        }

        $sqlContent = file_get_contents($fullPath);
        try {
            $pdo->exec($sqlContent);
            $logs[] = "Berhasil mengeksekusi: <strong>{$label}</strong> ({$relPath})";
        } catch (PDOException $e) {
            $errors[] = "Gagal mengeksekusi {$relPath}: " . $e->getMessage();
        }
    }
}

$tabelStatus = [];
try {
    $stmtBuku = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'buku' ORDER BY ordinal_position");
    $tabelStatus['buku'] = $stmtBuku->fetchAll(PDO::FETCH_ASSOC);

    $stmtAnggota = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'anggota' ORDER BY ordinal_position");
    $tabelStatus['anggota'] = $stmtAnggota->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Gagal membaca struktur tabel: " . $e->getMessage();
}
?>

<section>
    <h2>Inisialisasi &amp; Migrasi Skema Database</h2>

    <?php if (!empty($errors)): ?>
        <div class="flash flash-error">
            <strong>Terjadi Kesalahan:</strong>
            <ul style="margin-left: 1.25rem; margin-top: 0.5rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($logs)): ?>
        <div class="flash flash-success">
            <strong>Hasil Eksekusi Skema:</strong>
            <ul style="margin-left: 1.25rem; margin-top: 0.5rem;">
                <?php foreach ($logs as $log): ?>
                    <li><?php echo $log; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="setup_database.php" style="margin: 1.5rem 0;">
        <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-size: 1rem;">
            Jalankan / Perbarui Skema Database
        </button>
    </form>
</section>

<section>
    <h2>Status Tabel di Database PostgreSQL</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
        <div>
            <h3>Tabel <code>buku</code></h3>
            <?php if (empty($tabelStatus['buku'])): ?>
                <p style="color: #b42318; margin-top: 0.5rem;">Tabel belum dibuat di database.</p>
            <?php else: ?>
                <table style="margin-top: 0.5rem;">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Tipe Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabelStatus['buku'] as $col): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($col['column_name']); ?></code></td>
                                <td><?php echo htmlspecialchars($col['data_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div>
            <h3>Tabel <code>anggota</code></h3>
            <?php if (empty($tabelStatus['anggota'])): ?>
                <p style="color: #b42318; margin-top: 0.5rem;">Tabel belum dibuat di database.</p>
            <?php else: ?>
                <table style="margin-top: 0.5rem;">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Tipe Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tabelStatus['anggota'] as $col): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($col['column_name']); ?></code></td>
                                <td><?php echo htmlspecialchars($col['data_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
