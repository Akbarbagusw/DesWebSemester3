<?php
$page_title = "Daftar Buku";
include __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/koneksi.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
$keyword = trim($_GET['q'] ?? '');

$perPage = 10;
$requestedPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = max(1, $requestedPage ?: 1);

if ($keyword !== '') {
    $searchPattern = '%' . $keyword . '%';
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE judul ILIKE :judul OR pengarang ILIKE :pengarang");
    $countStmt->execute([
        'judul' => $searchPattern,
        'pengarang' => $searchPattern,
    ]);
    $totalRows = (int) $countStmt->fetchColumn();

    $stmt = $pdo->prepare(
        "SELECT * FROM buku
         WHERE judul ILIKE :judul OR pengarang ILIKE :pengarang
         ORDER BY id DESC
         LIMIT :limit OFFSET :offset"
    );
    $stmt->bindValue('judul', $searchPattern, PDO::PARAM_STR);
    $stmt->bindValue('pengarang', $searchPattern, PDO::PARAM_STR);
} else {
    $totalRows = (int) $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $stmt = $pdo->prepare("SELECT * FROM buku ORDER BY id DESC LIMIT :limit OFFSET :offset");
}

$totalPages = max(1, (int) ceil($totalRows / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;
$stmt->bindValue('limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue('offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$daftarBuku = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<section>
    <h2>Daftar Buku</h2>

    <?php if ($flash): ?>
        <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo htmlspecialchars($flash['pesan']); ?></p>
    <?php endif; ?>

    <form method="get" action="list.php" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
        <div class="search-box" style="flex: 1; min-width: 250px; display: flex; gap: 0.5rem; align-items: flex-end;">
            <div style="flex: 1;">
                <label for="search-input">Cari Judul / Pengarang</label>
                <input type="text" id="search-input" name="q" placeholder="Ketik judul atau pengarang..." value="<?php echo htmlspecialchars($keyword); ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.55rem 1rem;">Cari</button>
            <?php if ($keyword !== ''): ?>
                <a href="list.php" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Reset Filter</a>
            <?php endif; ?>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="tambah.php" class="btn btn-primary">+ Tambah Buku</a>
        </div>
    </form>

    <?php if ($keyword !== ''): ?>
        <p style="margin-bottom: 0.85rem; font-size: 0.95rem; color: #444;">
            Hasil pencarian untuk judul atau pengarang: <strong>"<?php echo htmlspecialchars($keyword); ?>"</strong>
            (ditemukan <?php echo $totalRows; ?> buku) &mdash;
            <a href="list.php">Tampilkan Semua</a>
        </p>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>ISBN</th>
                    <th>Tahun</th>
                    <th>Stok</th>
                    <th>Ditambahkan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($daftarBuku)): ?>
                    <tr>
                        <td colspan="7">Belum ada data buku. Silakan tambah lewat menu "Tambah Buku".</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($daftarBuku as $buku): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($buku['judul']); ?></strong>
                                <?php if (!empty($buku['kategori'])): ?>
                                    <span style="display: block; font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($buku['kategori']); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($buku['pengarang']); ?></td>
                            <td><code><?php echo htmlspecialchars(!empty($buku['isbn']) ? $buku['isbn'] : '-'); ?></code></td>
                            <td><?php echo htmlspecialchars($buku['tahun']); ?></td>
                            <td><?php echo htmlspecialchars($buku['stok']); ?></td>
                            <td>
                                <small style="color: #666;">
                                    <?php 
                                    if (!empty($buku['tanggal_ditambahkan'])) {
                                        echo htmlspecialchars(date('d/m/Y H:i', strtotime($buku['tanggal_ditambahkan'])));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </small>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                    <a href="ubah.php?id=<?php echo (int) $buku['id']; ?>" class="btn btn-secondary" style="padding: 0.45rem 0.75rem;">Edit</a>
                                    <form class="form-hapus" action="hapus.php" method="post" style="display: inline; margin: 0;">
                                        <input type="hidden" name="id" value="<?php echo (int) $buku['id']; ?>">
                                        <button type="submit" class="btn-hapus">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Navigasi halaman buku">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                $pageParams = ['page' => $i];
                if ($keyword !== '') {
                    $pageParams['q'] = $keyword;
                }
                ?>
                <a href="list.php?<?php echo htmlspecialchars(http_build_query($pageParams)); ?>"
                   class="<?php echo $i === $page ? 'active' : ''; ?>"
                   <?php echo $i === $page ? 'aria-current="page"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>