<?php
require __DIR__ . '/includes/functions.php';

$page_title = 'Login Petugas';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $errors[] = 'Username dan password wajib diisi.';
    }

    if (empty($errors)) {
        $valid = (
            ($username === 'petugas' && $password === '123456') ||
            ($username === 'admin' && $password === 'admin123')
        );

        if ($valid) {
            $_SESSION['petugas'] = [
                'username' => $username,
                'nama' => $username === 'admin' ? 'Administrator' : 'Akbar',
            ];
            flash('success', 'Login berhasil. Selamat datang di dashboard.');
            redirect('index.php');
        }

        $errors[] = 'Username atau password salah.';
    }
}

include __DIR__ . '/includes/header.php';
$success = flash('success');
$error = flash('error');
?>
<section class="auth-wrapper" style="max-width: 520px; margin: 0 auto;">
    <div class="auth-card" style="padding: 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <div class="auth-header" style="margin-bottom: 1rem;">
            <h2 style="margin-bottom: 0.5rem;">Login Petugas</h2>
            <p>Silakan masukkan kredensial untuk mengakses modul transaksi dan data perpustakaan.</p>
        </div>

        <?php if ($success): ?>
            <p class="form-success" style="color: #2e7d32; margin-bottom: 1rem; font-weight: 600;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="form-error" style="margin-bottom: 1rem; font-weight: 600;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <ul style="padding-left: 1.25rem; color: #b42318; margin-bottom: 1rem;">
                <?php foreach ($errors as $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <p>
                <label for="username">Username :</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username petugas" required autofocus value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </p>

            <p>
                <label for="password">Password :</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </p>

            <p style="margin-top: 1.25rem;">
                <button type="submit" style="width: 100%;">[ Masuk ]</button>
            </p>
        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #888;">
                <a href="index.php">&larr; Kembali ke Beranda Tamu</a>
            </p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>