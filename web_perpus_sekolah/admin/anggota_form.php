<?php
/**
 * Form Tambah/Edit Anggota
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
$isEdit = false;
$anggota = ['nama_lengkap' => '', 'username' => '', 'kelas' => '', 'no_telepon' => '', 'alamat' => ''];

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=? AND role='siswa'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows === 1) {
        $anggota = $r->fetch_assoc();
        $isEdit = true;
    } else {
        header('Location: anggota.php');
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $kelas = trim($_POST['kelas'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($username)) {
        $error = 'Nama dan username wajib diisi!';
    } elseif (!$isEdit && empty($password)) {
        $error = 'Password wajib diisi untuk anggota baru!';
    } else {
        // Cek duplikat username
        $check = $conn->prepare("SELECT id FROM users WHERE username=? AND id!=?");
        $checkId = $isEdit ? $id : 0;
        $check->bind_param("si", $username, $checkId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            if ($isEdit) {
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET nama_lengkap=?, username=?, password=?, kelas=?, no_telepon=?, alamat=? WHERE id=?");
                    $stmt->bind_param("ssssssi", $nama, $username, $hashed, $kelas, $telepon, $alamat, $id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET nama_lengkap=?, username=?, kelas=?, no_telepon=?, alamat=? WHERE id=?");
                    $stmt->bind_param("sssssi", $nama, $username, $kelas, $telepon, $alamat, $id);
                }
                $msg = 'Data anggota berhasil diperbarui!';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (nama_lengkap,username,password,role,kelas,no_telepon,alamat) VALUES(?,?,?,'siswa',?,?,?)");
                $stmt->bind_param("ssssss", $nama, $username, $hashed, $kelas, $telepon, $alamat);
                $msg = 'Anggota berhasil ditambahkan!';
            }
            if ($stmt->execute()) {
                setFlash('success', $msg);
                header('Location: anggota.php');
                exit();
            } else {
                $error = 'Gagal menyimpan data.';
            }
        }
    }
    $anggota = compact('nama', 'username', 'kelas', 'telepon', 'alamat');
    $anggota['nama_lengkap'] = $nama;
    $anggota['no_telepon'] = $telepon;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Tambah' ?> Anggota - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <div class="sidebar-header"><i class="fas fa-book-open-reader"
                    style="font-size:2rem;color:var(--accent);"></i>
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
                <h1><i class="fas fa-<?= $isEdit ? 'edit' : 'plus' ?>"></i> <?= $isEdit ? 'Edit' : 'Tambah' ?> Anggota
                </h1>
                <a href="anggota.php" class="btn btn-sm btn-outline"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
            <div class="content-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                <?php endif; ?>
                <div class="form-card">
                    <h3><i class="fas fa-user"></i> Data Anggota</h3>
                    <form method="POST">
                        <div class="form-group"><label>Nama Lengkap *</label><input type="text" name="nama_lengkap"
                                class="form-control" value="<?= htmlspecialchars($anggota['nama_lengkap']) ?>" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group"><label>Username *</label><input type="text" name="username"
                                    class="form-control" value="<?= htmlspecialchars($anggota['username']) ?>" required>
                            </div>
                            <div class="form-group"><label>Kelas</label><input type="text" name="kelas"
                                    class="form-control" value="<?= htmlspecialchars($anggota['kelas'] ?? '') ?>"
                                    placeholder="RPL-1"></div>
                        </div>
                        <div class="form-group"><label>Password
                                <?= $isEdit ? '(kosongkan jika tidak diubah)' : '*' ?></label><input type="password"
                                name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>></div>
                        <div class="form-row">
                            <div class="form-group"><label>No. Telepon</label><input type="text" name="no_telepon"
                                    class="form-control" value="<?= htmlspecialchars($anggota['no_telepon'] ?? '') ?>">
                            </div>
                            <div class="form-group"><label>Alamat</label><textarea name="alamat" class="form-control"
                                    rows="2"><?= htmlspecialchars($anggota['alamat'] ?? '') ?></textarea></div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                            <?= $isEdit ? 'Perbarui' : 'Simpan' ?></button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>

</html>