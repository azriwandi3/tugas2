<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div>
            <h4><i class="fas fa-book-open-reader"></i> <?= APP_NAME ?></h4>
            <p>Sistem informasi perpustakaan digital <?= SCHOOL_NAME ?> untuk memudahkan siswa dan admin dalam
                peminjaman dan pendataan buku secara efektif dan efisien.</p>
        </div>
        <div>
            <h4>Tautan</h4>
            <div class="footer-links">
                <a href="<?= $base_url ?>/">Beranda</a>
                <a href="<?= $base_url ?>/login.php">Login</a>
                <a href="<?= $base_url ?>/register.php">Daftar</a>
            </div>
        </div>
        <div>
            <h4>Kontak</h4>
            <div class="footer-links">
                <a href="#"><i class="fas fa-map-marker-alt"></i> Jl. Pendidikan No. 1</a>
                <a href="#"><i class="fas fa-phone"></i> (021) 1234567</a>
                <a href="#"><i class="fas fa-envelope"></i> info@smkn1airjoman.sch.id</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> - <?= SCHOOL_NAME ?>. All rights reserved.</p>
    </div>
</footer>

<script src="<?= $base_url ?>/assets/js/script.js"></script>
</body>

</html>