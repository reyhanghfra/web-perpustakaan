<?php
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth_guard.php';


// In some file analyzers (intelephense), $koneksi bisa terbaca sebagai undefined.
// Di runtime, $koneksi diset oleh config/koneksi.php.


if (!isset($_SESSION['id_user'])) {

    header('Location: /web-perpustakaan/login.php');
    exit;
}
// Kolom role & created_at tidak ada pada database.sql (users hanya: id_user, username, password, nama, created_at)
// role tidak ada, jadi tampilkan tanpa role. created_at tetap tersedia.

$search = trim($_GET['search'] ?? '');

// Search: nama atau username
if ($search !== '') {
    $s = mysqli_real_escape_string($koneksi, $search);
    $query = "SELECT id_user, username, nama, created_at FROM users
              WHERE nama LIKE '%$s%' OR username LIKE '%$s%'
              ORDER BY created_at DESC";
} else {
    $query = "SELECT id_user, username, nama, created_at FROM users ORDER BY created_at DESC";
}

$result = mysqli_query($koneksi, $query);



$staff_list = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $staff_list[] = $row;
    }
}

$message = '';
$message_type = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'added') {
        $message = 'Staff berhasil ditambahkan!';
        $message_type = 'success';
    } elseif ($_GET['status'] === 'updated') {
        $message = 'Staff berhasil diperbarui!';
        $message_type = 'success';
    } elseif ($_GET['status'] === 'deleted') {
        $message = 'Staff berhasil dihapus!';
        $message_type = 'success';
    } elseif ($_GET['status'] === 'error') {
        $message = 'Terjadi kesalahan!';
        $message_type = 'danger';
    }
}

$page_title = 'Kelola Admin';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1><i class="bi bi-shield-lock me-2 text-primary"></i>Kelola Admin</h1>
          <p>Kelola data akun admin dan petugas perpustakaan.</p>
        </div>
        <a href="tambah.php" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Tambah Admin
        </a>
      </div>

      <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Search Bar -->
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
          <form method="GET" action="">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
              <input type="text" name="search" class="form-control border-start-0"
                placeholder="Cari nama atau username..."
                value="<?= htmlspecialchars($search ?? '') ?>">
              <?php if ($search): ?>
                <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
              <?php endif; ?>
              <button class="btn btn-warning text-white" type="submit">Cari</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Nama</th>
                  <th>Username</th>
                  <th>Dibuat</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($staff_list) > 0): ?>
                  <?php foreach ($staff_list as $staff): ?>
                    <tr>
                      <td><?= (int)$staff['id_user'] ?></td>
                      <td class="fw-semibold"><?= htmlspecialchars($staff['nama']) ?></td>
                      <td><code><?= htmlspecialchars($staff['username']) ?></code></td>
                      <td><small><?= htmlspecialchars($staff['created_at']) ?></small></td>
                      <td class="text-center">
                        <a href="edit.php?id=<?= (int)$staff['id_user'] ?>" class="btn btn-sm btn-outline-warning">
                          <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="hapus.php?id=<?= (int)$staff['id_user'] ?>" class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('Yakin ingin menghapus?')">
                          <i class="bi bi-trash"></i> Hapus
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="5" class="text-center text-muted py-5">Belum ada data admin.</td>
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

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>   