<?php
/**
 * pages/buku/tambah.php — Tambah Data Buku Baru
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Tambah Buku';
require_once __DIR__ . '/../../includes/header.php';

$error = '';
$success = '';

// Eksekusi ketika form disubmit (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan sanitasi data inputan form
    $judul       = trim(mysqli_real_escape_string($koneksi, $_POST['judul'] ?? ''));
    $penulis     = trim(mysqli_real_escape_string($koneksi, $_POST['penulis'] ?? ''));
    $penerbit    = trim(mysqli_real_escape_string($koneksi, $_POST['penerbit'] ?? ''));
    $tahun       = intval($_POST['tahun'] ?? 0);
    $stok        = intval($_POST['stok'] ?? 0);
    $id_kategori = intval($_POST['id_kategori'] ?? 0);

    // Validasi input wajib
    if ($judul === '' || $penulis === '' || $penerbit === '' || $tahun === 0 || $id_kategori === 0) {
        $error = 'Semua kolom formulir wajib diisi dengan benar.';
    } else {
        // Query SQL insert data ke tabel buku
        $sql = "INSERT INTO buku (judul, penulis, penerbit, tahun, stok, id_kategori) 
                VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$stok', '$id_kategori')";
        
        if (mysqli_query($koneksi, $sql)) {
            $success = 'Data buku baru berhasil ditambahkan!';
            // Mengarahkan kembali ke halaman index setelah 2 detik
            header('Refresh: 2; url=index.php');
        } else {
            $error = 'Gagal menyimpan data ke sistem: ' . mysqli_error($koneksi);
        }
    }
}

// Mengambil list opsi kategori dari database secara dinamis
$kategori_options = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Buku</h1>
        <p>Isi detail informasi buku di bawah ini secara lengkap.</p>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="max-width: 800px;">
        <div class="card-body p-4">
          
          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2 alert-auto-close mb-3" role="alert">
              <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label for="judul" class="form-label">Judul Buku</label>
              <input type="text" id="judul" name="judul" class="form-control" required placeholder="Contoh: Belajar Clean Code PHP">
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="penulis" class="form-label">Nama Penulis</label>
                <input type="text" id="penulis" name="penulis" class="form-control" required placeholder="Nama penulis">
              </div>
              <div class="col-md-6 mb-3">
                <label for="penerbit" class="form-label">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" class="form-control" required placeholder="Nama penerbit">
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="tahun" class="form-label">Tahun Terbit</label>
                <input type="number" id="tahun" name="tahun" class="form-control" min="1900" max="2100" value="<?= date('Y') ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="stok" class="form-label">Stok Buku</label>
                <input type="number" id="stok" name="stok" class="form-control" min="0" value="1" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="id_kategori" class="form-label">Kategori Buku</label>
                <select id="id_kategori" name="id_kategori" class="form-select" required>
                  <option value="" disabled selected>-- Pilih Kategori --</option>
                  <?php while($cat = mysqli_fetch_assoc($kategori_options)): ?>
                    <option value="<?= $cat['id_kategori'] ?>"><?= htmlspecialchars($cat['nama_kategori']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <div class="mt-4 pt-2 border-top d-flex gap-2">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> Simpan Buku
              </button>
              <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div></div><?php require_once __DIR__ . '/../../includes/footer.php'; ?>