<?php
require __DIR__ . '/../includes/auth.php';

$page_title = "Ubah Anggota";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID anggota tidak valid.'];
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = :id");
$stmt->execute(['id' => $id]);
$anggota = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anggota) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Data anggota tidak ditemukan.'];
    header('Location: list.php');
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$old = $_SESSION['old_anggota'] ?? $anggota;
unset($_SESSION['old_anggota']);
?>
<section>
    <h2>Ubah Anggota</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form id="form-ubah" method="post" action="proses_ubah.php" onsubmit="return confirm('Yakin ingin menyimpan perubahan data anggota ini?');">
        <input type="hidden" name="id" value="<?php echo (int) $anggota['id']; ?>">

        <p>
            <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
            <input type="text" id="nama" name="nama" required placeholder="Contoh: Siti Aminah" value="<?php echo htmlspecialchars((string) ($old['nama'] ?? $anggota['nama'] ?? '')); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Minimal 3 karakter, berupa huruf alfabet.</small>
        </p>

        <p>
            <label for="no_anggota">Nomor Anggota <span style="color: red;">*</span></label>
            <input type="text" id="no_anggota" name="no_anggota" required placeholder="Contoh: AG-001" value="<?php echo htmlspecialchars((string) ($old['no_anggota'] ?? $anggota['no_anggota'] ?? '')); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Alfanumerik unik 3-15 karakter.</small>
        </p>

        <p>
            <label for="email">Alamat Email</label>
            <input type="email" id="email" name="email" placeholder="Contoh: sitiaminah@example.com" value="<?php echo htmlspecialchars((string) ($old['email'] ?? $anggota['email'] ?? '')); ?>">
        </p>

        <p>
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" id="tanggal_lahir" name="tanggal_lahir" value="<?php echo htmlspecialchars((string) ($old['tanggal_lahir'] ?? $anggota['tanggal_lahir'] ?? '')); ?>">
        </p>

        <p>
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <?php $selectedJK = $old['jenis_kelamin'] ?? $anggota['jenis_kelamin'] ?? 'Perempuan'; ?>
            <select id="jenis_kelamin" name="jenis_kelamin">
                <option value="Perempuan" <?php echo $selectedJK === 'Perempuan' ? 'selected' : ''; ?>>Perempuan</option>
                <option value="Laki-laki" <?php echo $selectedJK === 'Laki-laki' ? 'selected' : ''; ?>>Laki-laki</option>
            </select>
        </p>

        <p>
            <label for="no_hp">Nomor HP / WhatsApp <span style="color: red;">*</span></label>
            <input type="tel" id="no_hp" name="no_hp" required placeholder="Contoh: 081234567890" value="<?php echo htmlspecialchars((string) ($old['no_hp'] ?? $anggota['no_hp'] ?? '')); ?>">
            <small style="color: #666; display: block; margin-top: 0.25rem;">Awalan 08 atau +62, panjang 8-15 digit.</small>
        </p>

        <p>
            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" rows="3" placeholder="Contoh: Jl. Soekarno Hatta No. 9, Malang"><?php echo htmlspecialchars((string) ($old['alamat'] ?? $anggota['alamat'] ?? '')); ?></textarea>
        </p>

        <p style="margin-top: 1.5rem;">
            <button type="submit">Simpan Perubahan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
