<?php
/**
 * logout_anggota.php — Logout khusus anggota
 */
session_start();
session_unset();
session_destroy();
header('Location: /perpustakaan/login_anggota.php');
exit;