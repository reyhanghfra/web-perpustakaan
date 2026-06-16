<?php
/**
 * daftar.php — Halaman Pendaftaran Anggota Baru
 */
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: /perpustakaan/pages/dashboard/');
    exit;
}
if (isset($_SESSION['anggota_login']) && $_SESSION['anggota_login'] === true) {
    header('Location: /perpustakaan/pages/anggota_area/');
    exit;
}

require_once __DIR__ . '/config/koneksi.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama']     ?? ''));
    $no_hp    = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp']    ?? ''));
    $password = trim($_POST['password'] ?? '');
    $konfirm  = trim($_POST['konfirmasi_password'] ?? '');

    // Validasi kosong
    if ($nama === '' || $no_hp === '' || $password === '') {
        $error = 'Semua kolom wajib diisi.';
    }
    // Validasi format no HP
    elseif (!preg_match('/^[0-9]{10,15}$/', $no_hp)) {
        $error = 'Nomor HP tidak valid. Gunakan format: 081234567890 (10–15 digit angka).';
    }
    // Validasi panjang password
    elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    }
    // Validasi konfirmasi password
    elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    }
    else {
        // Cek no HP sudah terdaftar
        $cek_hp = mysqli_query($koneksi,
            "SELECT id_anggota FROM anggota WHERE no_hp = '$no_hp' LIMIT 1");
        if (mysqli_num_rows($cek_hp) > 0) {
            $error = 'Nomor HP tersebut sudah terdaftar.';
        } else {
            // Cek nama sudah dipakai sebagai username
            $username_baru = mysqli_real_escape_string($koneksi, $nama);
            $cek_user = mysqli_query($koneksi,
                "SELECT id_users_anggota FROM users_anggota WHERE username = '$username_baru' LIMIT 1");
            if (mysqli_num_rows($cek_user) > 0) {
                $error = 'Nama tersebut sudah digunakan. Gunakan nama yang berbeda.';
            } else {
                // Simpan ke tabel anggota (alamat dikosongkan / default)
                $sql_anggota = "INSERT INTO anggota (nama, alamat, no_hp)
                                VALUES ('$nama', '-', '$no_hp')";

                if (mysqli_query($koneksi, $sql_anggota)) {
                    $id_anggota_baru = mysqli_insert_id($koneksi);
                    $hash_password   = password_hash($password, PASSWORD_DEFAULT);

                    // Simpan ke tabel users_anggota
                    $sql_user = "INSERT INTO users_anggota (id_anggota, username, password)
                                 VALUES ($id_anggota_baru, '$username_baru', '$hash_password')";

                    if (mysqli_query($koneksi, $sql_user)) {
                        $success = true;
                    } else {
                        // Rollback: hapus anggota yang baru dibuat kalau user gagal
                        mysqli_query($koneksi,
                            "DELETE FROM anggota WHERE id_anggota = $id_anggota_baru");
                        $error = 'Gagal membuat akun: ' . mysqli_error($koneksi);
                    }
                } else {
                    $error = 'Gagal menyimpan data: ' . mysqli_error($koneksi);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Anggota — Perpustakaan Mini</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/perpustakaan/assets/css/style.css">
</head>
<body class="login-page">

  <div class="login-card" style="max-width: 480px;">
    <div class="login-logo">
      <i class="bi bi-person-plus"></i>
    </div>

    <h4 class="text-center fw-bold mb-1">Daftar Anggota</h4>
    <p class="text-center text-muted small mb-4">Buat akun untuk mengakses layanan perpustakaan</p>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        Pendaftaran berhasil! Silakan login dengan nama lengkap dan password kamu.
      </div>
      <a href="/perpustakaan/login_anggota.php" class="btn btn-primary w-100 mb-2">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login Sekarang
      </a>
      <a href="/perpustakaan/login.php" class="btn btn-outline-secondary w-100">
        <i class="bi bi-shield-lock me-1"></i> Login sebagai Petugas
      </a>
    <?php else: ?>

    <form method="POST" action="">
      <!-- Nama Lengkap -->
      <div class="mb-3">
        <label for="nama" class="form-label">Nama Lengkap</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="nama" name="nama" class="form-control"
                 placeholder="Masukkan nama lengkap"
                 value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                 autofocus required>
        </div>
        <div class="form-text">Nama ini akan digunakan sebagai username saat login.</div>
      </div>

      <!-- Nomor HP -->
      <div class="mb-3">
        <label for="no_hp" class="form-label">Nomor HP / WhatsApp</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-telephone"></i></span>
          <input type="text" id="no_hp" name="no_hp" class="form-control"
                 placeholder="Contoh: 081234567890"
                 value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>"
                 maxlength="15" required>
        </div>
        <div class="form-text">Nomor ini digunakan untuk notifikasi booking via WhatsApp.</div>
      </div>

      <!-- Password -->
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Minimal 6 karakter" required>
          <button class="btn btn-outline-secondary" type="button"
                  onclick="togglePass('password', 'eye1')">
            <i class="bi bi-eye" id="eye1"></i>
          </button>
        </div>
      </div>

      <!-- Konfirmasi Password -->
      <div class="mb-4">
        <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input type="password" id="konfirmasi_password" name="konfirmasi_password"
                 class="form-control" placeholder="Ulangi password" required>
          <button class="btn btn-outline-secondary" type="button"
                  onclick="togglePass('konfirmasi_password', 'eye2')">
            <i class="bi bi-eye" id="eye2"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-success w-100 mb-3">
        <i class="bi bi-person-check me-1"></i> Daftar Sekarang
      </button>

      <div class="d-flex align-items-center gap-2 mb-3">
        <hr class="flex-grow-1 m-0">
        <span class="text-muted small">sudah punya akun?</span>
        <hr class="flex-grow-1 m-0">
      </div>

      <a href="/perpustakaan/login_anggota.php" class="btn btn-outline-primary w-100">
        <i class="bi bi-box-arrow-in-right me-1"></i> Login sebagai Anggota
      </a>
    </form>

    <?php endif; ?>

    <p class="text-center text-muted small mt-4 mb-0">
      Library Space &copy; <?= date('Y') ?>
    </p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePass(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(iconId);
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