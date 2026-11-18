<html>
<head><title>Contoh Penggunaan UDF</title></head>
<body>
<!-- Menentukan Form Input -->
<form method="POST">
Masukkan Bilangan Pertama  :  <br>
<input type="text" name="A" size=10> <br>
Masukkan Bilangan Kedua :  <br>
<input type="text" name="B" size=10> <br>
<input type="submit" value="hitung">
</form>

<!-- membandingkan 2 buah bilangan yang diinput -->
<?php
  // Perbaikan: $_POST bersifat case-sensitive, harus huruf besar semua
  // Menggunakan ?? '' untuk mencegah error jika form belum disubmit
  $A = $_POST["A"] ?? "";
  $B = $_POST["B"] ?? "";

  // Hanya jalankan perhitungan jika form sudah disubmit (variabel tidak kosong)
  if ($A !== "" && $B !== "") {
      
      function jumlah($A,$B) {
          $jumlahbil = $A + $B;
          return $jumlahbil;
      }
      
      function kurang($A,$B) {
          // Perbaikan: Operator minus (-) sempat terpotong di baris bawah
          $kurangbil = $A - $B;
          return $kurangbil;
      }
      
      function kali($A,$B) {
          // Perbaikan: Operator kali (*) sempat terpotong di baris bawah
          $kalibil = $A * $B;
          return $kalibil;
      }
      
      function bagi($A,$B) {
          $bagibil = $A / $B;
          return $bagibil;
      }

      echo "<br>";
      echo ("Bilangan Pertama : ");
      echo $A;
      echo "<br>";
      echo ("Bilangan Kedua : ");
      echo $B;
      echo "<br> <br>";
      
      echo "Hasil Penjumlahan 2 buah bilangan ";
      echo "<br>";
      // Perbaikan: Di PHP tidak perlu menggunakan tanda ampersand (&) saat memanggil fungsi
      $jumlahbil = jumlah($A,$B);
      printf( "Penjumlahan antara :  %d  +  %d  =  %d <br>",$A,$B,$jumlahbil);
      echo "<br>";
      
      echo "Hasil Pengurangan 2 buah bilangan ";
      echo "<br>";
      $kurangbil = kurang($A,$B);
      printf( "Pengurangan antara :  %d  -  %d  =  %d ",$A,$B,$kurangbil);
      echo "<br><br>";
      
      echo "Hasil Perkalian 2 buah bilangan ";
      echo "<br>";
      $kalibil = kali($A,$B);
      printf( "Perkalian antara :  %d  *  %d  =  %d ", $A, $B, $kalibil);
      echo "<br><br>";
      
      echo "Hasil Pembagian 2 buah bilangan ";
      echo "<br>";
      $bagibil = bagi($A,$B);
      printf( "Pembagian antara :  %d  / %d  =  %d ",$A,$B,$bagibil);
      echo "<br><br>";
  }
?>
</body>
</html>