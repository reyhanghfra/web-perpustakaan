<?php
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';

if(isset($_POST['simpan']))
{
    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_kategori']
    );

    mysqli_query(
        $koneksi,
        "INSERT INTO kategori(nama_kategori)
         VALUES('$nama')"
    );

    header("Location:index.php");
    exit;
}

$page_title = "Tambah Kategori";
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">

<?php require_once __DIR__ . '/../../includes/sidebar.php'; ?>

<div class="main-wrapper flex-grow-1">

<?php require_once __DIR__ . '/../../includes/navbar.php'; ?>

<div class="pt-2 px-4">

<div class="page-header mb-4">
    <h1>
        <i class="bi bi-plus-circle-fill text-primary me-2"></i>
        Tambah Kategori
    </h1>
    <p>Tambahkan kategori buku baru.</p>
</div>

<div class="row justify-content-center">

<div class="col-lg-7">

<div class="card border-0 shadow-sm">

<div class="card-header bg-primary text-white">
    Form Tambah Kategori
</div>

<div class="card-body p-4">

<form method="POST">

<div class="mb-4">
<label class="form-label fw-semibold">
Nama Kategori
</label>

<input
type="text"
name="nama_kategori"
class="form-control form-control-lg"
placeholder="Masukkan nama kategori"
required>
</div>

<div class="d-flex gap-2">

<button
type="submit"
name="simpan"
class="btn btn-primary">

<i class="bi bi-check-circle me-1"></i>
Simpan

</button>

<a
href="index.php"
class="btn btn-light border">

Kembali

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

</div>

</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>