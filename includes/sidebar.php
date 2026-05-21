<?php
/**
 * includes/sidebar.php
 * Sidebar navigasi kiri
 */
$current = $_SERVER['REQUEST_URI'];
?>
<aside class="sidebar" id="sidebar">
  <nav class="sidebar-nav">

    <div class="sidebar-section-label">MENU UTAMA</div>

    <a href="/perpustakaan/pages/dashboard/"
       class="sidebar-link <?= strpos($current, '/dashboard') !== false ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>

    <div class="sidebar-section-label mt-3">DATA MASTER</div>

    <a href="/perpustakaan/pages/buku/"
       class="sidebar-link <?= strpos($current, '/buku') !== false ? 'active' : '' ?>">
      <i class="bi bi-journals"></i>
      <span>Data Buku</span>
    </a>

    <a href="/perpustakaan/pages/anggota/"
       class="sidebar-link <?= strpos($current, '/anggota') !== false ? 'active' : '' ?>">
      <i class="bi bi-people"></i>
      <span>Data Anggota</span>
    </a>

    <div class="sidebar-section-label mt-3">TRANSAKSI</div>

    <a href="/perpustakaan/pages/peminjaman/"
       class="sidebar-link <?= strpos($current, '/peminjaman') !== false ? 'active' : '' ?>">
      <i class="bi bi-arrow-left-right"></i>
      <span>Peminjaman</span>
    </a>

    <div class="sidebar-section-label mt-3">AKUN</div>

    <a href="/perpustakaan/logout.php" class="sidebar-link text-danger-soft">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </a>

  </nav>
</aside>
