<?php
/**
 * Header & Navbar - Perpustakaan Sekolah Digital
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Perpustakaan Digital <?= SCHOOL_NAME ?> - Sistem peminjaman dan pendataan buku sekolah secara digital">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= APP_NAME ?> <?= SCHOOL_NAME ?></title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php if (!isset($hideNavbar) || !$hideNavbar): ?>
<nav class="navbar">
    <a href="<?= $base_url ?>/" class="logo">
        <i class="fas fa-book-open-reader"></i>
        <span><?= APP_NAME ?></span>
    </a>
    <button class="nav-toggle" onclick="toggleNav()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="nav-links">
        <a href="<?= $base_url ?>/" class="<?= $currentPage == 'index' ? 'active' : '' ?>">
            <i class="fas fa-home"></i> Beranda
        </a>
        <?php if ($currentUser): ?>
            <?php if (isAdmin()): ?>
                <a href="<?= $base_url ?>/admin/dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="<?= $base_url ?>/siswa/dashboard.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            <?php endif; ?>
            <a href="<?= $base_url ?>/logout.php" class="btn-login">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        <?php else: ?>
            <a href="<?= $base_url ?>/login.php" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </a>
        <?php endif; ?>
    </div>
</nav>
<?php endif; ?>
