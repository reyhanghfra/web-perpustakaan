<?php
session_start();

// Hapus hanya session anggota, bukan session petugas
unset($_SESSION['anggota_login']);
unset($_SESSION['anggota_id']);
unset($_SESSION['anggota_nama']);
unset($_SESSION['anggota_username']);

// Jika tidak ada session lain, destroy seluruhnya
if (empty($_SESSION)) {
    session_destroy();
}

header('Location: /web-perpustakaan/login_anggota.php');
exit;