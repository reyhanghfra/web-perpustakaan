<?php
session_start();
require_once '../../config/koneksi.php';
require_once '../../includes/auth_guard.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: /web-perpustakaan/login.php');
    exit;
}
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

            <div class="d-flex justify-content-between align-items-center page-title">
                <h2>Admin/Staff</h2>
                <a href="tambah.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Staff
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (count($staff_list) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Username</th>
                                        <th width="30%">Nama</th>
                                        <th width="15%">Role</th>
                                        <th width="15%">Terdaftar</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    foreach ($staff_list as $staff): 
                                    ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><code><?php echo htmlspecialchars($staff['username']); ?></code></td>
                                            <td><?php echo htmlspecialchars($staff['nama']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($staff['role']); ?>">
                                                    <?php echo ucfirst($staff['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?php echo date('d M Y', strtotime($staff['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $staff['id_user']; ?>" 
                                                   class="btn btn-sm btn-warning">Edit</a>
                                                <a href="hapus.php?id=<?php echo $staff['id_user']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-5">Belum ada data staff</p>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted">
                    <small>Total: <?php echo count($staff_list); ?> staff</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
