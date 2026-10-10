<?php
require __DIR__ . '/../includes/auth.php';

$page_title = "Tambah Anggota";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$old = $_SESSION['old_anggota'] ?? [];
unset($_SESSION['old_anggota']);
?>
<section>
    <h2>Tambah Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-tambah" method="post" action="proses_tambah.php">
        <p>
            <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
            <input type="text" id="nama" name="nama" required placeholder="Contoh: Siti Aminah" value="<?php echo htmlspecialchars($old['nama'] ?? ''); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Minimal 3 karakter, berupa huruf alfabet.</small>
        </p>

        <p>
            <label for="no_anggota">Nomor Anggota <span style="color: red;">*</span></label>
            <input type="text" id="no_anggota" name="no_anggota" required placeholder="Contoh: AG-001" value="<?php echo htmlspecialchars($old['no_anggota'] ?? ''); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Alfanumerik unik 3-15 karakter.</small>
        </p>

        <p>
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" placeholder="Contoh: sitiaminah@example.com" value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
        </p>

        <p>
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars($old['tanggal_lahir'] ?? ''); ?>">
        </p>

        <p>
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <?php $selectedJK = $old['jenis_kelamin'] ?? 'Perempuan'; ?>
            <select id="jenis_kelamin" name="jenis_kelamin">
                <option value="Perempuan" <?php echo $selectedJK === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                <option value="Laki-laki" <?php echo $selectedJK === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
            </select>
        </p>

        <p>
            <label for="no_hp">Nomor HP / WhatsApp <span style="color: red;">*</span></label>
            <input type="tel" id="no_hp" name="no_hp" required placeholder="Contoh: 081234567890" value="<?php echo htmlspecialchars($old['no_hp'] ?? ''); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Awalan 08 atau +62, panjang 8-15 digit.</small>
        </p>

        <p>
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" placeholder="Contoh: Jl. Soekarno Hatta No. 9, Malang"><?php echo htmlspecialchars($old['alamat'] ?? ''); ?></textarea>
        </p>

        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Anggota</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>