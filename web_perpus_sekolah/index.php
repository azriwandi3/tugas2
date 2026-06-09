<?php
/**
 * Landing Page - Perpustakaan Sekolah Digital
 */
$pageTitle = 'Beranda';
require_once 'config/database.php';

// Get statistics
$totalBuku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$totalAnggota = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='siswa'")->fetch_assoc()['total'];
$totalPinjam = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='dipinjam'")->fetch_assoc()['total'];
$totalKembali = $conn->query("SELECT COUNT(*) as total FROM peminjaman WHERE status='dikembalikan'")->fetch_assoc()['total'];

// Get latest books
$latestBooks = $conn->query("SELECT * FROM buku ORDER BY created_at DESC LIMIT 8");

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Selamat Datang di<br><span class="highlight"><?= APP_NAME ?></span></h1>
            <p>Sistem informasi perpustakaan <?= SCHOOL_NAME ?> yang modern dan mudah digunakan. Pinjam, baca, dan kelola buku secara digital kapan saja dan di mana saja.</p>
            <div class="hero-buttons">
                <a href="<?= $base_url ?>/login.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Masuk Sekarang
                </a>
                <a href="<?= $base_url ?>/register.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-user-plus"></i> Daftar Anggota
                </a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="book-stack">📚</div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="section-title">
        <h2>Perpustakaan Dalam Angka</h2>
        <p>Statistik perpustakaan <?= SCHOOL_NAME ?> saat ini</p>
    </div>
    <div class="stats-grid">
        <div class="stat-card animate-fade-up">
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <h3><?= $totalBuku ?></h3>
            <p>Total Koleksi Buku</p>
        </div>
        <div class="stat-card animate-fade-up delay-1">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <h3><?= $totalAnggota ?></h3>
            <p>Anggota Terdaftar</p>
        </div>
        <div class="stat-card animate-fade-up delay-2">
            <div class="stat-icon"><i class="fas fa-hand-holding-heart"></i></div>
            <h3><?= $totalPinjam ?></h3>
            <p>Sedang Dipinjam</p>
        </div>
        <div class="stat-card animate-fade-up delay-3">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <h3><?= $totalKembali ?></h3>
            <p>Dikembalikan</p>
        </div>
    </div>
</section>

<!-- Latest Books -->
<section class="books-section">
    <div class="section-title">
        <h2>Koleksi Buku Terbaru</h2>
        <p>Temukan buku-buku menarik di perpustakaan kami</p>
    </div>
    <div class="books-grid">
        <?php while ($buku = $latestBooks->fetch_assoc()): ?>
        <div class="book-card">
            <div class="book-cover" style="background: linear-gradient(135deg, 
                hsl(<?= (crc32($buku['judul']) % 360) ?>, 60%, 45%), 
                hsl(<?= (crc32($buku['judul']) % 360 + 40) ?>, 70%, 55%));">
                <i class="fas fa-book"></i>
                <span class="kategori-badge"><?= htmlspecialchars($buku['kategori']) ?></span>
            </div>
            <div class="book-info">
                <h4><?= htmlspecialchars($buku['judul']) ?></h4>
                <p class="author"><i class="fas fa-user-pen"></i> <?= htmlspecialchars($buku['pengarang']) ?></p>
                <div class="book-meta">
                    <span class="stock <?= $buku['stok'] > 0 ? 'available' : 'empty' ?>">
                        <i class="fas fa-<?= $buku['stok'] > 0 ? 'check-circle' : 'times-circle' ?>"></i>
                        Stok: <?= $buku['stok'] ?>
                    </span>
                    <span style="font-size:0.8rem;color:var(--text-light);"><?= $buku['tahun_terbit'] ?></span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
