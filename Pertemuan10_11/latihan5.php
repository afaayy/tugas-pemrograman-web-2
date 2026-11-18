<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

$hasil = mysqli_query($con, "SELECT * FROM tbl_mhs");

while ($data = mysqli_fetch_row($hasil)) {
    echo "$data[0] $data[1] $data[2] $data[3]<br>";
}

mysqli_close($con);
?>