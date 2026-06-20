<?php
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

// Validasi login saja (role tidak tersedia di database.sql dan tidak diset di login.php)


    $id_user = $_GET['id'] ?? null;
$error = '';
$staff = null;

// Validasi id numeric supaya tidak memicu error/bind_param kosong
$id_user = filter_var($id_user, FILTER_VALIDATE_INT);

if ($id_user) {
    $query = "SELECT * FROM users WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $staff = mysqli_fetch_assoc($result);

    if (!$staff) {
        header('Location: /web-perpustakaan/pages/admin/?status=error');
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_user = $_POST['id_user'] ?? null;
    $id_user = filter_var($id_user, FILTER_VALIDATE_INT);
    $username = trim($_POST['username'] ?? '');

    $nama = trim($_POST['nama'] ?? '');
    // Kolom role tidak ada pada database.sql, jadi role tidak diproses.
    $password = $_POST['password'] ?? '';


    if (empty($username) || empty($nama)) {
        $error = 'Username dan nama wajib diisi!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } else {
        $check_query = "SELECT id_user FROM users WHERE username = ? AND id_user != ?";
        $stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($stmt, "si", $username, $id_user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Username sudah digunakan user lain!';
        } else {
            if (empty($password)) {
                $update_query = "UPDATE users SET username = ?, nama = ? WHERE id_user = ?";
                $stmt = mysqli_prepare($koneksi, $update_query);
                mysqli_stmt_bind_param($stmt, "ssi", $username, $nama, $id_user);

            } else {
                if (strlen($password) < 6) {
                    $error = 'Password minimal 6 karakter!';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    $update_query = "UPDATE users SET username = ?, password = ?, nama = ? WHERE id_user = ?";
                    $stmt = mysqli_prepare($koneksi, $update_query);
                    mysqli_stmt_bind_param($stmt, "sssi", $username, $hashed_password, $nama, $id_user);

                    // Note: database.sql tidak punya kolom role, jadi tidak diproses.


                }
            }

            if (empty($error) && isset($stmt) && $stmt && mysqli_stmt_execute($stmt)) {

                header('Location: /web-perpustakaan/pages/admin/?status=updated');
                exit;
            } elseif (empty($error)) {
                $error = 'Gagal mengubah data!';
            }
        }
    }
}
$page_title = 'Edit Admin';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-person-gear me-2 text-primary"></i>Edit Admin</h1>
        <p>Ubah data akun admin atau petugas.</p>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="max-width: 600px;">
        <div class="card-header bg-primary text-white">Form Edit Admin</div>
        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <?php if ($staff): ?>
            <form method="POST">
              <input type="hidden" name="id_user" value="<?= $staff['id_user'] ?>">

              <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                  required value="<?= htmlspecialchars($staff['username']) ?>">
              </div>

              <div class="mb-3">
                <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama"
                  required value="<?= htmlspecialchars($staff['nama']) ?>">
              </div>

              <hr>

              <div class="mb-4">
                <label for="password" class="form-label fw-semibold">Password Baru <span class="text-muted fw-normal">(opsional)</span></label>
                <input type="password" class="form-control" id="password" name="password"
                  placeholder="Kosongkan jika tidak ingin mengubah">
                <small class="text-muted">Minimum 6 karakter</small>
              </div>

              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                  <i class="bi bi-check-circle me-1"></i> Update
                </button>
                <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
              </div>
            </form>
          <?php else: ?>
            <div class="alert alert-danger">Data admin tidak ditemukan.</div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>