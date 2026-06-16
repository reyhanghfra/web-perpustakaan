<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth_guard.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: /web-perpustakaan/pages/dashboard/');
    exit;
}

$id_user = $_GET['id'] ?? null;

if (!$id_user) {
    header('Location: /web-perpustakaan/pages/admin/?status=error');
    exit;
}

$check_query = "SELECT id_user FROM users WHERE id_user = ?";
$stmt = mysqli_prepare($koneksi, $check_query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header('Location: /web-perpustakaan/pages/admin/?status=error');
    exit;
}

$delete_query = "DELETE FROM users WHERE id_user = ?";
$stmt = mysqli_prepare($koneksi, $delete_query);
mysqli_stmt_bind_param($stmt, "i", $id_user);

if (mysqli_stmt_execute($stmt)) {
    header('Location: /web-perpustakaan/pages/admin/?status=deleted');
} else {
    header('Location: /web-perpustakaan/pages/admin/?status=error');
}
exit;
