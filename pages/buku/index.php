<?php
/**
 * pages/buku/index.php — Daftar Data Buku
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Data Buku';
require_once __DIR__ . '/../../includes/header.php';

$search = trim($_GET['search'] ?? '');
$where = '';
if ($search !== '') {
  $s = mysqli_real_escape_string($koneksi, $search);
  $where = "WHERE buku.judul LIKE '%$s%' OR buku.penulis LIKE '%$s%' OR buku.penerbit LIKE '%$s%' OR kategori.nama_kategori LIKE '%$s%'";
}

$sql = "SELECT buku.*, kategori.nama_kategori 
        FROM buku 
        JOIN kategori ON buku.id_kategori = kategori.id_kategori 
        $where
        ORDER BY buku.id_buku DESC";
$result = mysqli_query($koneksi, $sql);
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1><i class="bi bi-journals me-2 text-primary"></i>Data Buku</h1>
          <p>Kelola semua koleksi buku perpustakaan di sini.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Tambah Buku
        </a>
      </div>

      <!-- Search Bar -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
          <form method="GET" action="">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
              <input type="text" name="search" class="form-control border-start-0"
                placeholder="Cari judul, penulis, penerbit, atau kategori..."
                value="<?= htmlspecialchars($search) ?>">
              <?php if ($search): ?>
                <a href="index.php" class="btn btn-outline-secondary">
                  <i class="bi bi-x-lg"></i>
                </a>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary">Cari</button>
            </div>
          </form>
        </div>
      </div>

      <?php if ($search): ?>
        <p class="text-muted small mb-2">
          Menampilkan hasil pencarian untuk: <strong>"<?= htmlspecialchars($search) ?>"</strong>
          — <?= mysqli_num_rows($result) ?> data ditemukan
        </p>
      <?php endif; ?>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width: 60px;">No</th>
                  <th>Judul Buku</th>
                  <th>Penulis</th>
                  <th>Penerbit</th>
                  <th class="text-center">Tahun</th>
                  <th class="text-center">Stok</th>
                  <th>Kategori</th>
                  <th class="text-center" style="width: 120px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($result && mysqli_num_rows($result) > 0): 
                  $no = 1;
                  while ($row = mysqli_fetch_assoc($result)):
                ?>
                  <tr>
                    <td class="text-center text-muted"><?= $no++ ?></td>
                    <td class="fw-semibold text-dark"><?= htmlspecialchars($row['judul']) ?></td>
                    <td><?= htmlspecialchars($row['penulis']) ?></td>
                    <td><?= htmlspecialchars($row['penerbit']) ?></td>
                    <td class="text-center"><?= $row['tahun'] ?></td>
                    <td class="text-center">
                      <span class="badge bg-<?= $row['stok'] > 0 ? 'success' : 'danger' ?>">
                        <?= $row['stok'] ?>
                      </span>
                    </td>
                    <td><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                    <td class="text-center">
                      <button onclick="konfirmasiHapus('hapus.php?id=<?= $row['id_buku'] ?>', '<?= htmlspecialchars($row['judul']) ?>')" class="btn btn-sm btn-outline-danger" title="Hapus">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                <?php
                  endwhile;
                else:
                ?>
                  <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                      <i class="bi bi-book-half d-block display-4 mb-2 text-black-50"></i>
                      <?= $search ? 'Tidak ada buku yang cocok dengan pencarian.' : 'Belum ada data buku di database.' ?>
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
function konfirmasiHapus(url, judul) {
  if (confirm('Hapus buku "' + judul + '"?')) {
    window.location.href = url;
  }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>