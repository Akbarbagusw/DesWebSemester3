<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Registrasi Petugas';
$errors = $_SESSION['register_errors'] ?? [];
$old = $_SESSION['register_old'] ?? ['nama' => '', 'username' => ''];
unset($_SESSION['register_errors'], $_SESSION['register_old']);

include __DIR__ . '/../includes/header.php';
?>
<section class="auth-wrapper" style="max-width: 640px; margin: 0 auto;">
    <div class="auth-card" style="padding: 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <div class="auth-header" style="margin-bottom: 1rem;">
            <h2 style="margin-bottom: 0.5rem;">Registrasi Petugas</h2>
            <p>Silakan lengkapi formulir registrasi untuk membuat akun baru.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <ul style="padding-left: 1.25rem; color: #b42318; margin-bottom: 1rem;">
                <?php foreach ($errors as $message): ?>
                    <li><?php echo htmlspecialchars($message); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="proses_register.php" method="POST">
            <p>
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required value="<?php echo htmlspecialchars((string) ($old['nama'] ?? '')); ?>">
            </p>

            <p>
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars((string) ($old['username'] ?? '')); ?>">
            </p>

            <p>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </p>

            <p>
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </p>

            <p style="margin-top: 1rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                <button type="submit" style="flex: 2; min-width: 180px;">Daftar</button>
                <a href="login.php" class="btn btn-secondary" style="flex: 1; min-width: 140px; padding: 0.6rem 1rem; text-align: center;">Batal</a>
            </p>
        </form>

        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
