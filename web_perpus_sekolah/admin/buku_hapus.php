<?php
/**
 * Hapus Buku
 */
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
if ($id) {
    // Cek apakah ada peminjaman aktif
    $check = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_buku = ? AND status = 'dipinjam'");
    $check->bind_param("i", $id);
    $check->execute();
    $active = $check->get_result()->fetch_assoc()['t'];

    if ($active > 0) {
        setFlash('danger', 'Buku tidak dapat dihapus karena masih ada ' . $active . ' peminjaman aktif!');
    } else {
        $stmt = $conn->prepare("DELETE FROM buku WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            setFlash('success', 'Buku berhasil dihapus!');
        } else {
            setFlash('danger', 'Gagal menghapus buku!');
        }
    }
}
header('Location: buku.php');
exit();
?>
