<?php
/**
 * login_anggota.php — Halaman Login Khusus Anggota
 */
session_start();

if (isset($_SESSION['anggota_login']) && $_SESSION['anggota_login'] === true) {
    header('Location: /perpustakaan/pages/anggota_area/');
    exit;
}
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: /perpustakaan/pages/dashboard/');
    exit;
}

require_once __DIR__ . '/config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $sql = "SELECT ua.id_users_anggota, ua.password, a.id_anggota, a.nama, a.no_hp
                FROM users_anggota ua
                JOIN anggota a ON ua.id_anggota = a.id_anggota
                WHERE ua.username = '$username'
                LIMIT 1";
        $result = mysqli_query($koneksi, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password'])) {
                $_SESSION['anggota_login']    = true;
                $_SESSION['anggota_id']       = $user['id_anggota'];
                $_SESSION['anggota_nama']     = $user['nama'];
                $_SESSION['anggota_no_hp']    = $user['no_hp'];

                header('Location: /perpustakaan/pages/anggota_area/');
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Anggota — Perpustakaan Mini</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/perpustakaan/assets/css/style.css">
</head>
<body class="login-page">

  <div class="login-card">
    <div class="login-logo">
      <i class="bi bi-book-half"></i>
    </div>

    <h4 class="text-center fw-bold mb-1">Login Anggota</h4>
    <p class="text-center text-muted small mb-4">Masuk dengan nama lengkap dan password kamu</p>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-auto-close d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Nama Lengkap</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="username" name="username" class="form-control"
                 placeholder="Masukkan nama lengkap"
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 autofocus required>
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Masukkan password" required>
          <button class="btn btn-outline-secondary" type="button"
                  onclick="togglePassword()">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
      </button>

      <div class="d-flex align-items-center gap-2 mb-3">
        <hr class="flex-grow-1 m-0">
        <span class="text-muted small">belum punya akun?</span>
        <hr class="flex-grow-1 m-0">
      </div>

      <a href="/perpustakaan/daftar.php" class="btn btn-outline-success w-100">
        <i class="bi bi-person-plus me-1"></i> Daftar sebagai Anggota
      </a>
    </form>

    <div class="text-center mt-3">
      <a href="/perpustakaan/login.php" class="text-muted small text-decoration-none">
        <i class="bi bi-shield-lock me-1"></i> Login sebagai Petugas
      </a>
    </div>

    <p class="text-center text-muted small mt-4 mb-0">
      Library Space &copy; <?= date('Y') ?>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="/perpustakaan/assets/js/main.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }
  </script>
</body>
</html>