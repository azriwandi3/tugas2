<?php
/**
 * Riwayat Peminjaman - Siswa
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireSiswa();

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT p.*, b.judul, b.pengarang, b.kategori FROM peminjaman p JOIN buku b ON p.id_buku=b.id WHERE p.id_user=? ORDER BY p.created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$riwayat = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - <?= APP_NAME ?></title>
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
            <a href="pengembalian.php"><i class="fas fa-undo"></i> Pengembalian</a>
            <a href="riwayat.php" class="active"><i class="fas fa-history"></i> Riwayat</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="content-header"><h1><i class="fas fa-history"></i> Riwayat Peminjaman</h1></div>
        <div class="content-body">
            <div class="table-container">
                <div class="table-header"><h3>Semua Riwayat (<?= $riwayat->num_rows ?>)</h3></div>
                <table class="data-table">
                    <thead><tr><th>No</th><th>Judul</th><th>Pengarang</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Tgl Dikembalikan</th><th>Status</th><th>Denda</th></tr></thead>
                    <tbody>
                        <?php $no=1; while ($r = $riwayat->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($r['judul']) ?></strong></td>
                            <td><?= htmlspecialchars($r['pengarang']) ?></td>
                            <td><?= formatTanggal($r['tgl_pinjam']) ?></td>
                            <td><?= formatTanggal($r['tgl_kembali']) ?></td>
                            <td><?= $r['tgl_dikembalikan'] ? formatTanggal($r['tgl_dikembalikan']) : '-' ?></td>
                            <td>
                                <?php if ($r['status'] == 'dikembalikan'): ?>
                                    <span class="badge badge-success">Dikembalikan</span>
                                <?php elseif (strtotime($r['tgl_kembali']) < time()): ?>
                                    <span class="badge badge-danger">Terlambat</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Dipinjam</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $r['denda'] > 0 ? formatRupiah($r['denda']) : '-' ?></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($riwayat->num_rows == 0): ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-history"></i><h3>Belum ada riwayat peminjaman</h3></div></td></tr>
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
