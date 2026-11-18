<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

// Input data pertama
mysqli_query($con, "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('Karina', 'Suwandi', '29')");

// Input data kedua
mysqli_query($con, "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('Glenn', 'Gandari', '32')");

echo "Data berhasil disisipkan!";

mysqli_close($con);
?>