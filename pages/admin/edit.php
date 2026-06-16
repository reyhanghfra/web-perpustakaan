<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth_guard.php';

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
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff - Perpustakaan Mini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 500px;
            margin: 30px auto;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-submit:hover {
            color: white;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <?php include '../../includes/navbar.php'; ?>

        <div class="container form-container">
            <a href="index.php" style="text-decoration: none; color: #667eea;">← Kembali</a>

            <h2 class="mt-3 mb-4">Edit Staff</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($staff): ?>
                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST">
                            <input type="hidden" name="id_user" value="<?php echo $staff['id_user']; ?>">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    required value="<?php echo htmlspecialchars($staff['username']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama"
                                    required value="<?php echo htmlspecialchars($staff['nama']); ?>">
                            </div>



                            <hr>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password Baru (opsional)</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Kosongkan jika tidak ingin mengubah">
                                <small class="text-muted">Minimum 6 karakter</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-submit">Simpan</button>
                                <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">Data staff tidak ditemukan</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>