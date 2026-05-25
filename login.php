<?php
/**
 * login.php — Halaman Login
 */
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: /perpustakaan/pages/dashboard/');
    exit;
}

require_once __DIR__ . '/config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & sanitasi input
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Validasi tidak boleh kosong
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Cek username di database
        $sql  = "SELECT id_user, username, password, nama FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($koneksi, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password dengan password_verify()
            if (password_verify($password, $user['password'])) {
                $_SESSION['login']   = true;
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username']= $user['username'];
                $_SESSION['nama']    = $user['nama'];

                header('Location: /perpustakaan/pages/dashboard/');
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
  <title>Login — Perpustakaan Mini</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/perpustakaan/assets/css/style.css">
</head>
<body class="login-page">

  <div class="login-card">
    <!-- Logo -->
    <div class="login-logo">
      <i class="bi bi-book-half"></i>
    </div>

    <h4 class="text-center fw-700 mb-1" style="font-weight:700;">Library Space</h4>
    <p class="text-center text-muted small mb-4">Masuk ke sistem manajemen perpustakaan</p>

    <?php if ($error): ?>
      <div class="alert alert-danger alert-auto-close d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="username" name="username" class="form-control"
                 placeholder="Masukkan username"
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
                  onclick="togglePassword()" title="Tampilkan password">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
      </button>
    </form>

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
