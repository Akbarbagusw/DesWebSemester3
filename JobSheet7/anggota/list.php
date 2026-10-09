<?php
$page_title = "Daftar Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$daftarAnggota = $_SESSION['anggota'] ?? [];
?>
<section>
    <h2>Daftar Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div class="search-box" style="flex: 1; min-width: 250px;">
            <label for="search-input">Cari Nama Anggota</label>
            <input type="text" id="search-input" class="table-filter" data-filter-column="1" placeholder="Ketik nama anggota...">
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Anggota</a>
            <a href="../reset_session.php" class="btn btn-hapus" onclick="return confirm('Reset seluruh data di session?');">Reset Data</a>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarAnggota)): ?>
                    <tr>
                        <td colspan="6">Belum ada data anggota. Silakan tambah lewat menu "Tambah Anggota".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarAnggota as $anggota): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($anggota['no_anggota'] ?? '-'); ?></code></td>
                            <td>
                                <strong><?php echo htmlspecialchars($anggota['nama'] ?? '-'); ?></strong>
                                <?php if (!empty($anggota['jenis_kelamin'])): ?>
                                    <span style="display: block; font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($anggota['jenis_kelamin']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($anggota['email'] ?? '-'); ?></td>
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