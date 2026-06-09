<?php
/**
 * Form Tambah/Edit Buku
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
$isEdit = false;
$buku = ['judul'=>'','pengarang'=>'','penerbit'=>'','tahun_terbit'=>'','isbn'=>'','kategori'=>'','stok'=>0,'deskripsi'=>''];

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $buku = $result->fetch_assoc();
        $isEdit = true;
    } else {
        header('Location: buku.php');
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $pengarang = trim($_POST['pengarang'] ?? '');
    $penerbit = trim($_POST['penerbit'] ?? '');
    $tahun = $_POST['tahun_terbit'] ?? null;
    $isbn = trim($_POST['isbn'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $stok = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if (empty($judul) || empty($pengarang)) {
        $error = 'Judul dan pengarang wajib diisi!';
    } else {
        if ($isEdit) {
            $stmt = $conn->prepare("UPDATE buku SET judul=?, pengarang=?, penerbit=?, tahun_terbit=?, isbn=?, kategori=?, stok=?, deskripsi=? WHERE id=?");
            $stmt->bind_param("ssssssisi", $judul, $pengarang, $penerbit, $tahun, $isbn, $kategori, $stok, $deskripsi, $id);
            $msg = 'Buku berhasil diperbarui!';
        } else {
            $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, kategori, stok, deskripsi) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssis", $judul, $pengarang, $penerbit, $tahun, $isbn, $kategori, $stok, $deskripsi);
            $msg = 'Buku berhasil ditambahkan!';
        }
        if ($stmt->execute()) {
            setFlash('success', $msg);
            header('Location: buku.php');
            exit();
        } else {
            $error = 'Gagal menyimpan data: ' . $conn->error;
        }
    }
    $buku = compact('judul','pengarang','penerbit','tahun','isbn','kategori','stok','deskripsi');
    $buku['tahun_terbit'] = $tahun;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Buku - <?= APP_NAME ?></title>
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
            <a href="buku.php" class="active"><i class="fas fa-book"></i> Kelola Buku</a>
            <a href="anggota.php"><i class="fas fa-users"></i> Kelola Anggota</a>
            <a href="transaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a>
            <div class="menu-label">Lainnya</div>
            <a href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> Buku</h1>
            <a href="buku.php" class="btn btn-sm btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
        <div class="content-body">
            <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>

            <div class="form-card">
                <h3><i class="fas fa-book"></i> Data Buku</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Judul Buku *</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($buku['judul']) ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Pengarang *</label>
                            <input type="text" name="pengarang" class="form-control" value="<?= htmlspecialchars($buku['pengarang']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Penerbit</label>
                            <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($buku['penerbit']) ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tahun Terbit</label>
                            <input type="number" name="tahun_terbit" class="form-control" value="<?= $buku['tahun_terbit'] ?>" min="1900" max="2099">
                        </div>
                        <div class="form-group">
                            <label>ISBN</label>
                            <input type="text" name="isbn" class="form-control" value="<?= htmlspecialchars($buku['isbn'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Kategori</label>
                            <input type="text" name="kategori" class="form-control" value="<?= htmlspecialchars($buku['kategori']) ?>" placeholder="Novel, Pelajaran, dll">
                        </div>
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" value="<?= $buku['stok'] ?>" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($buku['deskripsi']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $isEdit ? 'Perbarui' : 'Simpan' ?> Buku
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
<script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>
</html>
