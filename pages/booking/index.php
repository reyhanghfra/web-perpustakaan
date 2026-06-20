<?php
/**
 * pages/booking/index.php — Halaman Booking Buku
 * Saat booking berhasil, kode booking dikirim ke WhatsApp anggota
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Booking Buku';

// Catatan: form pembuatan booking baru kini berada di dashboard anggota
// (pages/anggota_area/index.php). Halaman ini hanya untuk admin memproses
// booking (saat anggota datang mengambil buku) menjadi peminjaman aktif.

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

<?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-auto-close d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          Booking berhasil diproses! Buku sudah tercatat sebagai dipinjam dan notifikasi WhatsApp telah dikirim.
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-auto-close d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?php
          $err = $_GET['error'];
          echo match($err) {
            'notfound' => 'Booking tidak ditemukan atau sudah diproses.',
            'stok'     => 'Stok buku sudah habis, tidak bisa diproses.',
            'pinjam'   => 'Gagal membuat data peminjaman.',
            default    => 'Terjadi kesalahan.'
          };
          ?>
        </div>
      <?php endif; ?>

      <div class="page-header mb-4">
        <h1><i class="bi bi-bookmark-check me-2 text-primary"></i>Booking Buku</h1>
        <p>Daftar booking yang dibuat anggota. Proses di sini saat anggota datang mengambil buku.</p>
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
                <th>Aksi</th>
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
                <td>
                  <?php if ($row['status'] === 'Booking'): ?>
                    <button class="btn btn-sm btn-success"
                            onclick="konfirmasiProses('<?= $row['id_booking'] ?>', '<?= htmlspecialchars($row['kode_booking']) ?>', '<?= htmlspecialchars($row['nama_anggota']) ?>', '<?= htmlspecialchars($row['judul_buku']) ?>')">
                      <i class="bi bi-check-circle me-1"></i> Proses
                    </button>
                  <?php elseif ($row['status'] === 'Batal'): ?>
                    <span class="text-muted small">—</span>
                  <?php else: ?>
                    <span class="text-muted small">Selesai</span>
                  <?php endif; ?>
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

<!-- Modal Konfirmasi Proses Booking -->
<div class="modal fade" id="modalProses" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-check-circle text-success me-2"></i>Proses Booking
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body px-4">
        <p class="mb-3">Konfirmasi peminjaman buku berikut:</p>
        <table class="table table-borderless table-sm mb-0">
          <tr>
            <td class="text-muted" style="width:130px">Kode Booking</td>
            <td><strong id="modal_kode"></strong></td>
          </tr>
          <tr>
            <td class="text-muted">Anggota</td>
            <td id="modal_anggota"></td>
          </tr>
          <tr>
            <td class="text-muted">Judul Buku</td>
            <td id="modal_buku"></td>
          </tr>
          <tr>
            <td class="text-muted">Tgl Pinjam</td>
            <td><?= date('d-m-Y') ?></td>
          </tr>
          <tr>
            <td class="text-muted">Batas Kembali</td>
            <td><?= date('d-m-Y', strtotime('+7 days')) ?> <span class="text-muted small">(7 hari)</span></td>
          </tr>
        </table>
      </div>
      <div class="modal-footer border-0 gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Batal
        </button>
        <form method="POST" action="/web-perpustakaan/pages/booking/proses_booking.php" id="formProses">
          <input type="hidden" name="id_booking" id="input_id_booking">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i> Ya, Proses Sekarang
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function konfirmasiProses(id, kode, anggota, buku) {
  document.getElementById('modal_kode').textContent    = kode;
  document.getElementById('modal_anggota').textContent = anggota;
  document.getElementById('modal_buku').textContent    = buku;
  document.getElementById('input_id_booking').value   = id;
  new bootstrap.Modal(document.getElementById('modalProses')).show();
}
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>