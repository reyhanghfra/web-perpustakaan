<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/auth_guard.php';
require_once __DIR__ . '/../config/koneksi.php';

$id = $_SESSION['anggota_id'];

$pinjam = mysqli_query($koneksi, "
  SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali, p.status,
         GROUP_CONCAT(b.judul SEPARATOR ', ') as judul_buku
  FROM peminjaman p
  JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
  JOIN buku b ON dp.id_buku = b.id_buku
  WHERE p.id_anggota = $id
  GROUP BY p.id_peminjaman
  ORDER BY p.id_peminjaman DESC
");

$total_pinjam  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_anggota = $id AND status = 'dipinjam'"))[0];
$total_kembali = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM peminjaman WHERE id_anggota = $id AND status = 'kembali'"))[0];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Anggota</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f6fb; }
  </style>
</head>
<body>

<nav class="navbar bg-white border-bottom px-4 shadow-sm">
  <span class="navbar-brand fw-bold">
    <i class="bi bi-book-half me-2 text-success"></i>Portal Anggota
  </span>
  <div class="ms-auto d-flex align-items-center gap-3">
    <span class="text-muted small">Halo, <strong><?= htmlspecialchars($_SESSION['anggota_nama']) ?></strong></span>
    <a href="logout.php" class="btn btn-sm btn-outline-danger">
      <i class="bi bi-box-arrow-right me-1"></i>Logout
    </a>
  </div>
</nav>

<div class="container py-4">

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-warning bg-opacity-25 p-3">
            <i class="bi bi-arrow-left-right fs-4 text-warning"></i>
          </div>
          <div>
            <div class="text-muted small">Sedang Dipinjam</div>
            <div class="fs-3 fw-bold"><?= $total_pinjam ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle bg-success bg-opacity-25 p-3">
            <i class="bi bi-check-circle fs-4 text-success"></i>
          </div>
          <div>
            <div class="text-muted small">Sudah Dikembalikan</div>
            <div class="fs-3 fw-bold"><?= $total_kembali ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">
      <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Peminjaman
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Buku</th>
              <th>Tgl Pinjam</th>
              <th>Tgl Kembali</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($pinjam && mysqli_num_rows($pinjam) > 0):
              $no = 1;
              while ($row = mysqli_fetch_assoc($pinjam)): ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                <td><?= $row['tanggal_pinjam'] ?></td>
                <td><?= $row['tanggal_kembali'] ?></td>
                <td>
                  <?php if ($row['status'] === 'dipinjam'): ?>
                    <span class="badge bg-warning text-dark">Dipinjam</span>
                  <?php else: ?>
                    <span class="badge bg-success">Dikembalikan</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  <i class="bi bi-inbox display-4 d-block mb-2"></i>
                  Belum ada riwayat peminjaman.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>