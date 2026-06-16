<?php
/**
 * login.php — Halaman Login
 */
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
  header('Location: /web-perpustakaan/pages/dashboard/');
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
    // Kolom role tidak ada di database.sql (tabel users). Jadi cukup ambil field yang tersedia.
    $sql = "SELECT id_user, username, password, nama FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
      $user = mysqli_fetch_assoc($result);


      if (password_verify($password, $user['password'])) {
        $_SESSION['login']    = true;
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama']     = $user['nama'];
        // role kolom tidak ada pada tabel users di database.sql
        // $_SESSION['role'] tetap tidak diset.


        header('Location: /web-perpustakaan/pages/dashboard/');
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
  <link rel="stylesheet" href="/web-perpustakaan/assets/css/style.css">
</head>

<body class="login-page">

  <div class="login-card">
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
  <script src="/web-perpustakaan/assets/js/main.js"></script>
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
    <!-- ===== MODAL DAFTAR ANGGOTA ===== -->
  <div class="modal fade" id="modalDaftar" tabindex="-1" aria-labelledby="modalDaftarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow">

        <div class="modal-header border-0 pb-0">
          <div class="text-center w-100">
            <div class="login-logo mx-auto mb-2" style="width:56px;height:56px;font-size:1.5rem;">
              <i class="bi bi-person-plus"></i>
            </div>
            <h5 class="modal-title fw-bold" id="modalDaftarLabel">Daftar Anggota</h5>
            <p class="text-muted small mb-0">Buat akun untuk mengakses layanan perpustakaan</p>
          </div>
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                  data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>

        <div class="modal-body px-4 pt-3">

          <!-- Alert error daftar -->
          <div id="alertDaftarError" class="alert alert-danger d-none d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <span id="pesanErrorDaftar"></span>
          </div>

          <!-- Alert sukses daftar -->
          <div id="alertDaftarSuccess" class="alert alert-success d-none d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill flex-shrink-0"></i>
            <span id="pesanSuccessDaftar"></span>
          </div>

          <form id="formDaftar">
            <!-- Nama Lengkap -->
            <div class="mb-3">
              <label for="daftar_nama" class="form-label">Nama Lengkap</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" id="daftar_nama" name="nama" class="form-control"
                       placeholder="Masukkan nama lengkap" required>
              </div>
              <div class="form-text">Nama ini digunakan sebagai username saat login.</div>
            </div>

            <!-- No HP -->
            <div class="mb-3">
              <label for="daftar_no_hp" class="form-label">Nomor HP / WhatsApp</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" id="daftar_no_hp" name="no_hp" class="form-control"
                       placeholder="Contoh: 081234567890" maxlength="15" required>
              </div>
              <div class="form-text">Digunakan untuk notifikasi booking via WhatsApp.</div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="daftar_password" class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="daftar_password" name="password" class="form-control"
                       placeholder="Minimal 6 karakter" required>
                <button class="btn btn-outline-secondary" type="button"
                        onclick="togglePassModal('daftar_password','eye_modal1')">
                  <i class="bi bi-eye" id="eye_modal1"></i>
                </button>
              </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
              <label for="daftar_konfirm" class="form-label">Konfirmasi Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" id="daftar_konfirm" name="konfirmasi_password"
                       class="form-control" placeholder="Ulangi password" required>
                <button class="btn btn-outline-secondary" type="button"
                        onclick="togglePassModal('daftar_konfirm','eye_modal2')">
                  <i class="bi bi-eye" id="eye_modal2"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn btn-success w-100" id="btnSubmitDaftar">
              <i class="bi bi-person-check me-1"></i> Daftar Sekarang
            </button>
          </form>

        </div>
      </div>
    </div>
  </div>
  <!-- ===== END MODAL ===== -->

  <script>
    // Toggle show/hide password di modal
    function togglePassModal(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon  = document.getElementById(iconId);
      input.type  = input.type === 'password' ? 'text' : 'password';
      icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }

    // Submit form daftar via AJAX
    document.getElementById('formDaftar').addEventListener('submit', function(e) {
      e.preventDefault();

      const btn        = document.getElementById('btnSubmitDaftar');
      const alertError = document.getElementById('alertDaftarError');
      const alertOk    = document.getElementById('alertDaftarSuccess');
      const pesanError = document.getElementById('pesanErrorDaftar');
      const pesanOk    = document.getElementById('pesanSuccessDaftar');

      // Reset alert
      alertError.classList.add('d-none');
      alertOk.classList.add('d-none');

      // Loading state
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

      const formData = new FormData(this);

      fetch('/perpustakaan/proses_daftar.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-person-check me-1"></i> Daftar Sekarang';

        if (data.status === 'success') {
          // Tampilkan sukses, reset form
          alertOk.classList.remove('d-none');
          pesanOk.textContent = data.pesan;
          document.getElementById('formDaftar').reset();
        } else {
          // Tampilkan error
          alertError.classList.remove('d-none');
          pesanError.textContent = data.pesan;
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-person-check me-1"></i> Daftar Sekarang';
        alertError.classList.remove('d-none');
        pesanError.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
      });
    });
  </script>
</body>

</html>