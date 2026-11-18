<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname     = "pemograman_web2";

// 1. Koneksi ke server dan langsung pilih database
$link = mysqli_connect($servername, $dbusername, $dbpassword, $dbname);

if (!$link) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// 2. Perintah SQL untuk membuat tabel tbl_mhs
$sql = "CREATE TABLE IF NOT EXISTS tbl_mhs (
    mhsID int NOT NULL AUTO_INCREMENT,
    PRIMARY KEY(mhsID),
    FirstName varchar(15),
    LastName varchar(15),
    Age int
)";

$buat_tabel = mysqli_query($link, $sql);

if ($buat_tabel) {
    echo "Tabel <b>tbl_mhs</b> berhasil dibuat!<br>";
} else {
    die("Gagal membuat tabel: " . mysqli_error($link));
}

// 3. Perintah SQL untuk input data (insert data)
$sql_insert = "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('Anjar', 'Prabowo', 25)";
$input = mysqli_query($link, $sql_insert);

if ($input) {
    echo "Data berhasil di-input ke dalam tabel!";
} else {
    echo "Gagal input data: " . mysqli_error($link);
}
?>