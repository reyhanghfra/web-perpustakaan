<?php
/**
 * logout.php — Menghapus session dan redirect ke login
 */
session_start();
session_unset();
session_destroy();

header('Location: /perpustakaan/login.php');
exit;
