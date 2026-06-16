/**
 * main.js — JavaScript utama Perpustakaan Mini
 */

// Konfirmasi hapus data
function konfirmasiHapus(url, nama) {
  if (confirm('Yakin ingin menghapus data "' + nama + '"?\nTindakan ini tidak bisa dibatalkan.')) {
    window.location.href = url;
  }
}

// Auto-close alert setelah 4 detik
document.addEventListener('DOMContentLoaded', function () {
  const alerts = document.querySelectorAll('.alert-auto-close');
  alerts.forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 500);
    }, 4000);
  });
});
