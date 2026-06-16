<?php
/**
 * pages/booking/index.php — Halaman Booking Buku
 * Saat booking berhasil, kode booking dikirim ke WhatsApp anggota
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/whatsapp.php';

$page_title = 'Booking Buku';
$error   = '';
$success = '';
$kode_baru = '';

// Proses form booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota = intval($_POST['id_anggota'] ?? 0);
    $id_buku    = intval($_POST['id_buku']    ?? 0);

    if ($id_anggota === 0 || $id_buku === 0) {
        $error = 'Pilih anggota dan buku terlebih dahulu.';
    } else {
        // Cek stok buku
        $cek_stok = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT stok, judul FROM buku WHERE id_buku = $id_buku"));

        if (!$cek_stok || $cek_stok['stok'] < 1) {
            $error = 'Stok buku tidak tersedia.';
        } else {
            // Generate kode booking unik: BK-YYYYMMDD + 4 digit random
            $kode_baru = 'BK-' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

            // Simpan ke tabel booking
            $tgl_booking  = date('Y-m-d');
            $tgl_expired  = date('Y-m-d', strtotime('+3 days')); // booking berlaku 3 hari

            $sql_insert = "INSERT INTO booking 
                            (kode_booking, id_anggota, id_buku, tanggal_booking, tanggal_expired, status)
                           VALUES 
                            ('$kode_baru', $id_anggota, $id_buku, '$tgl_booking', '$tgl_expired', 'Booking')";

            if (mysqli_query($koneksi, $sql_insert)) {
                // Ambil data anggota untuk kirim WA
                $anggota = mysqli_fetch_assoc(mysqli_query($koneksi,
                    "SELECT nama, no_hp FROM anggota WHERE id_anggota = $id_anggota"));

                $nama_buku = htmlspecialchars($cek_stok['judul']);
                $nama_anggota = htmlspecialchars($anggota['nama']);

                // Susun pesan WhatsApp
                $pesan = "📚 *PERPUSTAKAAN MINI*\n\n";
                $pesan .= "Halo, *$nama_anggota*! 👋\n\n";
                $pesan .= "Booking buku kamu berhasil dibuat.\n\n";
                $pesan .= "📖 Buku     : *$nama_buku*\n";
                $pesan .= "🔑 Kode     : *$kode_baru*\n";
                $pesan .= "📅 Booking  : " . date('d-m-Y') . "\n";
                $pesan .= "⏳ Berlaku  : s/d " . date('d-m-Y', strtotime('+3 days')) . "\n\n";
                $pesan .= "Tunjukkan kode ini ke petugas perpustakaan untuk mengambil buku.\n";
                $pesan .= "Terima kasih! 🙏";

                // Kirim WhatsApp
                kirimWhatsApp($anggota['no_hp'], $pesan);

                $success = "Booking berhasil! Kode <strong>$kode_baru</strong> telah dikirim ke WhatsApp anggota.";
            } else {
                $error = 'Gagal menyimpan booking: ' . mysqli_error($koneksi);
            }
        }
    }
}

// Ambil list anggota & buku untuk dropdown
$list_anggota = mysqli_query($koneksi, "SELECT id_anggota, nama, no_hp FROM anggota ORDER BY nama ASC");
$list_buku    = mysqli_query($koneksi, "SELECT id_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul ASC");

// Ambil riwayat booking
$riwayat = mysqli_query($koneksi,
    "SELECT b.*, a.nama AS nama_anggota, a.no_hp, bk.judul AS judul_buku
     FROM booking b
     LEFT JOIN anggota a  ON b.id_anggota = a.id_anggota
     LEFT JOIN buku bk    ON b.id_buku    = bk.id_buku
     ORDER BY b.id_booking DESC LIMIT 20");

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
    <div class="pt-2 px-4">

      <div class="page-header mb-4">
        <h1><i class="bi bi-bookmark-check me-2 text-primary"></i>Booking Buku</h1>
        <p>Buat booking buku untuk anggota. Kode booking otomatis dikirim ke WhatsApp anggota.</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger alert-auto-close d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-circle-fill"></i> <?= $error ?>
        </div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i> <?= $success ?>
        </div>
      <?php endif; ?>

      <!-- Form Booking -->
      <div class="card shadow-sm border-0 mb-4" style="max-width: 700px;">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0"><i class="bi bi-plus-circle me-2"></i>Form Booking Baru</h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="">
            <div class="mb-3">
              <label for="id_anggota" class="form-label">Nama Anggota</label>
              <select id="id_anggota" name="id_anggota" class="form-select" required>
                <option value="" disabled selected>-- Pilih Anggota --</option>
                <?php while ($a = mysqli_fetch_assoc($list_anggota)): ?>
                  <option value="<?= $a['id_anggota'] ?>">
                    <?= htmlspecialchars($a['nama']) ?> (<?= htmlspecialchars($a['no_hp']) ?>)
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="mb-4">
              <label for="id_buku" class="form-label">Buku yang Dibooking</label>
              <select id="id_buku" name="id_buku" class="form-select" required>
                <option value="" disabled selected>-- Pilih Buku --</option>
                <?php while ($b = mysqli_fetch_assoc($list_buku)): ?>
                  <option value="<?= $b['id_buku'] ?>">
                    <?= htmlspecialchars($b['judul']) ?> (Stok: <?= $b['stok'] ?>)
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">
              <i class="bi bi-send me-1"></i> Buat Booking & Kirim WhatsApp
            </button>
          </form>
        </div>
      </div>

      <!-- Riwayat Booking -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white">
          <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Booking</h5>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Anggota</th>
                <th>No. HP</th>
                <th>Judul Buku</th>
                <th>Tgl Booking</th>
                <th>Berlaku s/d</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              if ($riwayat && mysqli_num_rows($riwayat) > 0):
                while ($row = mysqli_fetch_assoc($riwayat)):
              ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td><strong><?= htmlspecialchars($row['kode_booking']) ?></strong></td>
                <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                <td class="text-muted"><?= htmlspecialchars($row['no_hp']) ?></td>
                <td><?= htmlspecialchars($row['judul_buku']) ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_booking'])) ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_expired'])) ?></td>
                <td>
                  <?php
                  $badge = match($row['status']) {
                    'Booking'  => 'bg-warning text-dark',
                    'Diambil'  => 'bg-success',
                    'Batal'    => 'bg-danger',
                    default    => 'bg-secondary'
                  };
                  ?>
                  <span class="badge <?= $badge ?>"><?= $row['status'] ?></span>
                </td>
              </tr>
              <?php
                endwhile;
              else:
              ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data booking.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>