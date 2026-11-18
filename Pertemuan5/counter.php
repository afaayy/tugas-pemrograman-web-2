<html>
<head>
<title>Contoh Counter</title>
</head>
<body>
<?php
$nama_file = "counter.dat";

if (file_exists($nama_file)) {
    $berkas = fopen($nama_file, "r");
    $pencacah = (integer)trim(fgets($berkas, 255));
    $pencacah++;
    fclose($berkas);
} else {
    $pencacah = 1;
}

// simpan pencacah
$berkas = fopen($nama_file, "w");
fputs($berkas, $pencacah); // Diubah dari Spencacah menjadi $pencacah
fclose($berkas);

// tulis ke halaman web
print("Anda pengunjung ke-" . $pencacah . "<br>\n"); 
// Diubah agar variabel $pencacah terbaca dengan benar di dalam teks
?>
</body>
</html>