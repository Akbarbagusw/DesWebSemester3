<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$keyword = trim($_GET['q'] ?? '');
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE judul ILIKE :keyword ORDER BY id DESC");
    $stmt->execute(['keyword' => "%{$keyword}%"]);
    $daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $daftarBuku = $pdo->query("SELECT * FROM buku ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form method="get" action="list.php" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div class="search-box" style="flex: 1; min-width: 250px; display: flex; gap: 0.5rem; align-items: flex-end;">
            <div style="flex: 1;">
                <label for="search-input">Cari Judul Buku</label>
                <input type="text" id="search-input" name="q" class="table-filter" data-filter-column="0" placeholder="Ketik judul buku..." value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset Filter</a>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Buku</a>
        </div>
    </form>

    <?php if ($keyword !== ''): ?>
        <p style="margin-bottom: 0.85rem; font-size: 0.95rem; color: #444;">
            Hasil pencarian server untuk kata kunci: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong> 
            (ditemukan <?php echo count($daftarBuku); ?> buku) &mdash; 
            <a href="list.php">Tampilkan Semua</a>
        </p>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>ISBN</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Ditambahkan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="7">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($buku['judul']); ?></strong>
                                <?php if (!empty($buku['kategori'])): ?>
                                    <span style="display: block; font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($buku['kategori']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($buku['pengarang']); ?></td>
                            <td><code><?php echo htmlspecialchars(!empty($buku['isbn']) ? $buku['isbn'] : '-'); ?></code></td>
                            <td><?php echo htmlspecialchars($buku['tahun']); ?></td>
                            <td><?php echo htmlspecialchars($buku['stok']); ?></td>
                            <td>
                                <small style="color: #666;">
                                    <?php 
                                    if (!empty($buku['tanggal_ditambahkan'])) {
                                        echo htmlspecialchars(date('d/m/Y H:i', strtotime($buku['tanggal_ditambahkan'])));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </small>
                            </td>
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