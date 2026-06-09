<?php
/**
 * Logout - Destroy session and redirect
 */
require_once 'config/database.php';
require_once 'includes/auth.php';

$_SESSION = [];
session_destroy();

header('Location: ' . $base_url . '/login.php');
exit();
?>
