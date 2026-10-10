<?php
$page_title = "Beranda";
include __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$totalBuku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
$totalAnggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
?>
<section>
    <h2>Selamat Datang di Sistem Perpustakaan Mini</h2>
    <p style="margin-top: 0.5rem; font-size: 0.95rem; color: #555;">
        <strong>Jobsheet 8:</strong> Koneksi PostgreSQL.
    </p>
</section>

<?php if ($flash): ?>
    <section>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    </section>
<?php endif; ?>

<section>
    <h2>Ringkasan</h2>
    <article>
        <h3>Total Buku</h3>
        <p><?php echo $totalBuku; ?></p>
    </article>
    <article>
        <h3>Total Anggota</h3>
        <p><?php echo $totalAnggota; ?></p>
    </article>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>