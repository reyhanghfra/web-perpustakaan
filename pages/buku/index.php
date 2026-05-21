<?php
/**
 * pages/buku/index.php — Data Buku (dikerjakan Minggu 2)
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';
$page_title = 'Data Buku';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
    <div class="pt-2">
      <div class="page-header">
        <h1><i class="bi bi-journals me-2 text-primary"></i>Data Buku</h1>
        <p>Kelola data buku perpustakaan.</p>
      </div>
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        Fitur CRUD Buku akan diimplementasikan pada <strong>Minggu 2</strong>.
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
