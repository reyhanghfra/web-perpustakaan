<?php
session_start();
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin/Staff - Perpustakaan Mini</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-title {
            margin: 30px 0 20px;
        }
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-add:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .table thead {
            background-color: #f8f9fa;
        }
        .badge-admin {
            background-color: #dc3545;
        }
        .badge-petugas {
            background-color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <?php include '../../includes/navbar.php'; ?>
        
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show mt-3" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="page-title">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">Data Admin</h2>
                    <a href="tambah.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Admin
                    </a>
                </div>
                    <div class="mb-3">
                    <form method="GET" action="">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari nama atau username..." value="<?= htmlspecialchars($search ?? '') ?>">
                        <button class="btn btn-warning text-white" type="submit">Cari</button>
                    </div>
                    </form>
                </div>

            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($staff_list) > 0): ?>
                                    <?php foreach ($staff_list as $staff): ?>
                                        <tr>
                                            <td><?= (int)$staff['id_user']; ?></td>
                                            <td><?= htmlspecialchars($staff['nama']); ?></td>
                                            <td><code><?= htmlspecialchars($staff['username']); ?></code></td>
                                            <td><small><?= htmlspecialchars($staff['created_at']); ?></small></td>
                                            <td>
                                                <a href="edit.php?id=<?= (int)$staff['id_user']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="hapus.php?id=<?= (int)$staff['id_user']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Belum ada data admin</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
