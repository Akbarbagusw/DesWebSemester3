<?php
require __DIR__ . '/../includes/auth.php';

$page_title = "Tambah Buku";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$old = $_SESSION['old_buku'] ?? [];
unset($_SESSION['old_buku']);
?>
<section>
    <h2>Tambah Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="proses_tambah.php">
        <p>
            <label for="judul">Judul Buku <span style="color: red;">*</span></label>
            <input type="text" id="judul" name="judul" required placeholder="Contoh: Laskar Pelangi" value="<?php echo htmlspecialchars($old['judul'] ?? ''); ?>">
        </p>

        <p>
            <label for="pengarang">Pengarang <span style="color: red;">*</span></label>
            <input type="text" id="pengarang" name="pengarang" required placeholder="Contoh: Andrea Hirata" value="<?php echo htmlspecialchars($old['pengarang'] ?? ''); ?>">
        </p>

        <p>
            <label for="tahun">Tahun Terbit</label>
            <input type="number" id="tahun" name="tahun" min="1900" max="2026" value="<?php echo htmlspecialchars($old['tahun'] ?? '2024'); ?>">
        </p>

        <p>
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" placeholder="Contoh: 978-602-03-8591-4" value="<?php echo htmlspecialchars($old['isbn'] ?? ''); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Hanya boleh berisi angka dan tanda hubung (10 atau 13 digit).</small>
        </p>

        <p>
            <label for="stok">Jumlah Stok <span style="color: red;">*</span></label>
            <input type="number" id="stok" name="stok" min="0" value="<?php echo htmlspecialchars($old['stok'] ?? '1'); ?>" required>
        </p>

        <p>
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori">
                <?php
                $kategoriList = ['Fiksi', 'Non-Fiksi', 'Referensi', 'Teknologi & Komputer', 'Sains'];
                $selectedKategori = $old['kategori'] ?? 'Fiksi';
                foreach ($kategoriList as $kat):
                ?>
                    <option value="<?php echo $kat; ?>" <?php echo $selectedKategori === $kat ? 'selected' : ''; ?>>
                        <?php echo $kat; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Buku</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>