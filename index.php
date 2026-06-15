<?php
/**
 * index.php — Entry point utama project
 * Redirect ke dashboard jika sudah login, atau ke halaman login
 */
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: /web-perpustakaan/pages/dashboard/');
} else {
    header('Location: /web-perpustakaan/login.php');
}
exit;
