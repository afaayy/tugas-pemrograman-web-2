<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

$hasil = mysqli_query($con, "SELECT * FROM tbl_mhs");

while ($data = mysqli_fetch_array($hasil)) {
    echo "$data[FirstName] $data[LastName] $data[Age]<br>";
}

mysqli_close($con);
?>