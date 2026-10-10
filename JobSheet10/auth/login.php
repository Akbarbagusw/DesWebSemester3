<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/remember_me.php';
restore_remembered_login();

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$page_title = 'Login Petugas';
$flash = $_SESSION['flash'] ?? null;
if (isset($_SESSION['flash'])) {
    unset($_SESSION['flash']);
}
include __DIR__ . '/../includes/header.php';
?>
<section class="auth-wrapper" style="max-width: 520px; margin: 0 auto;">
    <div class="auth-card" style="padding: 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
        <div class="auth-header" style="margin-bottom: 1rem;">
            <h2 style="margin-bottom: 0.5rem;">Login Petugas</h2>
            <p>Silakan masukkan kredensial untuk mengakses modul transaksi dan data perpustakaan.</p>
        </div>

        <?php if ($flash): ?>
            <p class="flash flash-<?php echo htmlspecialchars($flash['type'] ?? 'error'); ?>" style="margin-bottom: 1rem;">
                <?php echo htmlspecialchars($flash['pesan'] ?? ''); ?>
            </p>
        <?php endif; ?>

        <form action="proses_login.php" method="POST">
            <p>
                <label for="username">Username :</label>
                <input type="text" id="username" name="username" placeholder="Masukkan username petugas" required autofocus>
            </p>

            <p>
                <label for="password">Password :</label>
                <input type="password" id="password" name="password" placeholder="Masukkan password" required>
            </p>

            <p class="auth-remember">
                <label for="remember_me">
                    <input type="checkbox" id="remember_me" name="remember_me" value="1">
                    Ingat Saya
                </label>
            </p>

            <p style="margin-top: 1.25rem;">
                <button type="submit" style="width: 100%;">[ Masuk ]</button>
            </p>
        </form>

        <div class="auth-footer">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.85rem; color: #888;">
                <a href="../index.php">&larr; Kembali ke Beranda Tamu</a>
            </p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
