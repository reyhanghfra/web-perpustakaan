<?php
/**
 * includes/navbar.php
 * Topbar / navbar atas
 */
?>
<nav class="navbar navbar-expand-lg navbar-dark" id="mainNavbar">
  <div class="container-fluid px-4">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="/perpustakaan/pages/dashboard/">
      <span class="brand-icon"><i class="bi bi-book-half"></i></span>
      <span class="brand-name">Perpustakaan Mini</span>
    </a>

    <!-- Toggler mobile -->
    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMenu">
      <!-- Nav Links -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/dashboard') !== false) ? 'active' : '' ?>"
             href="/perpustakaan/pages/dashboard/">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/buku') !== false) ? 'active' : '' ?>"
             href="/perpustakaan/pages/buku/">
            <i class="bi bi-journals me-1"></i> Buku
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/anggota') !== false) ? 'active' : '' ?>"
             href="/perpustakaan/pages/anggota/">
            <i class="bi bi-people me-1"></i> Anggota
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/peminjaman') !== false) ? 'active' : '' ?>"
             href="/perpustakaan/pages/peminjaman/">
            <i class="bi bi-arrow-left-right me-1"></i> Peminjaman
          </a>
        </li>
      </ul>

      <!-- User info + Logout -->
      <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small">
          <i class="bi bi-person-circle me-1"></i>
          <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>
        </span>
        <a href="/perpustakaan/logout.php" class="btn btn-sm btn-outline-light">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>
