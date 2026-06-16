<?php
/**
 * proses_daftar.php — Endpoint AJAX untuk proses pendaftaran anggota
 */
header('Content-Type: application/json');
require_once __DIR__ . '/config/koneksi.php';

$nama    = trim(mysqli_real_escape_string($koneksi, $_POST['nama']                ?? ''));
$no_hp   = trim(mysqli_real_escape_string($koneksi, $_POST['no_hp']               ?? ''));
$alamat  = trim(mysqli_real_escape_string($koneksi, $_POST['alamat']              ?? ''));
$password= trim($_POST['password']             ?? '');
$konfirm = trim($_POST['konfirmasi_password']  ?? '');

// Validasi kosong
if ($nama === '' || $no_hp === '' || $alamat === '' || $password === '') {
    echo json_encode(['status' => 'error', 'pesan' => 'Semua kolom wajib diisi.']);
    exit;
}

// Validasi format no HP
if (!preg_match('/^[0-9]{10,15}$/', $no_hp)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nomor HP tidak valid. Gunakan format: 081234567890 (10–15 digit angka).']);
    exit;
}

// Validasi panjang password
if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'pesan' => 'Password minimal 6 karakter.']);
    exit;
}

// Validasi konfirmasi password
if ($password !== $konfirm) {
    echo json_encode(['status' => 'error', 'pesan' => 'Konfirmasi password tidak cocok.']);
    exit;
}

// Cek no HP sudah terdaftar
$cek_hp = mysqli_query($koneksi, "SELECT id_anggota FROM anggota WHERE no_hp = '$no_hp' LIMIT 1");
if (mysqli_num_rows($cek_hp) > 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nomor HP tersebut sudah terdaftar.']);
    exit;
}

// Cek nama sudah dipakai sebagai username
$cek_user = mysqli_query($koneksi, "SELECT id_users_anggota FROM users_anggota WHERE username = '$nama' LIMIT 1");
if (mysqli_num_rows($cek_user) > 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nama tersebut sudah digunakan. Gunakan nama yang berbeda.']);
    exit;
}

// Simpan ke tabel anggota
$sql_anggota = "INSERT INTO anggota (nama, alamat, no_hp) VALUES ('$nama', '$alamat', '$no_hp')";
if (!mysqli_query($koneksi, $sql_anggota)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal menyimpan data anggota.']);
    exit;
}

$id_anggota_baru = mysqli_insert_id($koneksi);
$hash_password   = password_hash($password, PASSWORD_DEFAULT);

// Simpan ke tabel users_anggota
$sql_user = "INSERT INTO users_anggota (id_anggota, username, password)
             VALUES ($id_anggota_baru, '$nama', '$hash_password')";
if (!mysqli_query($koneksi, $sql_user)) {
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota = $id_anggota_baru");
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal membuat akun login.']);
    exit;
}

echo json_encode([
    'status' => 'success',
    'pesan'  => 'Pendaftaran berhasil! Silakan login dengan nama lengkap dan password kamu.'
]);