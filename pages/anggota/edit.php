<?php
/**
 * pages/anggota/edit.php — Edit Anggota
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
  header('Location: index.php');
  exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota='$id'");
$row = mysqli_fetch_assoc($data);
if (!$row) {
  header('Location: index.php');
  exit;
}

$error = '';

if (isset($_POST['update'])) {
  $nama   = trim(mysqli_real_escape_string($koneksi, $_POST['nama'] ?? ''));
  $alamat = trim(mysqli_real_escape_string($koneksi, $_POST['alamat'] ?? ''));
  $no_hp  = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp'] ?? ''));

  if ($nama === '' || $alamat === '' || $no_hp === '') {
    $error = 'Semua kolom wajib diisi.';
  } else {
    $sql = "UPDATE anggota SET nama='$nama', alamat='$alamat', no_hp='$no_hp' WHERE id_anggota='$id'";
    if (mysqli_query($koneksi, $sql)) {
      header('Location: index.php');
      exit;
    } else {
      // Jangan tampilkan detail error SQL agar tampilan tidak merah
      $error = 'Gagal update data anggota.';
    }
  }
}

$page_title = 'Edit Anggota';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Anggota</h1>
        <p>Ubah data anggota perpustakaan.</p>
      </div>

      <div class="card border-0 shadow-sm" style="max-width: 800px;">
        <div class="card-header bg-primary text-white">Form Edit Anggota</div>
        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="">
            <div class="mb-3">
              <label class="form-label fw-semibold" for="nama">Nama</label>
              <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($_POST['nama'] ?? $row['nama']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="alamat">Alamat</label>
              <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($_POST['alamat'] ?? $row['alamat']) ?></textarea>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold" for="no_hp">No HP</label>
              <input type="text" id="no_hp" name="no_hp" class="form-control" value="<?= htmlspecialchars($_POST['no_hp'] ?? $row['no_hp']) ?>" required>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" name="update" class="btn btn-primary px-4">
                <i class="bi bi-check-circle me-1"></i>Update
              </button>
              <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
            </div>
          </form>

        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

