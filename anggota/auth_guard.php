<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['anggota_login']) || $_SESSION['anggota_login'] !== true) {
    header('Location: /web-perpustakaan/login_anggota.php');
    exit;
}