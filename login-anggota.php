<?php
/**
 * login-anggota.php — Halaman Login Anggota
 */
session_start();

if (isset($_SESSION['anggota_login']) && $_SESSION['anggota_login'] === true) {
  header('Location: /web-perpustakaan/anggota/dashboard.php');
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
    $sql = "SELECT id_anggota, username, password, nama FROM anggota WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) === 1) {
      $anggota = mysqli_fetch_assoc($result);

      if (password_verify($password, $anggota['password'])) {
        $_SESSION['anggota_login']    = true;
        $_SESSION['anggota_id']       = $anggota['id_anggota'];
        $_SESSION['anggota_username'] = $anggota['username'];
        $_SESSION['anggota_nama']     = $anggota['nama'];

        header('Location: /web-perpustakaan/anggota/dashboard.php');
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
  <link rel="stylesheet" href="/web-perpustakaan/assets/css/style.css">
</head>

<body class="login-page">

  <div class="login-card">
    <div class="login-logo">
      <i class="bi bi-person-badge"></i>
    </div>

    <h4 class="text-center mb-1" style="font-weight:700;">Portal Anggota</h4>
    <p class="text-center text-muted small mb-4">Masuk ke portal anggota perpustakaan</p>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-3" role="alert">
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

      <button type="submit" class="btn btn-success w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
      </button>
    </form>

    <p class="text-center mt-3 mb-0 small">
      Login sebagai petugas? <a href="/web-perpustakaan/login.php">Klik di sini</a>
    </p>

    <p class="text-center text-muted small mt-3 mb-0">
      Library Space &copy; <?= date('Y') ?>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const icon = document.getElementById('eyeIcon');
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