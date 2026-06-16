<?php
/**
 * pages/anggota_area/index.php — Dashboard Anggota
 */
session_start();

// Proteksi: hanya anggota yang sudah login
if (!isset($_SESSION['anggota_login']) || $_SESSION['anggota_login'] !== true) {
    header('Location: /perpustakaan/login_anggota.php');
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';

$id_anggota  = $_SESSION['anggota_id'];
$nama        = $_SESSION['anggota_nama'];

// Hitung total peminjaman aktif milik anggota ini
$total_aktif = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS total FROM peminjaman
     WHERE id_anggota = $id_anggota AND status = 'Diambil'"))['total'];

// Hitung total booking aktif
$total_booking = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS total FROM booking
     WHERE id_anggota = $id_anggota AND status = 'Booking'"))['total'];

// Hitung total riwayat
$total_riwayat = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COUNT(*) AS total FROM peminjaman
     WHERE id_anggota = $id_anggota"))['total'];

// Ambil daftar peminjaman aktif
$peminjaman_aktif = mysqli_query($koneksi,
    "SELECT p.*, b.judul, b.penulis
     FROM peminjaman p
     JOIN buku b ON p.id_buku = b.id_buku
     WHERE p.id_anggota = $id_anggota AND p.status = 'Diambil'
     ORDER BY p.tanggal_kembali ASC");

// Ambil daftar booking aktif
$booking_aktif = mysqli_query($koneksi,
    "SELECT bk.*, b.judul
     FROM booking bk
     JOIN buku b ON bk.id_buku = b.id_buku
     WHERE bk.id_anggota = $id_anggota AND bk.status = 'Booking'
     ORDER BY bk.tanggal_expired ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Anggota — Perpustakaan Mini</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/perpustakaan/assets/css/style.css">
</head>
<body style="font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light, #f8f9fa);">

  <!-- Navbar sederhana untuk anggota -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-4">
    <span class="navbar-brand fw-bold">
      <i class="bi bi-book-half me-2"></i>Library Space
    </span>
    <div class="ms-auto d-flex align-items-center gap-3">
      <span class="text-white small">
        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nama) ?>
      </span>
      <a href="/perpustakaan/logout_anggota.php" class="btn btn-outline-light btn-sm">
        <i class="bi bi-box-arrow-right me-1"></i> Keluar
      </a>
    </div>
  </nav>

  <div class="container py-4">

    <!-- Greeting -->
    <div class="mb-4">
      <h5 class="fw-bold mb-0">Halo, <?= htmlspecialchars($nama) ?>! 👋</h5>
      <p class="text-muted small">Selamat datang di portal anggota perpustakaan.</p>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fs-1 text-primary fw-bold"><?= $total_aktif ?></div>
          <div class="small text-muted">Sedang Dipinjam</div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fs-1 text-warning fw-bold"><?= $total_booking ?></div>
          <div class="small text-muted">Booking Aktif</div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fs-1 text-success fw-bold"><?= $total_riwayat ?></div>
          <div class="small text-muted">Total Riwayat</div>
        </div>
      </div>
    </div>

    <!-- Peminjaman Aktif -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-arrow-left-right me-2"></i>Buku Sedang Dipinjam
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Judul Buku</th>
              <th>Penulis</th>
              <th>Tgl Pinjam</th>
              <th>Batas Kembali</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($peminjaman_aktif) > 0):
              while ($row = mysqli_fetch_assoc($peminjaman_aktif)):
                $terlambat = strtotime($row['tanggal_kembali']) < strtotime('today');
            ?>
            <tr>
              <td class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></td>
              <td class="text-muted"><?= htmlspecialchars($row['penulis']) ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])) ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])) ?></td>
              <td>
                <span class="badge <?= $terlambat ? 'bg-danger' : 'bg-success' ?>">
                  <?= $terlambat ? 'Terlambat' : 'Tepat Waktu' ?>
                </span>
              </td>
            </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="5" class="text-center text-muted py-4">
                Tidak ada buku yang sedang dipinjam.
              </td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Booking Aktif -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-warning text-dark fw-semibold">
        <i class="bi bi-bookmark-check me-2"></i>Booking Aktif
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Kode Booking</th>
              <th>Judul Buku</th>
              <th>Tgl Booking</th>
              <th>Berlaku s/d</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($booking_aktif) > 0):
              while ($row = mysqli_fetch_assoc($booking_aktif)):
            ?>
            <tr>
              <td><strong><?= htmlspecialchars($row['kode_booking']) ?></strong></td>
              <td><?= htmlspecialchars($row['judul']) ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_booking'])) ?></td>
              <td><?= date('d-m-Y', strtotime($row['tanggal_expired'])) ?></td>
            </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="4" class="text-center text-muted py-4">
                Tidak ada booking aktif.
              </td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>