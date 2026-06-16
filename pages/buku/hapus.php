<?php
/**
 * pages/buku/hapus.php — Skrip Proses Hapus Buku
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

if (isset($_GET['id'])) {
    $id_buku = intval($_GET['id']);

    // Eksekusi hapus data
    $sql = "DELETE FROM buku WHERE id_buku = $id_buku";
    
    if (mysqli_query($koneksi, $sql)) {
        header('Location: index.php');
        exit;
    } else {
        echo "<script>
                alert('Gagal menghapus data buku! Kemungkinan data buku ini terikat pada data transaksi.');
                window.location.href = 'index.php';
              </script>";
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}