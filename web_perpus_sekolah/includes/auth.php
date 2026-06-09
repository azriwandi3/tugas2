<?php
/**
 * Auth Helper - Session & Authentication
 * Perpustakaan Sekolah Digital
 */

session_start();

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Cek role user
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isSiswa() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'siswa';
}

/**
 * Redirect jika belum login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /web_perpus_sekolah/login.php');
        exit();
    }
}

/**
 * Redirect jika bukan admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /web_perpus_sekolah/login.php');
        exit();
    }
}

/**
 * Redirect jika bukan siswa
 */
function requireSiswa() {
    requireLogin();
    if (!isSiswa()) {
        header('Location: /web_perpus_sekolah/login.php');
        exit();
    }
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'nama' => $_SESSION['nama_lengkap'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
