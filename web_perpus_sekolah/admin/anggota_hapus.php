<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? null;
if ($id) {
    $check = $conn->prepare("SELECT COUNT(*) as t FROM peminjaman WHERE id_user=? AND status='dipinjam'");
    $check->bind_param("i", $id);
    $check->execute();
    $active = $check->get_result()->fetch_assoc()['t'];

    if ($active > 0) {
        setFlash('danger', 'Anggota tidak dapat dihapus karena masih memiliki ' . $active . ' peminjaman aktif!');
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='siswa'");
        $stmt->bind_param("i", $id);
        if ($stmt->execute() && $stmt->affected_rows > 0) setFlash('success', 'Anggota berhasil dihapus!');
        else setFlash('danger', 'Gagal menghapus anggota!');
    }
}
header('Location: anggota.php');
exit();
