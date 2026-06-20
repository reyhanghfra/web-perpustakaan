<?php
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/auth_guard.php';

// Untuk IDE (intelephense): $koneksi memang berasal dari config/koneksi.php.
/** @var mysqli $koneksi */


// Validasi login saja (role tidak diset karena tabel users di database.sql tidak punya kolom role)
// Auth guard sudah menangani redirect jika user belum login.

// In some file analyzers (intelephense), $koneksi bisa terbaca sebagai undefined.
// Di runtime, $koneksi diset oleh config/koneksi.php.

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    // Kolom role tidak ada di database.sql, jadi role tidak diproses.
    // $role dihapus karena tidak dipakai.





    if (empty($username) || empty($password) || empty($nama)) {
        $error = 'Semua field wajib diisi!';
    } elseif (strlen($username) < 3) {
        $error = 'Username minimal 3 karakter!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } else {
        $check_query = "SELECT id_user FROM users WHERE username = ?";
        $stmt = mysqli_prepare($koneksi, $check_query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error = 'Username sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $insert_query = "INSERT INTO users (username, password, nama) VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($koneksi, $insert_query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $nama);


            if (mysqli_stmt_execute($stmt)) {
                header('Location: /web-perpustakaan/pages/admin/?status=added');
                exit;
            } else {
                $error = 'Gagal menambahkan staff!';
            }
        }
    }
}
$page_title = 'Tambah Admin';
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">
  <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <div class="main-wrapper flex-grow-1">
    <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

    <div class="pt-2 px-4">
      <div class="page-header mb-4">
        <h1><i class="bi bi-person-plus me-2 text-primary"></i>Tambah Admin</h1>
        <p>Tambahkan akun admin atau petugas baru.</p>
      </div>

      <div class="card shadow-sm border-0 mb-4" style="max-width: 600px;">
        <div class="card-body p-4">

          <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
              <i class="bi bi-exclamation-triangle-fill"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="POST">
            <div class="mb-3">
              <label for="username" class="form-label fw-semibold">Username</label>
              <input type="text" class="form-control" id="username" name="username"
                placeholder="Minimal 3 karakter" required>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">Password</label>
              <input type="password" class="form-control" id="password" name="password"
                placeholder="Minimal 6 karakter" required>
            </div>

            <div class="mb-3">
              <label for="confirm_password" class="form-label fw-semibold">Konfirmasi Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                placeholder="Ulangi password" required>
            </div>

            <div class="mb-4">
              <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
              <input type="text" class="form-control" id="nama" name="nama"
                placeholder="Nama lengkap" required>
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