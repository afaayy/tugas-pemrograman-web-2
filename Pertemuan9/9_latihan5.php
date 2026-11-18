<!DOCTYPE html>
<html>
<head>
    <title>Getdate</title>
</head>
<body>
    <center>
        <h1>

        <?php
        $sekarang = getdate();
        $bulan = $sekarang['month'];
        $hari = $sekarang['mday'];
        $tahun = $sekarang['year'];
        $jam = $sekarang['hours'];

        if ($jam <= 11) {
            echo "Selamat Pagi";
        } elseif ($jam > 11 && $jam <= 15) {
            echo "Selamat Siang";
        } elseif ($jam > 15 && $jam <= 18) {
            echo "Selamat Sore";
        } elseif ($jam > 18) {
            echo "Selamat Malam";
        }
        ?>

        </h1>

        <h2>Selamat datang</h2>

        <h3>
            Sekarang adalah tanggal
            <?php echo "$hari $bulan $tahun"; ?>
        </h3>

    </center>
</body>
</html>