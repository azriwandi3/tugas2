<?php
/**
 * Register Page - Pendaftaran Anggota Baru (Siswa)
 */
$pageTitle = 'Daftar Anggota';
$hideNavbar = true;
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . $base_url . '/siswa/dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $kelas = trim($_POST['kelas'] ?? '');
    $telepon = trim($_POST['no_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($username) || empty($password) || empty($kelas)) {
        $error = 'Nama, username, password, dan kelas wajib diisi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek username duplikat
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan!';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role, kelas, no_telepon, alamat) VALUES (?, ?, ?, 'siswa', ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $hashed, $nama, $kelas, $telepon, $alamat);

            if ($stmt->execute()) {
                setFlash('success', 'Pendaftaran berhasil! Silakan login.');
                header('Location: ' . $base_url . '/login.php');
                exit();
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="auth-page">
        <div class="auth-card" style="max-width:520px;">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Daftar Anggota Baru</h2>
                <p>Bergabung dengan <?= APP_NAME ?> <?= SCHOOL_NAME ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nama_lengkap"><i class="fas fa-user"></i> Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control"
                        placeholder="Masukkan nama lengkap" value="<?= htmlspecialchars($nama ?? '') ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-at"></i> Username *</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Username"
                            value="<?= htmlspecialchars($username ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="kelas"><i class="fas fa-graduation-cap"></i> Kelas *</label>
                        <input type="text" id="kelas" name="kelas" class="form-control" placeholder="Contoh: RPL-1"
                            value="<?= htmlspecialchars($kelas ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password *</label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group">
                        <label for="konfirmasi_password"><i class="fas fa-lock"></i> Konfirmasi *</label>
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control"
                            placeholder="Ulangi password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="no_telepon"><i class="fas fa-phone"></i> No. Telepon</label>
                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" placeholder="08xxxxxxxxxx"
                        value="<?= htmlspecialchars($telepon ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                    <textarea id="alamat" name="alamat" class="form-control" rows="2"
                        placeholder="Alamat lengkap"><?= htmlspecialchars($alamat ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-user-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div style="text-align:center;margin-top:1.5rem;font-size:0.9rem;color:var(--text-light);">
                Sudah punya akun?
                <a href="<?= $base_url ?>/login.php" style="font-weight:600;">Login di sini</a>
            </div>
        </div>
    </div>

</body>

</html>