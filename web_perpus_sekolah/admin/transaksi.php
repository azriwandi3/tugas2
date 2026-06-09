<?php
/**
 * Kelola Transaksi Peminjaman
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = "1=1";
$params = []; $types = "";
if ($status) { $where .= " AND p.status=?"; $params[] = $status; $types .= "s"; }
if ($search) { $where .= " AND (u.nama_lengkap LIKE ? OR b.judul LIKE ?)"; $s="%$search%"; $params[]=$s; $params[]=$s; $types .= "ss"; }

$sql = "SELECT p.*, u.nama_lengkap, u.kelas, b.judul FROM peminjaman p JOIN users u ON p.id_user=u.id JOIN buku b ON p.id_buku=b.id WHERE $where ORDER BY p.created_at DESC";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$trans = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header"><i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i><h3><?= APP_NAME ?></h3><small>Panel Admin</small></div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="buku.php"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="transaksi.php" class="active"><i class="fas fa-exchange-alt"></i> Transaksi</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-exchange-alt"></i> Transaksi Peminjaman</h1>
            <a href="transaksi_form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Transaksi</a>
        </div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['message'] ?></div>
            <?php endif; ?>

            <!-- Filter -->
            <div style="display:flex;gap:8px;margin-bottom:1.5rem;flex-wrap:wrap;">
                <a href="transaksi.php" class="btn btn-sm <?= !$status ? 'btn-primary' : 'btn-outline' ?>">Semua</a>
                <a href="transaksi.php?status=dipinjam" class="btn btn-sm <?= $status=='dipinjam' ? 'btn-warning' : 'btn-outline' ?>">Dipinjam</a>
                <a href="transaksi.php?status=dikembalikan" class="btn btn-sm <?= $status=='dikembalikan' ? 'btn-success' : 'btn-outline' ?>">Dikembalikan</a>
                <a href="transaksi.php?status=terlambat" class="btn btn-sm <?= $status=='terlambat' ? 'btn-danger' : 'btn-outline' ?>">Terlambat</a>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h3>Data Transaksi (<?= $trans->num_rows ?>)</h3>
                    <form method="GET"><div class="search-box"><i class="fas fa-search" style="color:var(--text-light);"></i><input type="text" name="search" placeholder="Cari nama atau judul..." value="<?= htmlspecialchars($search) ?>"></div></form>
                </div>
                <table class="data-table">
                    <thead><tr><th>No</th><th>Peminjam</th><th>Buku</th><th>Tgl Pinjam</th><th>Tgl Kembali</th><th>Status</th><th>Denda</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $no=1; while ($t = $trans->fetch_assoc()): ?>
                        <?php
                            $isLate = $t['status']=='dipinjam' && strtotime($t['tgl_kembali']) < time();
                            $daysLate = $isLate ? floor((time()-strtotime($t['tgl_kembali']))/86400) : 0;
                            $denda = $isLate ? $daysLate * DENDA_PER_HARI : $t['denda'];
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($t['nama_lengkap']) ?></strong><br><small style="color:var(--text-light);"><?= $t['kelas'] ?></small></td>
                            <td><?= htmlspecialchars($t['judul']) ?></td>
                            <td><?= formatTanggal($t['tgl_pinjam']) ?></td>
                            <td><?= formatTanggal($t['tgl_kembali']) ?></td>
                            <td>
                                <?php if ($t['status'] == 'dikembalikan'): ?>
                                    <span class="badge badge-success">Dikembalikan</span>
                                <?php elseif ($isLate): ?>
                                    <span class="badge badge-danger">Terlambat <?= $daysLate ?> hari</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Dipinjam</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $denda > 0 ? formatRupiah($denda) : '-' ?></td>
                            <td>
                                <?php if ($t['status'] == 'dipinjam'): ?>
                                <a href="transaksi_proses.php?id=<?= $t['id'] ?>&action=kembali" class="btn btn-sm btn-success" onclick="return confirm('Konfirmasi pengembalian buku?')">
                                    <i class="fas fa-check"></i> Kembalikan
                                </a>
                                <?php else: ?>
                                <span style="color:var(--text-light);font-size:0.85rem;">
                                    <?= $t['tgl_dikembalikan'] ? formatTanggal($t['tgl_dikembalikan']) : '-' ?>
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($trans->num_rows == 0): ?>
                        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-exchange-alt"></i><h3>Belum ada transaksi</h3></div></td></tr>
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
