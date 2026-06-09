<?php
/**
 * Proses Peminjaman Buku oleh Siswa
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireSiswa();

$id_buku = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$id_buku) {
    header('Location: katalog.php');
    exit();
}

// Cek apakah sudah meminjam buku ini
$cek = $conn->prepare("SELECT id FROM peminjaman WHERE id_user=? AND id_buku=? AND status='dipinjam'");
$cek->bind_param("ii", $userId, $id_buku);
$cek->execute();
if ($cek->get_result()->num_rows > 0) {
    setFlash('warning', 'Anda sudah meminjam buku ini dan belum dikembalikan!');
    header('Location: katalog.php');
    exit();
}

// Cek stok
$stok = $conn->prepare("SELECT stok, judul FROM buku WHERE id=?");
$stok->bind_param("i", $id_buku);
$stok->execute();
$buku = $stok->get_result()->fetch_assoc();

if (!$buku || $buku['stok'] <= 0) {
    setFlash('danger', 'Stok buku habis!');
    header('Location: katalog.php');
    exit();
}

// Cek maksimal peminjaman (maks 3 buku bersamaan)
$aktif = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=? AND status='dipinjam'");
$aktif->bind_param("i", $userId);
$aktif->execute();
if ($aktif->get_result()->fetch_assoc()['t'] >= 3) {
    setFlash('warning', 'Anda sudah meminjam 3 buku. Kembalikan dulu sebelum meminjam buku baru.');
    header('Location: katalog.php');
    exit();
}

// Proses peminjaman
$conn->begin_transaction();
try {
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = date('Y-m-d', strtotime("+".DURASI_PINJAM." days"));

    $stmt = $conn->prepare("INSERT INTO peminjaman (id_user,id_buku,tgl_pinjam,tgl_kembali,status) VALUES(?,?,?,?,'dipinjam')");
    $stmt->bind_param("iiss", $userId, $id_buku, $tgl_pinjam, $tgl_kembali);
    $stmt->execute();

    $upd = $conn->prepare("UPDATE buku SET stok=stok-1 WHERE id=?");
    $upd->bind_param("i", $id_buku);
    $upd->execute();

    $conn->commit();
    setFlash('success', 'Berhasil meminjam "' . $buku['judul'] . '"! Batas pengembalian: ' . formatTanggal($tgl_kembali));
} catch (Exception $e) {
    $conn->rollback();
    setFlash('danger', 'Gagal memproses peminjaman.');
}

header('Location: dashboard.php');
exit();
