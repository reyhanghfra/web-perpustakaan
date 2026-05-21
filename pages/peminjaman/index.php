<?php
/**
 * pages/peminjaman/index.php — Peminjaman (dikerjakan Minggu 3)
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';
$page_title = 'Peminjaman';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
    <div class="pt-2">
      <div class="page-header">
        <h1><i class="bi bi-arrow-left-right me-2 text-warning"></i>Peminjaman</h1>
        <p>Kelola transaksi peminjaman buku.</p>
      </div>
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Fitur Peminjaman akan diimplementasikan pada <strong>Minggu 3</strong>.
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
