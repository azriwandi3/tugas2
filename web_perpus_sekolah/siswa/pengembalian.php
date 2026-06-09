<?php
/**
 * Pengembalian Buku - Siswa
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireSiswa();

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.*, b.judul, b.pengarang, b.kategori FROM peminjaman p JOIN buku b ON p.id_buku=b.id WHERE p.id_user=? AND p.status='dipinjam' ORDER BY p.tgl_kembali ASC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$pinjam = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - <?= APP_NAME ?></title>
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
            <a href="katalog.php"><i class="fas fa-book"></i> Katalog Buku</a>
            <a href="pengembalian.php" class="active"><i class="fas fa-undo"></i> Pengembalian</a>
            <a href="riwayat.php"><i class="fas fa-history"></i> Riwayat</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="content-header"><h1><i class="fas fa-undo"></i> Pengembalian Buku</h1></div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['message'] ?></div>
            <?php endif; ?>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Untuk mengembalikan buku, silakan bawa buku ke perpustakaan. Admin akan memproses pengembalian Anda. Berikut daftar buku yang sedang Anda pinjam:
            </div>

            <div class="table-container">
                <div class="table-header"><h3>Buku yang Dipinjam (<?= $pinjam->num_rows ?>)</h3></div>
                <table class="data-table">
                    <thead><tr><th>No</th><th>Judul Buku</th><th>Pengarang</th><th>Kategori</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php $no=1; while ($p = $pinjam->fetch_assoc()): ?>
                        <?php
                            $isLate = strtotime($p['tgl_kembali']) < time();
                            $daysLeft = floor((strtotime($p['tgl_kembali'])-time())/86400);
                            $daysLate = $isLate ? abs($daysLeft) : 0;
                            $denda = $isLate ? $daysLate * DENDA_PER_HARI : 0;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($p['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($p['pengarang']) ?></td>
                            <td><span class="badge badge-primary"><?= $p['kategori'] ?></span></td>
                            <td><?= formatTanggal($p['tgl_pinjam']) ?></td>
                            <td><?= formatTanggal($p['tgl_kembali']) ?></td>
                            <td>
                                <?php if ($isLate): ?>
                                    <span class="badge badge-danger">Terlambat <?= $daysLate ?> hari</span>
                                    <br><small style="color:var(--danger);font-weight:600;">Denda: <?= formatRupiah($denda) ?></small>
                                <?php elseif ($daysLeft <= 2): ?>
                                    <span class="badge badge-warning"><?= $daysLeft ?> hari lagi</span>
                                <?php else: ?>
                                    <span class="badge badge-info"><?= $daysLeft ?> hari lagi</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($pinjam->num_rows == 0): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-check-circle"></i><h3>Tidak ada buku yang perlu dikembalikan</h3><p>Semua buku sudah dikembalikan 🎉</p></div></td></tr>
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
