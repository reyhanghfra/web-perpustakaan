<?php
/**
 * koneksi.php — File konfigurasi koneksi database
 * Semua halaman memanggil file ini dengan require_once
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Ganti sesuai user MySQL kamu
define('DB_PASS', '');            // Ganti sesuai password MySQL kamu
define('DB_NAME', 'perpustakaan_mini');

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$koneksi) {
    die('<div style="font-family:sans-serif;padding:20px;color:red;">
        <strong>Koneksi database gagal!</strong><br>
        Error: ' . mysqli_connect_error() . '<br>
        Pastikan MySQL sudah berjalan dan konfigurasi di koneksi.php sudah benar.
    </div>');
}

mysqli_set_charset($koneksi, 'utf8mb4');
