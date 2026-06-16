<?php
/**
 * pages/anggota/hapus.php — Skrip Proses Hapus Anggota
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

if (!isset($_GET['id'])) {
  header('Location: index.php');
  exit;
}

$id = (int)$_GET['id'];
if ($id <= 0) {
  header('Location: index.php');
  exit;
}

// Coba hapus anggota
$sql = "DELETE FROM anggota WHERE id_anggota='$id'";
if (mysqli_query($koneksi, $sql)) {
  header('Location: index.php');
  exit;
}

echo "<script>
        alert('Gagal menghapus anggota. Kemungkinan data terikat pada transaksi peminjaman.');
        window.location.href='index.php';
      </script>";
exit;



