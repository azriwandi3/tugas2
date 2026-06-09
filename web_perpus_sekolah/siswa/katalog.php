<?php
/**
 * Katalog Buku - Browse & Pinjam
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireSiswa();

$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$where = "1=1"; $params = []; $types = "";
if ($search) { $where .= " AND (judul LIKE ? OR pengarang LIKE ?)"; $s="%$search%"; $params[]=$s; $params[]=$s; $types.="ss"; }
if ($kategori) { $where .= " AND kategori=?"; $params[]=$kategori; $types.="s"; }

$stmt = $conn->prepare("SELECT * FROM buku WHERE $where ORDER BY judul ASC");
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$books = $stmt->get_result();

$categories = $conn->query("SELECT DISTINCT kategori FROM buku ORDER BY kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i><h3><?= APP_NAME ?></h3><small>Panel Siswa</small></div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu</div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="katalog.php" class="active"><i class="fas fa-book"></i> Katalog Buku</a>
            <a href="pengembalian.php"><i class="fas fa-undo"></i> Pengembalian</a>
            <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-book"></i> Katalog Buku</h1>
        </div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['message'] ?></div>
            <?php endif; ?>

            <!-- Search & Filter -->
            <form method="GET" style="display:flex;gap:12px;margin-bottom:2rem;flex-wrap:wrap;align-items:center;">
                <div class="search-box" style="flex:1;min-width:250px;">
                    <i class="fas fa-search" style="color:var(--text-light);"></i>
                    <input type="text" name="search" placeholder="Cari judul atau pengarang..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <select name="kategori" class="form-control" style="width:auto;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                    <option value="<?= $c['kategori'] ?>" <?= $kategori==$c['kategori']?'selected':'' ?>><?= $c['kategori'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
            </form>

            <!-- Books Grid -->
            <div class="books-grid">
                <?php while ($b = $books->fetch_assoc()): ?>
                <div class="book-card">
                    <div class="book-cover" style="background:linear-gradient(135deg, hsl(<?= crc32($b['judul'])%360 ?>,60%,45%), hsl(<?= (crc32($b['judul'])%360+40) ?>,70%,55%));">
                        <i class="fas fa-book"></i>
                        <span class="kategori-badge"><?= htmlspecialchars($b['kategori']) ?></span>
                    </div>
                    <div class="book-info">
                        <h4><?= htmlspecialchars($b['judul']) ?></h4>
                        <p class="author"><i class="fas fa-user-pen"></i> <?= htmlspecialchars($b['pengarang']) ?></p>
                        <div class="book-meta">
                            <span class="stock <?= $b['stok']>0?'available':'empty' ?>">
                                Stok: <?= $b['stok'] ?>
                            </span>
                            <?php if ($b['stok'] > 0): ?>
                            <a href="pinjam.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-primary" onclick="return confirm('Pinjam buku ini?')">
                                <i class="fas fa-hand-holding-heart"></i> Pinjam
                            </a>
                            <?php else: ?>
                            <span class="btn btn-sm" style="background:#eee;color:#999;cursor:not-allowed;">Habis</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php if ($books->num_rows == 0): ?>
            <div class="empty-state"><i class="fas fa-book"></i><h3>Buku tidak ditemukan</h3><p>Coba kata kunci lain</p></div>
            <?php endif; ?>
        </div>
    </main>
</div>
<script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>
</html>
