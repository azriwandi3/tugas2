<?php
/**
 * Konfigurasi Database MySQL
 * Perpustakaan Sekolah Digital
 */

// Konfigurasi Database
define('DB_HOST', 'mariadb-azri');
define('DB_USER', 'azri');
define('DB_PASS', 'azri123');
define('DB_NAME', 'perpustakaan_sekolah');

// Konfigurasi Aplikasi
define('APP_NAME', 'Perpustakaan Digital');
define('SCHOOL_NAME', 'SMK Negeri 1 Air Joman');
define('DURASI_PINJAM', 7); // hari
define('DENDA_PER_HARI', 1000); // Rupiah

// Koneksi Database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Base URL (sesuaikan dengan lokasi project)
$base_url = '/web_perpus_sekolah';

// Fungsi helper format Rupiah
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Fungsi helper format tanggal Indonesia
function formatTanggal($tanggal)
{
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $date = new DateTime($tanggal);
    return $date->format('d') . ' ' . $bulan[(int) $date->format('m')] . ' ' . $date->format('Y');
}
?>