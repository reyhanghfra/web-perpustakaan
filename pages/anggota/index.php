<?php
/**
 * pages/anggota/index.php — Data Anggota (CRUD)
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Data Anggota';
require_once __DIR__ . '/../../includes/header.php';

// Ambil semua data anggota
$sql = "SELECT * FROM anggota ORDER BY id_anggota DESC";
$result = mysqli_query($koneksi, $sql);
if ($result === false) {
  // Jangan tampilkan error SQL merah yang bisa mengganggu tampilan
  // cukup anggap tidak ada data
  $result = null;
}
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1><i class="bi bi-people me-2 text-success"></i>Data Anggota</h1>
          <p>Kelola data anggota perpustakaan.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Tambah Anggota
        </a>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width: 60px;">No</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>No HP</th>
                  <th class="text-center" style="width: 160px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
<?php if ($result && mysqli_num_rows($result) > 0): ?>
                  <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                      <td class="text-center text-muted"><?= $no++ ?></td>
                      <td class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></td>
                      <td>
                        <div class="text-truncate" style="max-width: 360px;" title="<?= htmlspecialchars($row['alamat']) ?>">
                          <?= htmlspecialchars($row['alamat']) ?>
                        </div>
                      </td>
                      <td><?= htmlspecialchars($row['no_hp']) ?></td>
                      <td class="text-center">
                        <a href="edit.php?id=<?= (int)$row['id_anggota'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <button
                          onclick="konfirmasiHapus('hapus.php?id=<?= (int)$row['id_anggota'] ?>', '<?= htmlspecialchars($row['nama']) ?>')"
                          class="btn btn-sm btn-outline-danger"
                          title="Hapus">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
<?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-5">
                      <i class="bi bi-people d-block display-4 mb-2 text-black-50"></i>
                      Belum ada data anggota di database.
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

<script>
  function konfirmasiHapus(url, nama) {
    if (confirm('Hapus data anggota ' + nama + '?')) {
      window.location.href = url;
    }
  }
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

