<?php
require __DIR__ . '/../includes/auth.php';

$page_title = "Ubah Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID buku tidak valid.'];
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM buku WHERE id = :id");
$stmt->execute(['id' => $id]);
$buku = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$buku) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data buku tidak ditemukan.'];
    header('Location: list.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$old = $_SESSION['old_buku'] ?? $buku;
unset($_SESSION['old_buku']);
?>
<section>
    <h2>Ubah Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-ubah" method="post" action="proses_ubah.php" onsubmit="return confirm('Yakin ingin menyimpan perubahan data buku ini?');">
        <input type="hidden" name="id" value="<?php echo (int) $buku['id']; ?>">

        <p>
            <label for="judul">Judul Buku <span style="color: red;">*</span></label>
            <input type="text" id="judul" name="judul" required placeholder="Contoh: Laskar Pelangi" value="<?php echo htmlspecialchars((string) ($old['judul'] ?? $buku['judul'] ?? '')); ?>">
        </p>

        <p>
            <label for="pengarang">Pengarang <span style="color: red;">*</span></label>
            <input type="text" id="pengarang" name="pengarang" required placeholder="Contoh: Andrea Hirata" value="<?php echo htmlspecialchars((string) ($old['pengarang'] ?? $buku['pengarang'] ?? '')); ?>">
        </p>

        <p>
            <label for="tahun">Tahun Terbit</label>
            <input type="number" id="tahun" name="tahun" min="1900" max="2026" value="<?php echo htmlspecialchars((string) ($old['tahun'] ?? $buku['tahun'] ?? '')); ?>">
        </p>

        <p>
            <label for="isbn">ISBN</label>
            <input type="text" id="isbn" name="isbn" placeholder="Contoh: 978-602-03-8591-4" value="<?php echo htmlspecialchars((string) ($old['isbn'] ?? $buku['isbn'] ?? '')); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Hanya boleh berisi angka dan tanda hubung (10 atau 13 digit).</small>
        </p>

        <p>
            <label for="stok">Jumlah Stok <span style="color: red;">*</span></label>
            <input type="number" id="stok" name="stok" min="0" value="<?php echo htmlspecialchars((string) ($old['stok'] ?? $buku['stok'] ?? '0')); ?>" required>
        </p>

        <p>
            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori">
                <?php
                $kategoriList = ['Fiksi', 'Non-Fiksi', 'Referensi', 'Teknologi & Komputer', 'Sains'];
                $selectedKategori = $old['kategori'] ?? $buku['kategori'] ?? 'Fiksi';
                foreach ($kategoriList as $kat):
                ?>
                    <option value="<?php echo $kat; ?>" <?php echo $selectedKategori === $kat ? 'selected' : ''; ?>>
                        <?php echo $kat; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Perubahan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
