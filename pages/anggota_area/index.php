<?php
/**
 * pages/anggota_area/index.php — Dashboard Anggota
 */
session_start();

if (!isset($_SESSION['anggota_login']) || $_SESSION['anggota_login'] !== true) {
    header('Location: /web-perpustakaan/login_anggota.php');
    exit;
}

require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/whatsapp.php';

$id_anggota = $_SESSION['anggota_id'];
$nama       = $_SESSION['anggota_nama'];

// ===== Proses Form Booking Buku (oleh anggota sendiri) =====
$booking_error   = '';
$booking_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_buku'])) {
    $id_buku_booking = intval($_POST['id_buku'] ?? 0);

    if ($id_buku_booking === 0) {
        $booking_error = 'Pilih buku terlebih dahulu.';
    } else {
        // Cek stok buku
        $cek_stok = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT stok, judul FROM buku WHERE id_buku = $id_buku_booking"));

        if (!$cek_stok || $cek_stok['stok'] < 1) {
            $booking_error = 'Stok buku tidak tersedia.';
        } else {
            // Generate kode booking unik: BK-YYYYMMDD + 4 digit random
            $kode_baru = 'BK-' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $tgl_booking = date('Y-m-d');
            $tgl_expired = date('Y-m-d', strtotime('+3 days')); // booking berlaku 3 hari

            $sql_insert = "INSERT INTO booking
                            (kode_booking, id_anggota, id_buku, tanggal_booking, tanggal_expired, status)
                           VALUES
                            ('$kode_baru', $id_anggota, $id_buku_booking, '$tgl_booking', '$tgl_expired', 'Booking')";

            if (mysqli_query($koneksi, $sql_insert)) {
                // Ambil no_hp anggota untuk kirim WA
                $data_anggota = mysqli_fetch_assoc(mysqli_query($koneksi,
                    "SELECT nama, no_hp FROM anggota WHERE id_anggota = $id_anggota"));

                $nama_buku       = htmlspecialchars($cek_stok['judul']);
                $nama_anggota_wa = htmlspecialchars($data_anggota['nama']);

                // Susun pesan WhatsApp
                $pesan = "📚 *PERPUSTAKAAN MINI*\n\n";
                $pesan .= "Halo, *$nama_anggota_wa*! 👋\n\n";
                $pesan .= "Booking buku kamu berhasil dibuat.\n\n";
                $pesan .= "📖 Buku     : *$nama_buku*\n";
                $pesan .= "🔑 Kode     : *$kode_baru*\n";
                $pesan .= "📅 Booking  : " . date('d-m-Y') . "\n";
                $pesan .= "⏳ Berlaku  : s/d " . date('d-m-Y', strtotime('+3 days')) . "\n\n";
                $pesan .= "Tunjukkan kode ini ke petugas perpustakaan untuk mengambil buku.\n";
                $pesan .= "Terima kasih! 🙏";

                // Kirim WhatsApp
                kirimWhatsApp($data_anggota['no_hp'], $pesan);

                $booking_success = "Booking berhasil! Kode <strong>$kode_baru</strong> telah dikirim ke WhatsApp kamu.";
            } else {
                $booking_error = 'Gagal menyimpan booking: ' . mysqli_error($koneksi);
            }
        }
    }
}

// ===============================
// KATEGORI & LIST BUKU
// ===============================

$list_kategori = mysqli_query(
    $koneksi,
    "SELECT * FROM kategori ORDER BY nama_kategori ASC"
);

$id_kategori = isset($_GET['kategori'])
    ? (int)$_GET['kategori']
    : 0;

if ($id_kategori > 0)
{
    $list_buku_booking = mysqli_query(
        $koneksi,
        "SELECT *
         FROM buku
         WHERE stok > 0
         AND id_kategori = '$id_kategori'
         ORDER BY judul ASC"
    );
}
else
{
    $list_buku_booking = mysqli_query(
        $koneksi,
        "SELECT *
         FROM buku
         WHERE stok > 0
         ORDER BY judul ASC"
    );
}

// Stat cards
$total_dipinjam = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM peminjaman
     WHERE id_anggota = $id_anggota AND status = 'Diambil'"))[0];

$total_dikembalikan = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM peminjaman
     WHERE id_anggota = $id_anggota AND status = 'Kembali'"))[0];

$total_booking = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM booking
     WHERE id_anggota = $id_anggota AND status = 'Booking'"))[0];

$total_terlambat = mysqli_fetch_row(mysqli_query($koneksi,
    "SELECT COUNT(*) FROM peminjaman
     WHERE id_anggota = $id_anggota
       AND status = 'Diambil'
       AND tanggal_kembali < CURDATE()"))[0];

// Tabel peminjaman aktif
$peminjaman_aktif = mysqli_query($koneksi,
    "SELECT p.*, b.judul, b.penulis
     FROM peminjaman p
     JOIN buku b ON p.id_buku = b.id_buku
     WHERE p.id_anggota = $id_anggota AND p.status = 'Diambil'
     ORDER BY p.tanggal_kembali ASC");

// Tabel booking aktif
$booking_aktif = mysqli_query($koneksi,
    "SELECT bk.*, b.judul, b.penulis
     FROM booking bk
     JOIN buku b ON bk.id_buku = b.id_buku
     WHERE bk.id_anggota = $id_anggota AND bk.status = 'Booking'
     ORDER BY bk.tanggal_expired ASC");

// Riwayat peminjaman (semua)
$riwayat = mysqli_query($koneksi,
    "SELECT p.*, b.judul
     FROM peminjaman p
     JOIN buku b ON p.id_buku = b.id_buku
     WHERE p.id_anggota = $id_anggota
     ORDER BY p.tanggal_pinjam DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Anggota — Library Space</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/web-perpustakaan/assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ANGGOTA (tema sama dengan admin) ===== -->
<nav class="navbar navbar-expand-lg navbar-dark" id="mainNavbar">
  <div class="container-fluid px-4">
    <a class="navbar-brand d-flex align-items-center gap-2"
       href="/web-perpustakaan/pages/anggota_area/">
      <span class="brand-icon"><i class="bi bi-book-half"></i></span>
      <span class="brand-name">Library Space</span>
    </a>

    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navAnggota">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navAnggota">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
        <li class="nav-item">
          <a class="nav-link active"
             href="/web-perpustakaan/pages/anggota_area/">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small">
          <i class="bi bi-person-circle me-1"></i>
          <?= htmlspecialchars($nama) ?>
        </span>
        <a href="/web-perpustakaan/logout_anggota.php"
           class="btn btn-sm btn-outline-light">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</nav>

<!-- ===== SIDEBAR ANGGOTA ===== -->
<aside class="sidebar" id="sidebar">
  <nav class="sidebar-nav">

    <div class="sidebar-section-label">MENU UTAMA</div>

    <a href="/web-perpustakaan/pages/anggota_area/"
       class="sidebar-link active">
      <i class="bi bi-speedometer2"></i>
      <span>Dashboard</span>
    </a>

    <div class="sidebar-section-label mt-3">TRANSAKSI SAYA</div>

    <a href="#form-booking" class="sidebar-link"
       onclick="scrollTo('form-booking')">
      <i class="bi bi-bookmark-plus"></i>
      <span>Booking Buku</span>
    </a>

    <a href="#tabel-pinjam" class="sidebar-link"
       onclick="scrollTo('tabel-pinjam')">
      <i class="bi bi-arrow-left-right"></i>
      <span>Peminjaman Aktif</span>
    </a>

    <a href="#tabel-booking" class="sidebar-link"
       onclick="scrollTo('tabel-booking')">
      <i class="bi bi-bookmark-check"></i>
      <span>Booking Aktif</span>
    </a>

    <a href="#tabel-riwayat" class="sidebar-link"
       onclick="scrollTo('tabel-riwayat')">
      <i class="bi bi-clock-history"></i>
      <span>Riwayat</span>
    </a>

    <div class="sidebar-section-label mt-3">AKUN</div>

    <a href="/web-perpustakaan/logout_anggota.php"
       class="sidebar-link text-danger-soft">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </a>

  </nav>
</aside>

<!-- ===== KONTEN UTAMA ===== -->
<div class="d-flex">
  <div class="main-wrapper flex-grow-1">
    <div class="pt-2">

      <!-- Page header -->
      <div class="page-header">
        <h1><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Anggota</h1>
        <p>Selamat datang, <strong><?= htmlspecialchars($nama) ?></strong>! Berikut ringkasan aktivitas perpustakaan kamu.</p>
      </div>

      <!-- Form Booking Buku -->
      <div class="card mb-4" id="form-booking">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-bookmark-plus text-primary"></i>
          Form Booking Buku
        </div>
        <div class="card-body p-4">

          <?php if ($booking_error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2">
              <i class="bi bi-exclamation-circle-fill"></i> <?= $booking_error ?>
            </div>
          <?php endif; ?>

          <?php if ($booking_success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2">
              <i class="bi bi-check-circle-fill"></i> <?= $booking_success ?>
            </div>
          <?php endif; ?>

          <p class="text-muted mb-3">Pilih buku yang ingin kamu booking. Kode booking akan dikirim ke WhatsApp kamu.</p>

          <!-- PILIH KATEGORI -->

<form method="GET" class="mb-4">

    <label class="form-label fw-semibold">
        Pilih Kategori Buku
    </label>

    <select
        name="kategori"
        class="form-select"
        onchange="this.form.submit()">

        <option value="">
            Semua Kategori
        </option>

        <?php while($k = mysqli_fetch_assoc($list_kategori)): ?>

        <option
            value="<?= $k['id_kategori'] ?>"
            <?= ($id_kategori == $k['id_kategori']) ? 'selected' : '' ?>>

            <?= htmlspecialchars($k['nama_kategori']) ?>

        </option>

        <?php endwhile; ?>

    </select>

</form>

<!-- PILIH BUKU -->

<form method="POST" action="#form-booking">

    <div class="mb-3">

        <label class="form-label fw-semibold">
            Pilih Buku
        </label>

        <select
            name="id_buku"
            class="form-select"
            required>

            <option value="">
                -- Pilih Buku --
            </option>

            <?php while($b = mysqli_fetch_assoc($list_buku_booking)): ?>

            <option value="<?= $b['id_buku'] ?>">

                <?= htmlspecialchars($b['judul']) ?>
                (Stok: <?= $b['stok'] ?>)

            </option>

            <?php endwhile; ?>

        </select>

    </div>

    <button
        type="submit"
        class="btn btn-primary">

        <i class="bi bi-bookmark-plus me-1"></i>
        Buat Booking

    </button>

</form>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon orange"><i class="bi bi-arrow-left-right"></i></div>
            <div>
              <div class="stat-label">Sedang Dipinjam</div>
              <div class="stat-value"><?= $total_dipinjam ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
            <div>
              <div class="stat-label">Sudah Dikembalikan</div>
              <div class="stat-value"><?= $total_dikembalikan ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon blue"><i class="bi bi-bookmark-check"></i></div>
            <div>
              <div class="stat-label">Booking Aktif</div>
              <div class="stat-value"><?= $total_booking ?></div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="stat-card">
            <div class="stat-icon red"><i class="bi bi-exclamation-circle"></i></div>
            <div>
              <div class="stat-label">Terlambat</div>
              <div class="stat-value"><?= $total_terlambat ?></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Peminjaman Aktif -->
      <div class="card mb-4" id="tabel-pinjam">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-arrow-left-right text-warning"></i>
          Peminjaman Aktif
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Judul Buku</th>
                  <th>Penulis</th>
                  <th>Tgl Pinjam</th>
                  <th>Batas Kembali</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($peminjaman_aktif) > 0):
                  while ($row = mysqli_fetch_assoc($peminjaman_aktif)):
                    $terlambat = strtotime($row['tanggal_kembali']) < strtotime('today');
                ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($row['penulis']) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])) ?></td>
                  <td>
                    <?php if ($terlambat): ?>
                      <span class="badge-status badge-dipinjam">⚠ Terlambat</span>
                    <?php else: ?>
                      <span class="badge-status badge-tersedia">Tepat Waktu</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                      Tidak ada buku yang sedang dipinjam.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Booking Aktif -->
      <div class="card mb-4" id="tabel-booking">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-bookmark-check text-primary"></i>
          Booking Aktif
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Kode Booking</th>
                  <th>Judul Buku</th>
                  <th>Penulis</th>
                  <th>Tgl Booking</th>
                  <th>Berlaku s/d</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($booking_aktif) > 0):
                  while ($row = mysqli_fetch_assoc($booking_aktif)):
                ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td><strong><?= htmlspecialchars($row['kode_booking']) ?></strong></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></td>
                  <td class="text-muted"><?= htmlspecialchars($row['penulis']) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_booking'])) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_expired'])) ?></td>
                </tr>
                <?php endwhile; else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      <i class="bi bi-bookmark fs-4 d-block mb-2"></i>
                      Tidak ada booking aktif.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Riwayat Peminjaman -->
      <div class="card" id="tabel-riwayat">
        <div class="card-header d-flex align-items-center gap-2">
          <i class="bi bi-clock-history text-success"></i>
          Riwayat Peminjaman
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Judul Buku</th>
                  <th>Tgl Pinjam</th>
                  <th>Tgl Kembali</th>
                  <th>Dikembalikan</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($riwayat) > 0):
                  while ($row = mysqli_fetch_assoc($riwayat)):
                ?>
                <tr>
                  <td class="text-muted"><?= $no++ ?></td>
                  <td class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])) ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])) ?></td>
                  <td>
                    <?= $row['tanggal_dikembalikan']
                        ? date('d-m-Y', strtotime($row['tanggal_dikembalikan']))
                        : '<span class="text-muted">—</span>' ?>
                  </td>
                  <td>
                    <?php if ($row['status'] === 'Kembali'): ?>
                      <span class="badge-status badge-kembali">Dikembalikan</span>
                    <?php else: ?>
                      <span class="badge-status badge-dipinjam">Dipinjam</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                      <i class="bi bi-clock-history fs-4 d-block mb-2"></i>
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
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Smooth scroll ke section saat klik sidebar
  function scrollTo(id) {
    event.preventDefault();
    document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
</script>
</body>
</html>