<?php
$con = mysqli_connect("localhost", "root", "", "pemograman_web2");

if (!$con) {
    die('Could not connect: ' . mysqli_connect_error());
}

$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$age = isset($_POST['age']) ? $_POST['age'] : '';

if (!empty($firstname)) {
    $sql = "INSERT INTO tbl_mhs (FirstName, LastName, Age) VALUES ('$firstname', '$lastname', '$age')";

    if (!mysqli_query($con, $sql)) {
        die('Error: ' . mysqli_error($con));
    }

    echo "1 record added";
} else {
    echo "Silakan isi form terlebih dahulu!";
}

mysqli_close($con);
?>