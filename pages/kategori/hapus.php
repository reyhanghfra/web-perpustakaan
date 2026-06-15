<?php

require_once __DIR__ . '/../../config/koneksi.php';

$id = $_GET['id'];

mysqli_query(
    $koneksi,
    "DELETE FROM kategori
     WHERE id_kategori='$id'"
);

header("Location:index.php");
exit;