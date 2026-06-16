<?php
/**
 * pages/admin/index.php — Data Admin/Staff
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Data Admin';
require_once __DIR__ . '/../../includes/header.php';

$query = "SELECT id_user, username, nama, role, created_at FROM users ORDER BY created_at DESC";
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
    if ($_GET['status'] === 'added')   { $message = 'Staff berhasil ditambahkan!'; $message_type = 'success'; }
    elseif ($_GET['status'] === 'updated') { $message = 'Staff berhasil diperbarui!'; $message_type = 'success'; }
    elseif ($_GET['status'] === 'deleted') { $message = 'Staff berhasil dihapus!'; $message_type = 'success'; }
    elseif ($_GET['status'] === 'error')   { $message = 'Terjadi kesalahan!'; $message_type = 'danger'; }
}
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">

      <?php if ($message): ?>
        <div class="alert alert-<?= $message_type ?> alert-dismissible fade show mt-3" role="alert">
          <?= htmlspecialchars($message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1><i class="bi bi-person-gear me-2 text-primary"></i>Data Admin/Staff</h1>
          <p>Kelola akun admin dan petugas perpustakaan.</p>
        </div>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="tambah.php" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Tambah Staff
        </a>
        <?php endif; ?>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width:60px;">#</th>
                  <th>Username</th>
                  <th>Nama</th>
                  <th>Role</th>
                  <th>Terdaftar</th>
                  <?php if ($_SESSION['role'] === 'admin'): ?>
                  <th class="text-center" style="width:140px;">Aksi</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (count($staff_list) > 0):
                  $no = 1;
                  foreach ($staff_list as $staff): ?>
                  <tr>
                    <td class="text-center text-muted"><?= $no++ ?></td>
                    <td><code><?= htmlspecialchars($staff['username']) ?></code></td>
                    <td class="fw-semibold"><?= htmlspecialchars($staff['nama']) ?></td>
                    <td>
                      <?php if ($staff['role'] === 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                      <?php else: ?>
                        <span class="badge bg-primary">Petugas</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-muted small"><?= date('d M Y', strtotime($staff['created_at'])) ?></td>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                    <td class="text-center">
                      <a href="edit.php?id=<?= $staff['id_user'] ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="hapus.php?id=<?= $staff['id_user'] ?>"
                         class="btn btn-sm btn-outline-danger"
                         onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                        <i class="bi bi-trash"></i>
                      </a>
                    </td>
                    <?php endif; ?>
                  </tr>
                <?php endforeach; else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                      <i class="bi bi-people d-block display-4 mb-2 text-black-50"></i>
                      Belum ada data staff.
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer text-muted small">
          Total: <?= count($staff_list) ?> staff
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>