<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

$hasil = mysqli_query($con, "SELECT * FROM tbl_mhs");
$hit = mysqli_num_rows($hasil);

echo "jumlah record $hit";

mysqli_close($con);
?>
```[cite: 16]