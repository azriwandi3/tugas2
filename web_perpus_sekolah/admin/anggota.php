<?php
/**
 * Kelola Anggota - List Data Siswa
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$search = $_GET['search'] ?? '';
$where = "role='siswa'";
$params = []; $types = "";

if ($search) {
    $where .= " AND (nama_lengkap LIKE ? OR username LIKE ? OR kelas LIKE ?)";
    $s = "%$search%"; $params = [$s,$s,$s]; $types = "sss";
}

$stmt = $conn->prepare("SELECT * FROM users WHERE $where ORDER BY created_at DESC");
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$members = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-book-open-reader" style="font-size:2rem;color:var(--accent);"></i>
            <h3><?= APP_NAME ?></h3><small>Panel Admin</small>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="buku.php"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="anggota.php" class="active"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="transaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-users"></i> Kelola Anggota</h1>
            <a href="anggota_form.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Anggota</a>
        </div>
        <div class="content-body">
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><i class="fas fa-info-circle"></i> <?= $flash['message'] ?></div>
            <?php endif; ?>

            <div class="table-container">
                <div class="table-header">
                    <h3>Daftar Anggota (<?= $members->num_rows ?>)</h3>
                    <form method="GET"><div class="search-box"><i class="fas fa-search" style="color:var(--text-light);"></i><input type="text" name="search" placeholder="Cari nama, username, kelas..." value="<?= htmlspecialchars($search) ?>"></div></form>
                </div>
                <table class="data-table">
                    <thead><tr><th>No</th><th>Nama Lengkap</th><th>Username</th><th>Kelas</th><th>Telepon</th><th>Tgl Daftar</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $no=1; while ($m = $members->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($m['nama_lengkap']) ?></strong></td>
                            <td><?= htmlspecialchars($m['username']) ?></td>
                            <td><span class="badge badge-info"><?= $m['kelas'] ?: '-' ?></span></td>
                            <td><?= $m['no_telepon'] ?: '-' ?></td>
                            <td><?= formatTanggal($m['created_at']) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="anggota_form.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete('anggota_hapus.php?id=<?= $m['id'] ?>','<?= addslashes($m['nama_lengkap']) ?>')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($members->num_rows == 0): ?>
                        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-users"></i><h3>Belum ada anggota</h3></div></td></tr>
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
