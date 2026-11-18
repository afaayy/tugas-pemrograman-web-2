<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname     = "pemograman_web2"; // Diubah menjadi pemograman_web2

// 1. Koneksi ke server MySQL menggunakan mysqli_connect
$link = mysqli_connect($servername, $dbusername, $dbpassword);

if (!$link) {
    die("Koneksi ke server gagal: " . mysqli_connect_error());
}

// 2. Membuat database melalui query SQL
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$cek = mysqli_query($link, $sql) or die("Couldn't Create Database: " . mysqli_error($link));

// 3. Cek apakah berhasil
if ($cek) {
    echo "Database <b>$dbname</b> berhasil dibuat";
}
?>