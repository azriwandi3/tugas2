<?php
/**
 * Form Tambah Transaksi Peminjaman (Admin)
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$siswa = $conn->query("SELECT id, nama_lengkap, kelas FROM users WHERE role='siswa' ORDER BY nama_lengkap");
$buku = $conn->query("SELECT id, judul, stok FROM buku WHERE stok > 0 ORDER BY judul");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user = (int)$_POST['id_user'];
    $id_buku = (int)$_POST['id_buku'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $durasi = (int)($_POST['durasi'] ?? DURASI_PINJAM);
    $tgl_kembali = date('Y-m-d', strtotime($tgl_pinjam . " +$durasi days"));

    if (!$id_user || !$id_buku || !$tgl_pinjam) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek stok
        $stok = $conn->prepare("SELECT stok FROM buku WHERE id=?");
        $stok->bind_param("i", $id_buku);
        $stok->execute();
        $s = $stok->get_result()->fetch_assoc();
        if (!$s || $s['stok'] <= 0) {
            $error = 'Stok buku habis!';
        } else {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO peminjaman (id_user,id_buku,tgl_pinjam,tgl_kembali,status) VALUES(?,?,?,?,'dipinjam')");
                $stmt->bind_param("iiss", $id_user, $id_buku, $tgl_pinjam, $tgl_kembali);
                $stmt->execute();

                $upd = $conn->prepare("UPDATE buku SET stok=stok-1 WHERE id=?");
                $upd->bind_param("i", $id_buku);
                $upd->execute();

                $conn->commit();
                setFlash('success', 'Transaksi peminjaman berhasil ditambahkan!');
                header('Location: transaksi.php');
                exit();
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Gagal membuat transaksi: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - <?= APP_NAME ?></title>
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
            <h1><i class="fas fa-plus"></i> Tambah Transaksi</h1>
            <a href="transaksi.php" class="btn btn-sm btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="content-body">
            <?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
            <div class="form-card">
                <h3><i class="fas fa-exchange-alt"></i> Data Peminjaman</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Peminjam (Siswa) *</label>
                        <select name="id_user" class="form-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php while ($s = $siswa->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nama_lengkap']) ?> (<?= $s['kelas'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Buku *</label>
                        <select name="id_buku" class="form-control" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php while ($b = $buku->fetch_assoc()): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tanggal Pinjam *</label>
                            <input type="date" name="tgl_pinjam" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Durasi (hari)</label>
                            <input type="number" name="durasi" class="form-control" value="<?= DURASI_PINJAM ?>" min="1" max="30">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </main>
</div>
<script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>
</html>
