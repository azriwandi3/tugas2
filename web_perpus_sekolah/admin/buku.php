<?php
/**
 * Kelola Buku - List Data Buku
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$where = "1=1";
$params = [];
$types = "";

if ($search) {
    $where .= " AND (judul LIKE ? OR pengarang LIKE ? OR isbn LIKE ?)";
    $s = "%$search%";
    $params = array_merge($params, [$s, $s, $s]);
    $types .= "sss";
}
if ($kategori) {
    $where .= " AND kategori = ?";
    $params[] = $kategori;
    $types .= "s";
}

$stmt = $conn->prepare("SELECT * FROM buku WHERE $where ORDER BY created_at DESC");
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$books = $stmt->get_result();

$categories = $conn->query("SELECT DISTINCT kategori FROM buku ORDER BY kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i>
            <h3><?= APP_NAME ?></h3>
            <small>Panel Admin</small>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="buku.php" class="active"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="transaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-book"></i> Kelola Buku</h1>
            <a href="buku_form.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Buku
            </a>
        </div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <i class="fas fa-info-circle"></i> <?= $flash['message'] ?>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h3>Daftar Buku (<?= $books->num_rows ?> buku)</h3>
                    <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                        <div class="search-box">
                            <i class="fas fa-search" style="color:var(--text-light);"></i>
                            <input type="text" name="search" placeholder="Cari judul, pengarang..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <select name="kategori" class="form-control" style="width:auto;padding:8px 12px;" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php while ($c = $categories->fetch_assoc()): ?>
                            <option value="<?= $c['kategori'] ?>" <?= $kategori == $c['kategori'] ? 'selected' : '' ?>><?= $c['kategori'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </form>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while ($b = $books->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($b['judul']) ?></strong><br><small style="color:var(--text-light);">ISBN: <?= $b['isbn'] ?: '-' ?></small></td>
                            <td><?= htmlspecialchars($b['pengarang']) ?></td>
                            <td><span class="badge badge-primary"><?= $b['kategori'] ?></span></td>
                            <td><?= $b['tahun_terbit'] ?></td>
                            <td><span class="badge <?= $b['stok'] > 0 ? 'badge-success' : 'badge-danger' ?>"><?= $b['stok'] ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="buku_form.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete('buku_hapus.php?id=<?= $b['id'] ?>', '<?= addslashes($b['judul']) ?>')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($books->num_rows == 0): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-book"></i><h3>Belum ada data buku</h3><p>Klik tombol "Tambah Buku" untuk menambahkan</p></div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>
</html>
