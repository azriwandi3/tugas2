<?php
/**
 * Admin Dashboard - Perpustakaan Sekolah Digital
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$pageTitle = 'Dashboard Admin';

// Statistics
$totalBuku = $conn->query("SELECT COUNT(*) as t FROM buku")->fetch_assoc()['t'];
$totalAnggota = $conn->query("SELECT COUNT(*) as t FROM users WHERE role='siswa'")->fetch_assoc()['t'];
$bukuDipinjam = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE status='dipinjam'")->fetch_assoc()['t'];
$kembaliHariIni = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE tgl_dikembalikan = CURDATE()")->fetch_assoc()['t'];

// Recent transactions
$recentTrans = $conn->query("SELECT p.*, u.nama_lengkap, b.judul FROM peminjaman p 
    JOIN users u ON p.id_user = u.id 
    JOIN buku b ON p.id_buku = b.id 
    ORDER BY p.created_at DESC LIMIT 10");

// Overdue books
$overdue = $conn->query("SELECT COUNT(*) as t FROM peminjaman WHERE status='dipinjam' AND tgl_kembali < CURDATE()")->fetch_assoc()['t'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i>
            <h3><?= APP_NAME ?></h3>
            <small>Panel Admin</small>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="buku.php"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="transaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/" target="_blank"><i class="fas fa-globe"></i> Lihat Website</a>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-header">
            <div>
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            </div>
            <button class="btn btn-sm btn-outline" onclick="toggleSidebar()" style="display:none;" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>">
                <i class="fas fa-info-circle"></i> <?= $flash['message'] ?>
            </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="dashboard-stats">
                <div class="dash-card animate-fade-up">
                    <div class="dash-icon bg-blue"><i class="fas fa-book"></i></div>
                    <h3><?= $totalBuku ?></h3>
                    <p>Total Koleksi Buku</p>
                </div>
                <div class="dash-card animate-fade-up delay-1">
                    <div class="dash-icon bg-teal"><i class="fas fa-users"></i></div>
                    <h3><?= $totalAnggota ?></h3>
                    <p>Total Anggota</p>
                </div>
                <div class="dash-card animate-fade-up delay-2">
                    <div class="dash-icon bg-orange"><i class="fas fa-hand-holding-heart"></i></div>
                    <h3><?= $bukuDipinjam ?></h3>
                    <p>Buku Dipinjam</p>
                </div>
                <div class="dash-card animate-fade-up delay-3">
                    <div class="dash-icon bg-green"><i class="fas fa-calendar-check"></i></div>
                    <h3><?= $kembaliHariIni ?></h3>
                    <p>Kembali Hari Ini</p>
                </div>
            </div>

            <?php if ($overdue > 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong><?= $overdue ?> buku</strong> sudah melewati batas pengembalian!
                <a href="transaksi.php" style="margin-left:auto;font-weight:600;">Lihat Detail →</a>
            </div>
            <?php endif; ?>

            <!-- Recent Transactions -->
            <div class="table-container">
                <div class="table-header">
                    <h3><i class="fas fa-clock"></i> Transaksi Terbaru</h3>
                    <a href="transaksi.php" class="btn btn-sm btn-outline">Lihat Semua</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($recentTrans->num_rows > 0): ?>
                        <?php while ($t = $recentTrans->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($t['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($t['judul']) ?></td>
                            <td><?= formatTanggal($t['tgl_pinjam']) ?></td>
                            <td><?= formatTanggal($t['tgl_kembali']) ?></td>
                            <td>
                                <?php if ($t['status'] == 'dipinjam'): ?>
                                    <?php if (strtotime($t['tgl_kembali']) < time()): ?>
                                        <span class="badge badge-danger">Terlambat</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge badge-success">Dikembalikan</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr><td colspan="5" class="empty-state">Belum ada transaksi</td></tr>
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
