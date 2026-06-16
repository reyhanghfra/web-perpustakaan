<?php
/**
 * pages/anggota/tambah.php — Tambah Anggota
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Tambah Anggota';
require_once __DIR__ . '/../../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama   = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
  $alamat = trim(mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? ''));
  $no_hp  = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp'] ?? ''));

  if ($nama === '' || $alamat === '' || $no_hp === '') {
    $error = 'Semua kolom wajib diisi.';
  } else {
    $sql = "INSERT INTO anggota (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
    if (mysqli_query($koneksi, $sql)) {
      $success = 'Data anggota berhasil ditambahkan!';
      header('Refresh: 1; url=index.php');
      exit;
    } else {
      // Jangan tampilkan error SQL detail (biar tidak merah/berantakan)
      $error = 'Gagal menyimpan data anggota.';
    }
  }
}
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Anggota</h1>
        <p>Isi data anggota baru di bawah ini.</p>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="max-width: 800px;">
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
            <div class="mb-3">
              <label for="nama" class="form-label">Nama</label>
              <input type="text" id="nama" name="nama" class="form-control" required value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
            </div>

            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
              <label for="no_hp" class="form-label">No HP</label>
              <input type="text" id="no_hp" name="no_hp" class="form-control" required value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" placeholder="Contoh: 081234567890">
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> Simpan
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

