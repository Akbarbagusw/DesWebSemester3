<?php
$page_title = "Debug Session";
include __DIR__ . '/includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<section>
    <h2>Debug Session ($_SESSION)</h2>
    <p>Halaman ini menampilkan isi mentah dari variabel superglobal <code>$_SESSION</code> untuk memantau data yang sebenarnya tersimpan di memori server selama proses form handling dan navigasi.</p>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>" style="margin-top: 1rem;"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <div style="margin: 1.25rem 0; display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="reset_session.php" class="btn btn-hapus" onclick="return confirm('Apakah Anda yakin ingin mereset seluruh data di $_SESSION?');">Reset Data Session</a>
    </div>

    <div style="background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 0.5rem; font-size: 1rem; color: #333;">Informasi Status Sesi Server:</h3>
        <ul style="margin-left: 1.25rem; font-size: 0.92rem; line-height: 1.6;">
            <li><strong>Session ID:</strong> <code><?php echo session_id() ?: '-'; ?></code></li>
            <li><strong>Session Name:</strong> <code><?php echo session_name(); ?></code></li>
            <li><strong>Total Buku tersimpan:</strong> <?php echo count($_SESSION['buku'] ?? []); ?> buku</li>
            <li><strong>Total Anggota tersimpan:</strong> <?php echo count($_SESSION['anggota'] ?? []); ?> anggota</li>
        </ul>
    </div>

    <h3>Output Mentah <code>print_r($_SESSION)</code>:</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    <h3 style="margin-top: 1.5rem;">Output Format JSON:</h3>
    <pre><?php echo htmlspecialchars(json_encode($_SESSION, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)); ?></pre>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
