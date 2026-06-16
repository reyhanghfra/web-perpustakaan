<?php
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

$page_title = 'Data Kategori';
require_once __DIR__ . '/../../includes/header.php';

$query = mysqli_query($koneksi, "
SELECT k.*,
COUNT(b.id_buku) as jumlah_buku
FROM kategori k
LEFT JOIN buku b
ON k.id_kategori = b.id_kategori
GROUP BY k.id_kategori
ORDER BY k.id_kategori DESC
");
?>

<div class="d-flex">

    <?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

    <div class="main-wrapper flex-grow-1">

        <?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

        <div class="pt-2 px-4">

            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>
                        <i class="bi bi-tags-fill me-2 text-primary"></i>
                        Data Kategori
                    </h1>
                    <p>Kelola semua kategori buku perpustakaan di sini.</p>
                </div>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="tambah.php" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Kategori
                    </a>
                <?php endif; ?>

            </div>

            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon blue">
                            <i class="bi bi-tags"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Kategori</div>
                            <div class="stat-value">
                                <?=
                                mysqli_fetch_row(
                                    mysqli_query($koneksi, "SELECT COUNT(*) FROM kategori")
                                )[0];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="bi bi-journal-bookmark"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Buku</div>
                            <div class="stat-value">
                                <?=
                                mysqli_fetch_row(
                                    mysqli_query($koneksi, "SELECT COUNT(*) FROM buku")
                                )[0];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon orange">
                            <i class="bi bi-collection"></i>
                        </div>
                        <div>
                            <div class="stat-label">Kategori Aktif</div>
                            <div class="stat-value">
                                <?=
                                mysqli_fetch_row(
                                    mysqli_query($koneksi, "
                                    SELECT COUNT(DISTINCT id_kategori)
                                    FROM buku
                                    ")
                                )[0];
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul text-primary me-2"></i>
                        Daftar Kategori Buku
                    </h5>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th width="70">No</th>
                                    <th>Nama Kategori</th>
                                    <th>Jumlah Buku</th>
                                    <th width="140" class="text-center">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                if (mysqli_num_rows($query) > 0):
                                    $no = 1;
                                    while ($row = mysqli_fetch_assoc($query)):
                                ?>

                                    <tr>
                                        <td class="text-muted"><?= $no++ ?></td>
                                        <td>
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($row['nama_kategori']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= $row['jumlah_buku'] ?> Buku
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                                <a href="edit.php?id=<?= $row['id_kategori'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="hapus.php?id=<?= $row['id_kategori'] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Hapus kategori ini?')"
                                                    title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                <?php
                                    endwhile;
                                else:
                                ?>

                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-tags display-4 text-secondary d-block mb-2"></i>
                                            <span class="text-muted">Belum ada data kategori.</span>
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>