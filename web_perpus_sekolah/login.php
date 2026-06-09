<?php
/**
 * Login Page - Perpustakaan Sekolah Digital
 */
$pageTitle = 'Login';
$hideNavbar = true;
require_once 'config/database.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: ' . $base_url . '/admin/dashboard.php');
    } else {
        header('Location: ' . $base_url . '/siswa/dashboard.php');
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header('Location: ' . $base_url . '/admin/dashboard.php');
                } else {
                    header('Location: ' . $base_url . '/siswa/dashboard.php');
                }
                exit();
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon">
                <i class="fas fa-book-open-reader"></i>
            </div>
            <h2><?= APP_NAME ?></h2>
            <p><?= SCHOOL_NAME ?></p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
        <?php endif; ?>

        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>">
            <i class="fas fa-info-circle"></i> <?= $flash['message'] ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       placeholder="Masukkan username" value="<?= htmlspecialchars($username ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:0.5rem;">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>

        <div style="text-align:center;margin-top:1.5rem;font-size:0.9rem;color:var(--text-light);">
            Belum punya akun? 
            <a href="<?= $base_url ?>/register.php" style="font-weight:600;">Daftar di sini</a>
        </div>
        <div style="text-align:center;margin-top:0.75rem;">
            <a href="<?= $base_url ?>/" style="font-size:0.85rem;color:var(--text-light);">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

</body>
</html>
