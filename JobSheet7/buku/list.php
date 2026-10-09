<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarBuku = $_SESSION['buku'] ?? [];
?>
<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div class="search-box" style="flex: 1; min-width: 250px;">
            <label for="search-input">Cari Judul Buku</label>
            <input type="text" id="search-input" class="table-filter" data-filter-column="0" placeholder="Ketik judul buku...">
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Buku</a>
            <a href="../reset_session.php" class="btn btn-hapus" onclick="return confirm('Reset seluruh data di session?');">Reset Data</a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>ISBN</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="6">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</td>
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