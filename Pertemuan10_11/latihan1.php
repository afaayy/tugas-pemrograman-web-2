<?php
$servername = 'localhost';
$dbusername = 'root';
$dbpassword = '';
$dbname     = 'db_pemrograman_web2'; // Sesuai dengan database yang kamu buat

// Membuat koneksi ke server dan langsung memilih database
$link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

// Cek koneksi
if (!$link) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

echo "ok....koneksi ke database db_pemrograman_web2 berhasil!";
?>