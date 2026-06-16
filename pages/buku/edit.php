<?php
/**
 * pages/buku/edit.php — Edit Data Buku
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

// Validasi ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Ambil data buku berdasarkan ID
$data = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id'");
$row  = mysqli_fetch_assoc($data);
if (!$row) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

// Proses update ketika form disubmit
if (isset($_POST['update'])) {
    $judul       = trim(mysqli_real_escape_string($koneksi, $_POST['judul']       ?? ''));
    $penulis     = trim(mysqli_real_escape_string($koneksi, $_POST['penulis']     ?? ''));
    $penerbit    = trim(mysqli_real_escape_string($koneksi, $_POST['penerbit']    ?? ''));
    $tahun       = intval($_POST['tahun']       ?? 0);
    $stok        = intval($_POST['stok']        ?? 0);
    $id_kategori = intval($_POST['id_kategori'] ?? 0);

    if ($judul === '' || $penulis === '' || $penerbit === '' || $tahun === 0 || $id_kategori === 0) {
        $error = 'Semua kolom formulir wajib diisi dengan benar.';
    } else {
        $sql = "UPDATE buku 
                SET judul='$judul', penulis='$penulis', penerbit='$penerbit',
                    tahun='$tahun', stok='$stok', id_kategori='$id_kategori'
                WHERE id_buku='$id'";

        if (mysqli_query($koneksi, $sql)) {
            $success = 'Data buku berhasil diperbarui!';
            header('Refresh: 1.5; url=index.php');
            // Refresh $row agar form menampilkan nilai terbaru
            $data = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id'");
            $row  = mysqli_fetch_assoc($data);
        } else {
            $error = 'Gagal memperbarui data: ' . mysqli_error($koneksi);
        }
    }
}

// Ambil daftar kategori untuk dropdown
$kategori_options = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

$page_title = 'Edit Buku';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Buku</h1>
        <p>Ubah informasi buku yang sudah tersimpan di perpustakaan.</p>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="max-width: 800px;">
        <div class="card-header bg-primary text-white fw-semibold">
          Form Edit Buku
        </div>
        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-check-circle-fill"></i>
              <?= htmlspecialchars($success) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <!-- Judul Buku -->
            <div class="mb-3">
              <label for="judul" class="form-label fw-semibold">Judul Buku</label>
              <input type="text" id="judul" name="judul" class="form-control"
                value="<?= htmlspecialchars($_POST['judul'] ?? $row['judul']) ?>"
                placeholder="Contoh: Belajar Clean Code PHP" required>
            </div>

            <!-- Penulis & Penerbit -->
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="penulis" class="form-label fw-semibold">Nama Penulis</label>
                <input type="text" id="penulis" name="penulis" class="form-control"
                  value="<?= htmlspecialchars($_POST['penulis'] ?? $row['penulis']) ?>"
                  placeholder="Nama penulis" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="penerbit" class="form-label fw-semibold">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" class="form-control"
                  value="<?= htmlspecialchars($_POST['penerbit'] ?? $row['penerbit']) ?>"
                  placeholder="Nama penerbit" required>
              </div>
            </div>

            <!-- Tahun, Stok, Kategori -->
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="tahun" class="form-label fw-semibold">Tahun Terbit</label>
                <input type="number" id="tahun" name="tahun" class="form-control"
                  min="1900" max="2100"
                  value="<?= intval($_POST['tahun'] ?? $row['tahun']) ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="stok" class="form-label fw-semibold">Stok Buku</label>
                <input type="number" id="stok" name="stok" class="form-control"
                  min="0"
                  value="<?= intval($_POST['stok'] ?? $row['stok']) ?>" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="id_kategori" class="form-label fw-semibold">Kategori Buku</label>
                <select id="id_kategori" name="id_kategori" class="form-select" required>
                  <option value="" disabled>-- Pilih Kategori --</option>
                  <?php
                  $selected_kat = intval($_POST['id_kategori'] ?? $row['id_kategori']);
                  while ($cat = mysqli_fetch_assoc($kategori_options)):
                  ?>
                    <option value="<?= $cat['id_kategori'] ?>"
                      <?= $cat['id_kategori'] == $selected_kat ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cat['nama_kategori']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 pt-2 border-top d-flex gap-2">
              <button type="submit" name="update" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i> Update Buku
              </button>
              <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>