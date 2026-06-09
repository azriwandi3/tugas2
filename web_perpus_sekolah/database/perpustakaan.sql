-- ============================================
-- DATABASE PERPUSTAKAAN SEKOLAH DIGITAL
-- ============================================

CREATE DATABASE IF NOT EXISTS perpustakaan_sekolah
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE perpustakaan_sekolah;

-- ============================================
-- TABEL USERS (Admin & Siswa)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    kelas VARCHAR(20) DEFAULT NULL,
    no_telepon VARCHAR(15) DEFAULT NULL,
    alamat TEXT DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL BUKU
-- ============================================
CREATE TABLE IF NOT EXISTS buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) DEFAULT NULL,
    tahun_terbit YEAR DEFAULT NULL,
    isbn VARCHAR(20) DEFAULT NULL,
    kategori VARCHAR(50) DEFAULT NULL,
    stok INT NOT NULL DEFAULT 0,
    cover VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL PEMINJAMAN
-- ============================================
CREATE TABLE IF NOT EXISTS peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_buku INT NOT NULL,
    tgl_pinjam DATE NOT NULL,
    tgl_kembali DATE NOT NULL,
    tgl_dikembalikan DATE DEFAULT NULL,
    status ENUM('dipinjam', 'dikembalikan', 'terlambat') NOT NULL DEFAULT 'dipinjam',
    denda DECIMAL(10,2) DEFAULT 0.00,
    catatan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES buku(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- DATA ADMIN DEFAULT
-- Password: admin123 (hashed with PHP password_hash)
-- ============================================
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$uAIC36BPLPGI6w.BXCoVCumBSqISgLMmz4bgkkQayAupXFBdVKOoO', 'Administrator Perpustakaan', 'admin');

-- ============================================
-- DATA BUKU CONTOH
-- ============================================
INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, isbn, kategori, stok, deskripsi) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '978-979-1227-00-0', 'Novel', 5, 'Novel yang menceritakan kisah 10 anak dari Belitung yang berjuang untuk mendapatkan pendidikan.'),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, '978-979-3062-00-0', 'Novel', 3, 'Novel pertama dari Tetralogi Buru yang mengisahkan kehidupan di era kolonial Belanda.'),
('Filosofi Teras', 'Henry Manampiring', 'Penerbit Buku Kompas', 2018, '978-602-412-00-0', 'Pengembangan Diri', 4, 'Buku tentang filsafat Stoa yang diaplikasikan dalam kehidupan modern.'),
('Sapiens: Riwayat Singkat Umat Manusia', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', 2011, '978-602-424-00-0', 'Sejarah', 2, 'Sejarah evolusi manusia dari zaman purba sampai revolusi ilmiah.'),
('Matematika Kelas X', 'Tim Kemendikbud', 'Kemendikbud', 2022, '978-602-244-00-0', 'Pelajaran', 10, 'Buku pelajaran Matematika untuk SMA/MA kelas X kurikulum merdeka.'),
('Fisika Kelas XI', 'Tim Kemendikbud', 'Kemendikbud', 2022, '978-602-244-01-0', 'Pelajaran', 8, 'Buku pelajaran Fisika untuk SMA/MA kelas XI kurikulum merdeka.'),
('Bahasa Indonesia Kelas XII', 'Tim Kemendikbud', 'Kemendikbud', 2022, '978-602-244-02-0', 'Pelajaran', 7, 'Buku pelajaran Bahasa Indonesia untuk SMA/MA kelas XII kurikulum merdeka.'),
('Kimia Dasar', 'Raymond Chang', 'Erlangga', 2010, '978-979-033-00-0', 'Pelajaran', 6, 'Buku kimia dasar untuk tingkat SMA dan universitas.'),
('Sang Pemimpi', 'Andrea Hirata', 'Bentang Pustaka', 2006, '978-979-1227-01-0', 'Novel', 4, 'Sekuel dari Laskar Pelangi yang menceritakan mimpi-mimpi besar anak Belitung.'),
('Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia Pustaka Utama', 2009, '978-979-22-00-0', 'Novel', 5, 'Novel inspiratif tentang kehidupan di pesantren dan mimpi untuk mengejar pendidikan tinggi.'),
('Sejarah Indonesia Kelas X', 'Tim Kemendikbud', 'Kemendikbud', 2022, '978-602-244-03-0', 'Pelajaran', 9, 'Buku pelajaran Sejarah Indonesia untuk SMA/MA kelas X.'),
('Biologi Kelas XI', 'Tim Kemendikbud', 'Kemendikbud', 2022, '978-602-244-04-0', 'Pelajaran', 7, 'Buku pelajaran Biologi untuk SMA/MA kelas XI kurikulum merdeka.');

-- ============================================
-- DATA SISWA CONTOH
-- Password: siswa123
-- ============================================
INSERT INTO users (username, password, nama_lengkap, role, kelas, no_telepon, alamat) VALUES
('siswa01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ahmad Rizki Pratama', 'siswa', 'X-IPA-1', '081234567890', 'Jl. Merdeka No. 10'),
('siswa02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Siti Nurhaliza', 'siswa', 'XI-IPS-2', '081234567891', 'Jl. Pahlawan No. 25'),
('siswa03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Budi Santoso', 'siswa', 'XII-IPA-3', '081234567892', 'Jl. Sudirman No. 50');

-- ============================================
-- DATA PEMINJAMAN CONTOH
-- ============================================
INSERT INTO peminjaman (id_user, id_buku, tgl_pinjam, tgl_kembali, status) VALUES
(2, 1, '2026-03-25', '2026-04-01', 'dipinjam'),
(3, 3, '2026-03-28', '2026-04-04', 'dipinjam'),
(4, 5, '2026-03-20', '2026-03-27', 'dikembalikan');

UPDATE peminjaman SET tgl_dikembalikan = '2026-03-26' WHERE id = 3;
