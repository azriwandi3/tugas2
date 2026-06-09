<?php
/**
 * Dashboard Siswa
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireSiswa();

$userId = $_SESSION['user_id'];

// Buku yang sedang dipinjam
$dipinjam = $conn->prepare("SELECT p.*, b.judul, b.pengarang, b.kategori FROM peminjaman p JOIN buku b ON p.id_buku=b.id WHERE p.id_user=? AND p.status='dipinjam' ORDER BY p.tgl_kembali ASC");
$dipinjam->bind_param("i", $userId);
$dipinjam->execute();
$pinjamResult = $dipinjam->get_result();

$totalPinjam = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=?");
$totalPinjam->bind_param("i", $userId);
$totalPinjam->execute();
$totalP = $totalPinjam->get_result()->fetch_assoc()['t'];

$sedangPinjam = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=? AND status='dipinjam'");
$sedangPinjam->bind_param("i", $userId);
$sedangPinjam->execute();
$sedangP = $sedangPinjam->get_result()->fetch_assoc()['t'];

$selesai = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=? AND status='dikembalikan'");
$selesai->bind_param("i", $userId);
$selesai->execute();
$selesaiP = $selesai->get_result()->fetch_assoc()['t'];

$totalBuku = $conn->query("SELECT COUNT(*) as t FROM buku")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i>
            <h3><?= APP_NAME ?></h3><small>Panel Siswa</small>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu</div>
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="katalog.php"><i class="fas fa-book"></i> Katalog Buku</a>
            <a href="pengembalian.php"><i class="fas fa-undo"></i> Pengembalian</a>
            <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/" target="_blank"><i class="fas fa-globe"></i> Beranda</a>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'],0,1)) ?></div>
                <div><div class="user-name"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div><div class="user-role">Siswa</div></div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <span style="color:var(--text-light);">Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>!</span>
        </div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['message'] ?></div>
            <?php endif; ?>

            <div class="dashboard-stats">
                <div class="dash-card"><div class="dash-icon bg-blue"><i class="fas fa-book"></i></div><h3><?= $totalBuku ?></h3><p>Total Buku Tersedia</p></div>
                <div class="dash-card"><div class="dash-icon bg-orange"><i class="fas fa-hand-holding-heart"></i></div><h3><?= $sedangP ?></h3><p>Sedang Dipinjam</p></div>
                <div class="dash-card"><div class="dash-icon bg-green"><i class="fas fa-check-circle"></i></div><h3><?= $selesaiP ?></h3><p>Sudah Dikembalikan</p></div>
                <div class="dash-card"><div class="dash-icon bg-teal"><i class="fas fa-chart-bar"></i></div><h3><?= $totalP ?></h3><p>Total Peminjaman</p></div>
            </div>

            <!-- Buku yang sedang dipinjam -->
            <div class="table-container">
                <div class="table-header">
                    <h3><i class="fas fa-clock"></i> Buku yang Sedang Dipinjam</h3>
                    <a href="katalog.php" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Pinjam Buku</a>
                </div>
                <table class="data-table">
                    <thead><tr><th>Judul</th><th>Pengarang</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php while ($p = $pinjamResult->fetch_assoc()): ?>
                        <?php $isLate = strtotime($p['tgl_kembali']) < time(); $daysLeft = floor((strtotime($p['tgl_kembali'])-time())/86400); ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($p['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($p['pengarang']) ?></td>
                            <td><?= formatTanggal($p['tgl_pinjam']) ?></td>
                            <td><?= formatTanggal($p['tgl_kembali']) ?></td>
                            <td>
                                <?php if ($isLate): ?>
                                    <span class="badge badge-danger">Terlambat!</span>
                                <?php elseif ($daysLeft <= 2): ?>
                                    <span class="badge badge-warning">Segera (<?= $daysLeft ?> hari)</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= $daysLeft ?> hari lagi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($pinjamResult->num_rows == 0): ?>
                        <tr><td colspan="5"><div class="empty-state"><i class="fas fa-book-open"></i><h3>Tidak ada buku yang dipinjam</h3><p><a href="katalog.php">Jelajahi katalog buku →</a></p></div></td></tr>
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
