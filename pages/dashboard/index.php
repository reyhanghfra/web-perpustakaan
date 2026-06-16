<?php
/**
 * pages/dashboard/index.php — Halaman Dashboard
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Dashboard';

// Ambil statistik
$total_buku     = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM buku"))[0];
$total_anggota  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM anggota"))[0];
$total_pinjam   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'"))[0];
$total_transaksi= mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman"))[0];

require_once __DIR__ . '/../../includes/header.php';
?>

<!-- Layout wrapper -->
<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <!-- Page content -->
    <div class="pt-2">

      <!-- Page header -->
      <div class="page-header">
        <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>! Berikut ringkasan data perpustakaan.</p>
      </div>

      <!-- Stat cards -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-journals"></i></div>
            <div>
              <div class="stat-label">Total Buku</div>
              <div class="stat-value"><?= $total_buku ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-people"></i></div>
            <div>
              <div class="stat-label">Total Anggota</div>
              <div class="stat-value"><?= $total_anggota ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-arrow-left-right"></i></div>
            <div>
              <div class="stat-label">Sedang Dipinjam</div>
              <div class="stat-value"><?= $total_pinjam ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-receipt"></i></div>
            <div>
              <div class="stat-label">Total Transaksi</div>
              <div class="stat-value"><?= $total_transaksi ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabel buku terbaru -->
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
              <i class="bi bi-journals text-primary"></i> Buku Terbaru
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Judul</th>
                      <th>Penulis</th>
                      <th>Stok</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $buku_res = mysqli_query($koneksi,
                      "SELECT b.judul, b.penulis, b.stok
                       FROM buku b ORDER BY b.id_buku DESC LIMIT 6");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($buku_res)):
                    ?>
                    <tr>
                      <td class="text-muted"><?= $no++ ?></td>
                      <td><?= htmlspecialchars($row['judul']) ?></td>
                      <td class="text-muted"><?= htmlspecialchars($row['penulis']) ?></td>
                      <td>
                        <?php if ($row['stok'] > 0): ?>
                          <span class="badge-status badge-tersedia"><?= $row['stok'] ?> tersedia</span>
                        <?php else: ?>
                          <span class="badge-status badge-habis">Habis</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white border-top text-end">
              <a href="/web-perpustakaan/pages/buku/" class="btn btn-sm btn-outline-primary">
                Lihat semua buku <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Anggota terbaru -->
        <div class="col-lg-5">
          <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
              <i class="bi bi-people text-success"></i> Anggota Terbaru
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Nama</th>
                      <th>No. HP</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $ang_res = mysqli_query($koneksi,
                      "SELECT nama, no_hp FROM anggota ORDER BY id_anggota DESC LIMIT 6");
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($ang_res)):
                    ?>
                    <tr>
                      <td class="text-muted"><?= $no++ ?></td>
                      <td><?= htmlspecialchars($row['nama']) ?></td>
                      <td class="text-muted"><?= htmlspecialchars($row['no_hp']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white border-top text-end">
              <a href="/web-perpustakaan/pages/anggota/" class="btn btn-sm btn-outline-success">
                Lihat semua anggota <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div><!-- /row -->

    </div><!-- /page content -->
  </div><!-- /main-wrapper -->
</div><!-- /d-flex -->

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
