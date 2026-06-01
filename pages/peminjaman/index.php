<?php
// require_once __DIR__ . '/../../includes/auth_guard.php'; // Aktifkan jika login kelompok sudah benar
require_once __DIR__ . '/../../config/koneksi.php';

// Ambil struktur kolom tabel peminjaman untuk mencegah error sql
$cek_peminjaman = mysqli_query($koneksi, "SHOW COLUMNS FROM peminjaman");
$kolom_buku = 'id_buku';
$kolom_anggota = 'id_anggota';

while ($k = mysqli_fetch_assoc($cek_peminjaman)) {
    if ($k['Field'] == 'kode_buku') {
        $kolom_buku = 'kode_buku';
    }
    if ($k['Field'] == 'id_user') {
        $kolom_anggota = 'id_user';
    }
}

// Ambil struktur kolom tabel anggota/user
$cek_anggota = mysqli_query($koneksi, "SHOW COLUMNS FROM anggota");
$pk_anggota = 'id_anggota';
while ($a = mysqli_fetch_assoc($cek_anggota)) {
    if ($a['Field'] == 'id_user') {
        $pk_anggota = 'id_user';
    }
}

// Ambil struktur kolom tabel buku
$cek_buku = mysqli_query($koneksi, "SHOW COLUMNS FROM buku");
$pk_buku = 'id_buku';
while ($b = mysqli_fetch_assoc($cek_buku)) {
    if ($b['Field'] == 'kode_buku') {
        $pk_buku = 'kode_buku';
    }
}

// Proses Input Kode Booking
if (isset($_POST['proses_booking'])) {
    $kode_booking = mysqli_real_escape_string($koneksi, $_POST['kode_booking']);
    $cek_booking = mysqli_query($koneksi, "SELECT * FROM booking WHERE kode_booking = '$kode_booking' AND status = 'Booking'");
    
    if (mysqli_num_rows($cek_booking) > 0) {
        $data_b = mysqli_fetch_assoc($cek_booking);
        
        $val_anggota = isset($data_b[$kolom_anggota]) ? $data_b[$kolom_anggota] : (isset($data_b['id_anggota']) ? $data_b['id_anggota'] : $data_b['id_user']);
        $val_buku = isset($data_b[$kolom_buku]) ? $data_b[$kolom_buku] : (isset($data_b['id_buku']) ? $data_b['id_buku'] : $data_b['kode_buku']);
        
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+7 days'));

        $insert_pinjam = mysqli_query($koneksi, "INSERT INTO peminjaman (kode_booking, $kolom_anggota, $kolom_buku, tanggal_pinjam, tanggal_kembali, status) VALUES ('$kode_booking', '$val_anggota', '$val_buku', '$tgl_pinjam', '$tgl_kembali', 'Diambil')");
        
        if ($insert_pinjam) {
            mysqli_query($koneksi, "UPDATE booking SET status = 'Diambil' WHERE kode_booking = '$kode_booking'");
            echo "<script>alert('Transaksi Berhasil! Buku telah diambil.'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Kode Booking Tidak Valid, atau Sudah Dibatalkan/Diambil!');</script>";
    }
}

// Proses Kembalikan Buku
if (isset($_GET['aksi']) && $_GET['aksi'] == 'kembali') {
    $id_peminjaman = $_GET['id'];
    $tgl_dikembalikan = date('Y-m-d');
    $update_status = mysqli_query($koneksi, "UPDATE peminjaman SET tanggal_dikembalikan = '$tgl_dikembalikan', status = 'Kembali' WHERE id_peminjaman = '$id_peminjaman'");
    if ($update_status) {
        echo "<script>alert('Buku berhasil dikembalikan!'); window.location='index.php';</script>";
    }
}

// Proses Hapus Transaksi Salah
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id_peminjaman = $_GET['id'];
    $hapus_transaksi = mysqli_query($koneksi, "DELETE FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    if ($hapus_transaksi) {
        echo "<script>alert('Data transaksi salah berhasil dihapus!'); window.location='index.php';</script>";
    }
}

$page_title = 'Peminjaman';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>
    
    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-arrow-left-right me-2 text-warning"></i>Peminjaman</h1>
        <p>Kelola transaksi peminjaman buku.</p>
      </div>

      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="card-title mb-0"><i class="bi bi-qr-code-scan me-2"></i>Input & Verifikasi Kode Booking Baru</h5>
        </div>
        <div class="card-body">
          <form action="" method="POST">
            <div class="mb-3">
              <label for="kode_booking" class="form-label">Masukkan Kode Booking dari Anggota</label>
              <input type="text" class="form-control" id="kode_booking" name="kode_booking" placeholder="Contoh: BK-2026052601" required>
            </div>
            <button type="submit" name="proses_booking" class="btn btn-success">
              <i class="bi bi-check-circle me-1"></i> Verifikasi & Berikan Buku
            </button>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-dark text-white">
          <h5 class="card-title mb-0"><i class="bi bi-table me-2"></i>Riwayat Transaksi Peminjaman</h5>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Nama Anggota</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Batas Kembali</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              
              // Query Join adaptif menggunakan variabel hasil cek kolom database tadi
              $query = "SELECT p.*, a.nama AS nama_anggota, b.judul AS judul_buku 
                        FROM peminjaman p
                        LEFT JOIN anggota a ON p.$kolom_anggota = a.$pk_anggota
                        LEFT JOIN buku b ON p.$kolom_buku = b.$pk_buku
                        ORDER BY p.id_peminjaman DESC";
              
              $tampil = mysqli_query($koneksi, $query);
              
              if ($tampil && mysqli_num_rows($tampil) > 0) {
                  while ($row = mysqli_fetch_assoc($tampil)) {
              ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><strong><?= $row['kode_booking']; ?></strong></td>
                  <td><?= $row['nama_anggota'] ?? '<span class="text-muted">Umum/Anonim</span>'; ?></td>
                  <td><?= $row['judul_buku'] ?? '<span class="text-muted">Buku tidak ditemukan</span>'; ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_pinjam'])); ?></td>
                  <td><?= date('d-m-Y', strtotime($row['tanggal_kembali'])); ?></td>
                  <td>
                    <?php if($row['status'] == 'Diambil' || $row['status'] == 'Sedang Dipinjam'): ?>
                      <span class="badge bg-warning text-dark">Sedang Dipinjam</span>
                    <?php else: ?>
                      <span class="badge bg-success">Sudah Kembali</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if($row['status'] == 'Diambil' || $row['status'] == 'Sedang Dipinjam'): ?>
                      <a href="index.php?aksi=kembali&id=<?= $row['id_peminjaman']; ?>" class="btn btn-sm btn-primary me-1" onclick="return confirm('Proses pengembalian buku ini?')">
                        <i class="bi bi-arrow-counterclockwise"></i> Kembali
                      </a>
                    <?php endif; ?>
                    <a href="index.php?aksi=hapus&id=<?= $row['id_peminjaman']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi salah ini?')">
                      <i class="bi bi-trash"></i> Hapus
                    </a>
                  </td>
                </tr>
              <?php 
                  }
              } else {
                  echo '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi peminjaman.</td></tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>