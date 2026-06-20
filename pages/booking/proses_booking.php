<?php
/**
 * pages/booking/proses_booking.php
 * Memproses booking menjadi peminjaman aktif
 */
require_once __DIR__ . '/../../includes/auth_guard.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../../includes/whatsapp.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ./index.php');
    exit;
}

$id_booking = intval($_POST['id_booking'] ?? 0);

if ($id_booking === 0) {
    header('Location: ./index.php?error=invalid');
    exit;
}

// Ambil data booking
$booking = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT b.*, a.nama AS nama_anggota, a.no_hp,
            bk.judul AS judul_buku, bk.stok
     FROM booking b
     JOIN anggota a  ON b.id_anggota = a.id_anggota
     JOIN buku bk    ON b.id_buku    = bk.id_buku
     WHERE b.id_booking = $id_booking
       AND b.status = 'Booking'
     LIMIT 1"));

// Validasi booking ada dan masih aktif
if (!$booking) {
    header('Location: ./index.php?error=notfound');
    exit;
}

// Validasi stok masih tersedia
if ($booking['stok'] < 1) {
    header('Location: ./index.php?error=stok');
    exit;
}

$tgl_pinjam  = date('Y-m-d');
$tgl_kembali = date('Y-m-d', strtotime('+7 days'));
$id_anggota  = $booking['id_anggota'];
$id_buku     = $booking['id_buku'];
$kode        = mysqli_real_escape_string($koneksi, $booking['kode_booking']);

// 1. Insert ke tabel peminjaman
$sql_pinjam = "INSERT INTO peminjaman
                (kode_booking, id_anggota, id_buku, tanggal_pinjam, tanggal_kembali, status)
               VALUES
                ('$kode', $id_anggota, $id_buku, '$tgl_pinjam', '$tgl_kembali', 'Diambil')";

if (!mysqli_query($koneksi, $sql_pinjam)) {
    header('Location: ./index.php?error=pinjam');
    exit;
}

// 2. Update status booking menjadi "Diambil"
mysqli_query($koneksi,
    "UPDATE booking SET status = 'Diambil' WHERE id_booking = $id_booking");

// 3. Kurangi stok buku
mysqli_query($koneksi,
    "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku AND stok > 0");

// 4. Kirim notifikasi WhatsApp ke anggota
$nama_anggota = $booking['nama_anggota'];
$judul_buku   = $booking['judul_buku'];
$tgl_kembali_fmt = date('d-m-Y', strtotime($tgl_kembali));

$pesan  = "📚 *PERPUSTAKAAN MINI*\n\n";
$pesan .= "Halo, *$nama_anggota*! 👋\n\n";
$pesan .= "Buku kamu telah berhasil dipinjam.\n\n";
$pesan .= "📖 Buku          : *$judul_buku*\n";
$pesan .= "🔑 Kode Booking  : *$kode*\n";
$pesan .= "📅 Tgl Pinjam    : " . date('d-m-Y') . "\n";
$pesan .= "⏳ Batas Kembali : *$tgl_kembali_fmt*\n\n";
$pesan .= "Harap kembalikan buku sebelum batas waktu. Terima kasih! 🙏";

kirimWhatsApp($booking['no_hp'], $pesan);

// Redirect sukses
header('Location: ./index.php?success=1');
exit;