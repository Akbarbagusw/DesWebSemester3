<?php
require __DIR__ . '/includes/functions.php';

$page_title = 'Registrasi Anggota';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $identitas = trim($_POST['identitas'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_hp = trim($_POST['no_hp'] ?? '');
    $jenis_kelamin = trim($_POST['jk'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    if ($nama === '') $errors[] = 'Nama lengkap wajib diisi.';
    if ($identitas === '') $errors[] = 'Nomor identitas wajib diisi.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if ($no_hp === '') $errors[] = 'Nomor HP wajib diisi.';
    if ($password === '' || strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $konfirmasi_password) $errors[] = 'Konfirmasi password tidak cocok.';

    if (empty($errors)) {
        $members = read_json_array('anggota');
        $last_code = 'A000';

        foreach ($members as $member) {
            $code = $member['no_anggota'] ?? '';
            if (preg_match('/^A\d+$/', $code) && strcmp($code, $last_code) > 0) {
                $last_code = $code;
            }
        }

        $number = (int)substr($last_code, 1) + 1;
        $new_member = [
            'no_anggota' => 'A' . str_pad((string)$number, 3, '0', STR_PAD_LEFT),
            'nama' => $nama,
            'alamat' => $alamat,
            'no_hp' => $no_hp,
            'email' => $email,
            'jenis_kelamin' => $jenis_kelamin,
            'identitas' => $identitas,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $members[] = $new_member;
        write_json_array('anggota', $members);
        flash('success', 'Registrasi berhasil. Silakan login untuk melanjutkan.');
        redirect('login.php');
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="auth-wrapper" style="max-width: 640px; margin: 0 auto;">
    <div class="auth-card" style="padding: 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <div class="auth-header" style="margin-bottom: 1rem;">
            <h2 style="margin-bottom: 0.5rem;">Registrasi Anggota Baru</h2>
            <p>Silakan lengkapi formulir di bawah ini untuk menjadi anggota SIMPUS-Mini.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <ul style="padding-left: 1.25rem; color: #b42318; margin-bottom: 1rem;">
                <?php foreach ($errors as $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="registrasi.php" method="POST">
            <p>
                <label for="nama">Nama Lengkap : <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap Anda" required value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
            </p>

            <p>
                <label for="identitas">No. Identitas (NIK / NIM / NIS) : <span style="color: red;">*</span></label>
                <input type="text" id="identitas" name="identitas" placeholder="16 digit NIK atau Nomor Induk" required value="<?php echo htmlspecialchars($_POST['identitas'] ?? ''); ?>">
            </p>

            <p>
                <label for="email">Alamat Email : <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </p>

            <p>
                <label for="no_hp">Nomor HP / WhatsApp : <span style="color: red;">*</span></label>
                <input type="tel" id="no_hp" name="no_hp" placeholder="Contoh: 081234567890" required value="<?php echo htmlspecialchars($_POST['no_hp'] ?? ''); ?>">
            </p>

            <p>
                <label for="jk">Jenis Kelamin :</label>
                <select id="jk" name="jk" required>
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="L" <?php echo (($_POST['jk'] ?? '') === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo (($_POST['jk'] ?? '') === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </p>

            <p>
                <label for="alamat">Alamat Domisili :</label>
                <textarea id="alamat" name="alamat" rows="3" placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten"><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
            </p>

            <p>
                <label for="password">Password Akun : <span style="color: red;">*</span></label>
                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
            </p>

            <p>
                <label for="konfirmasi_password">Konfirmasi Password : <span style="color: red;">*</span></label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ulangi password" required>
            </p>

            <p style="margin-top: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="submit" style="flex: 2; min-width: 180px;">[ Daftar Sekarang ]</button>
                <a href="index.php" class="btn btn-secondary" style="flex: 1; min-width: 140px; padding: 0.6rem 1rem; text-align: center;">[ Batal ]</a>
            </p>
        </form>

        <div class="auth-footer">
            <p>Sudah terdaftar sebagai anggota? <a href="login.php">Masuk di sini</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #888;">
                <a href="index.php">&larr; Kembali ke Beranda Tamu</a>
            </p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>