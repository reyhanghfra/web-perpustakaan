<?php
/**
 * includes/auth_guard.php
 * Sertakan di bagian PALING ATAS setiap halaman yang memerlukan login.
 * Usage: require_once __DIR__ . '/../../includes/auth_guard.php';
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: /perpustakaan/login.php');
    exit;
}
