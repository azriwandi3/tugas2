<?php
/**
 * Proses Pengembalian Buku (Admin)
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? '';

if ($id && $action === 'kembali') {
    $stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id=? AND status='dipinjam'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pinjam = $stmt->get_result()->fetch_assoc();

    if ($pinjam) {
        $conn->begin_transaction();
        try {
            $today = date('Y-m-d');
            $denda = 0;
            if (strtotime($today) > strtotime($pinjam['tgl_kembali'])) {
                $daysLate = floor((strtotime($today) - strtotime($pinjam['tgl_kembali'])) / 86400);
                $denda = $daysLate * DENDA_PER_HARI;
            }

            $upd = $conn->prepare("UPDATE peminjaman SET status='dikembalikan', tgl_dikembalikan=?, denda=? WHERE id=?");
            $upd->bind_param("sdi", $today, $denda, $id);
            $upd->execute();

            $stok = $conn->prepare("UPDATE buku SET stok=stok+1 WHERE id=?");
            $stok->bind_param("i", $pinjam['id_buku']);
            $stok->execute();

            $conn->commit();
            $msg = 'Buku berhasil dikembalikan!';
            if ($denda > 0) $msg .= ' Denda: ' . formatRupiah($denda);
            setFlash('success', $msg);
        } catch (Exception $e) {
            $conn->rollback();
            setFlash('danger', 'Gagal memproses pengembalian!');
        }
    } else {
        setFlash('danger', 'Transaksi tidak ditemukan!');
    }
}

header('Location: transaksi.php');
exit();
