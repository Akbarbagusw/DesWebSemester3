<?php
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$keyword = trim($_GET['q'] ?? '');
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM anggota WHERE nama ILIKE :keyword OR no_anggota ILIKE :keyword ORDER BY id DESC");
    $stmt->execute(['keyword' => "%{$keyword}%"]);
    $daftarAnggota = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarAnggota = $pdo->query("SELECT * FROM anggota ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<section>
    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form method="get" action="list.php" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div class="search-box" style="flex: 1; min-width: 250px; display: flex; gap: 0.5rem; align-items: flex-end;">
            <div style="flex: 1;">
                <label for="search-input">Cari Nama / No. Anggota</label>
                <input type="text" id="search-input" name="q" class="table-filter" data-filter-column="1" placeholder="Ketik nama atau nomor anggota..." value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset Filter</a>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Anggota</a>
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarAnggota)): ?>
                    <tr>
                        <td colspan="5">Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarAnggota as $anggota): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($anggota['no_anggota'] ?? '-'); ?></code></td>
                            <td>
                                <strong><?php echo htmlspecialchars($anggota['nama'] ?? '-'); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($anggota['no_hp'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($anggota['alamat'] ?? '-'); ?></td>
                            <td>
                                <button type="button">Edit</button>
                                <button type="button" class="btn-hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>