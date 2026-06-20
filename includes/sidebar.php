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

    <a href="/web-perpustakaan/pages/dashboard/"
      class="sidebar-link <?= strpos($current, '/dashboard') !== false ? 'active' : '' ?>">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>

    <div class="sidebar-section-label mt-3">DATA MASTER</div>

    <a href="/web-perpustakaan/pages/buku/"
      class="sidebar-link <?= strpos($current, '/buku') !== false ? 'active' : '' ?>">
      <i class="bi bi-journals"></i>
      <span>Data Buku</span>
    </a>

    <a href="/web-perpustakaan/pages/anggota/"
      class="sidebar-link <?= strpos($current, '/anggota') !== false ? 'active' : '' ?>">
      <i class="bi bi-people"></i>
      <span>Data Anggota</span>
    </a>

    <div class="sidebar-section-label mt-3">TRANSAKSI</div>

    <a href="/web-perpustakaan/pages/booking/"
      class="sidebar-link <?= strpos($current, '/booking') !== false ? 'active' : '' ?>">
      <i class="bi bi-bookmark-check"></i>
      <span>Booking</span>
    </a>

    <a href="/web-perpustakaan/pages/peminjaman/"
      class="sidebar-link <?= strpos($current, '/peminjaman') !== false ? 'active' : '' ?>">
      <i class="bi bi-arrow-left-right"></i>
      <span>Peminjaman</span>
    </a>

    <div class="sidebar-section-label mt-3">PENGATURAN</div>

    <a href="/web-perpustakaan/pages/admin/"
      class="sidebar-link <?= strpos($current, '/admin') !== false ? 'active' : '' ?>">
      <i class="bi bi-shield-lock"></i>
      <span>Kelola Admin</span>
    </a>

    <div class="sidebar-section-label mt-3">AKUN</div>

    <a href="/web-perpustakaan/logout.php" class="sidebar-link text-danger-soft">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </a>

  </nav>
</aside>