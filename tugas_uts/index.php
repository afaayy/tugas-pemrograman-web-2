<?php

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'calon_mhs';

function aman($nilai): string
{
    return htmlspecialchars((string) $nilai, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function nilaiTampil($nilai): string
{
    return number_format((float) $nilai, 2, '.', '');
}

$koneksi = null;
$kesalahan = '';
$notifikasi = $_SESSION['pesan_sukses'] ?? '';
unset($_SESSION['pesan_sukses']);
$dataPendaftar = [];
$nomorBerikutnya = 101;
$statistik = [
    'semua' => 0,
    'Lulus' => 0,
    'Cadangan' => 0,
    'Tidak Lulus' => 0,
];

try {
    $koneksi = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    $kesalahan = 'Gagal terhubung ke database. Jalankan Apache dan MySQL di XAMPP, lalu impor file calon_mhs.sql. Detail: ' . $e->getMessage();
}

if ($koneksi !== null) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $gedung = strtoupper(trim((string) ($_POST['gedung'] ?? '')));
        $gelombang = (int) ($_POST['gelombang'] ?? 0);
        $bulan = (int) ($_POST['bulan'] ?? 0);
        $nama = trim((string) ($_POST['nama'] ?? ''));
        $jenisKelamin = (string) ($_POST['jenis_kelamin'] ?? '');
        $ttl = trim((string) ($_POST['ttl'] ?? ''));
        $asalSekolah = trim((string) ($_POST['asal_sekolah'] ?? ''));
        $pekerjaanOrtu = trim((string) ($_POST['pekerjaan_ortu'] ?? ''));
        $matematika = filter_var($_POST['matematika'] ?? null, FILTER_VALIDATE_FLOAT);
        $inggris = filter_var($_POST['inggris'] ?? null, FILTER_VALIDATE_FLOAT);
        $umum = filter_var($_POST['umum'] ?? null, FILTER_VALIDATE_FLOAT);

        if (
            !in_array($gedung, ['A', 'B', 'V'], true)
            || $gelombang < 1 || $gelombang > 5
            || $bulan < 1 || $bulan > 12
            || $nama === '' || $ttl === '' || $asalSekolah === '' || $pekerjaanOrtu === ''
            || !in_array($jenisKelamin, ['Laki-laki', 'Perempuan'], true)
            || $matematika === false || $inggris === false || $umum === false
            || $matematika < 0 || $matematika > 100
            || $inggris < 0 || $inggris > 100
            || $umum < 0 || $umum > 100
        ) {
            $kesalahan = 'Lengkapi semua kolom dengan benar. Nilai tes harus antara 0 sampai 100.';
        } else {
            $rataRata = round(($matematika + $inggris + $umum) / 3, 2);
            $keterangan = $rataRata >= 70 ? 'Lulus' : ($rataRata >= 60 ? 'Cadangan' : 'Tidak Lulus');

            try {
                $hasilNomor = $koneksi->query('SELECT COALESCE(MAX(id), 0) + 101 AS nomor FROM calon_mhs');
                $nomor = (int) $hasilNomor->fetch_assoc()['nomor'];
                $kode = $gedung . $gelombang . '-' . $nomor . '-' . $bulan;

                $perintah = $koneksi->prepare(
                    'INSERT INTO calon_mhs
                    (kode, nama, jenis_kelamin, ttl, asal_sekolah, pekerjaan_ortu,
                     matematika, inggris, umum, rata_rata, keterangan)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $perintah->bind_param(
                    'ssssssdddds',
                    $kode, $nama, $jenisKelamin, $ttl, $asalSekolah, $pekerjaanOrtu,
                    $matematika, $inggris, $umum, $rataRata, $keterangan
                );
                $perintah->execute();
                $perintah->close();

                $_SESSION['pesan_sukses'] = 'Data pendaftar berhasil disimpan ke database! Kode: ' . $kode;
                header('Location: index.php');
                exit;
            } catch (mysqli_sql_exception $e) {
                $kesalahan = 'Gagal menyimpan data ke MySQL: ' . $e->getMessage();
            }
        }
    }

    try {
        $hasilNomor = $koneksi->query('SELECT COALESCE(MAX(id), 0) + 101 AS nomor FROM calon_mhs');
        $nomorBerikutnya = (int) $hasilNomor->fetch_assoc()['nomor'];

        $hasilData = $koneksi->query(
            'SELECT id, kode, nama, jenis_kelamin, ttl, asal_sekolah, pekerjaan_ortu,
                    matematika, inggris, umum, rata_rata, keterangan
             FROM calon_mhs ORDER BY id ASC'
        );
        while ($baris = $hasilData->fetch_assoc()) {
            $dataPendaftar[] = $baris;
            $statistik['semua']++;
            if (isset($statistik[$baris['keterangan']])) {
                $statistik[$baris['keterangan']]++;
            }
        }
    } catch (mysqli_sql_exception $e) {
        $kesalahan = 'Tabel calon_mhs tidak sesuai atau belum dibuat. Impor file calon_mhs.sql. Detail: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pendaftaran Calon Mahasiswa</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #fff; color: #111; font: 13px Arial, Helvetica, sans-serif; }
        .wadah { max-width: 1100px; margin: 24px auto; padding: 0 14px; }
        h2 { font-size: 18px; margin: 0 0 13px; }
        h3 { font-size: 14px; margin: 14px 0 5px; }
        .jumlah { font-weight: bold; margin: 0 0 10px; }
        .jumlah span { font-weight: normal; margin-left: 10px; }
        .formulir { width: min(100%, 500px); }
        .baris { display: grid; grid-template-columns: 143px 1fr; align-items: center; gap: 4px; margin-bottom: 5px; }
        .baris label.judul { font-weight: bold; }
        input[type="text"], input[type="number"], select { font-family: Arial, sans-serif; font-size: 13px; height: 25px; border: 1px solid #aaa; padding: 3px 6px; background: white; border-radius: 0; }
        .baris > input[type="text"], .baris > input[type="number"] { width: 100%; }
        .pilihan-kode { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
        .pilihan-kode select { width: 88px; }
        .pilihan-kode #serial { white-space: nowrap; }
        .gender { display: flex; gap: 12px; align-items: center; }
        .gender label { display: inline-flex; gap: 4px; align-items: center; white-space: nowrap; }
        .aksi { margin: 8px 0 13px 147px; display: flex; gap: 5px; }
        .aksi button { cursor: pointer; padding: 3px 12px; background: #eee; border: 1px solid #888; border-radius: 0; font-size: 13px; }
        .aksi button:hover { background: #ddd; }
        .pesan { padding: 10px; margin: 12px 0; }
        .sukses { background: #d6efda; color: #115c23; }
        .gagal { background: #ffe5e5; color: #8b0000; }
        .geser-tabel { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #999; padding: 4px 6px; }
        th { text-align: center; white-space: nowrap; }
        td.angka, td.status { text-align: center; }
        td.kanan { text-align: right; font-weight: bold; }
        .keterangan { display: flex; gap: 20px; flex-wrap: wrap; margin-top: 12px; }
        .kotak { border: 1px solid #999; padding: 10px; max-width: 450px; min-width: 280px; }
        .kotak:first-child { max-width: 310px; }
        .kotak strong { display: block; margin-bottom: 6px; }
        .kotak ul { margin: 0; padding-left: 22px; line-height: 1.5; }
        .kode-contoh { font-size: 12px; color: #444; margin: 7px 0 0 147px; }
        @media (max-width: 600px) {
            .wadah { margin: 18px auto; }
            .baris { grid-template-columns: 1fr; gap: 2px; }
            .aksi, .kode-contoh { margin-left: 0; }
            .pilihan-kode select { width: 72px; }
            .gender { gap: 7px; }
            .kotak { min-width: 0; width: 100%; }
        }
    </style>
</head>
<body>
<div class="wadah">
    <h2>INPUT PENDAFTARAN</h2>
    <p class="jumlah">JUMLAH DATA <span>: [ <?= $statistik['semua'] ?> ]</span></p>

    <form class="formulir" method="post" action="index.php">
        <div class="baris">
            <label class="judul" for="gedung">KODE PENDAFTARAN</label>
            <div class="pilihan-kode">
                <select id="gedung" name="gedung" aria-label="Gedung tes" required>
                    <?php foreach (['A', 'B', 'V'] as $g): ?>
                        <option value="<?= $g ?>" <?= ($_POST['gedung'] ?? 'A') === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="gelombang" name="gelombang" aria-label="Gelombang" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= (int) ($_POST['gelombang'] ?? 1) === $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <span id="serial">-<?= $nomorBerikutnya ?>-</span>
                <select id="bulan" name="bulan" aria-label="Bulan tes" required>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i ?>" <?= (int) ($_POST['bulan'] ?? 1) === $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="baris">
            <label class="judul" for="nama">NAMA PENDAFTAR</label>
            <input id="nama" name="nama" type="text" maxlength="150" value="<?= aman($_POST['nama'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <span class="judul"><strong>JENIS KELAMIN</strong></span>
            <div class="gender">
                <label><input type="radio" name="jenis_kelamin" value="Laki-laki" <?= ($_POST['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'checked' : '' ?> required> Laki-laki</label>
                <label><input type="radio" name="jenis_kelamin" value="Perempuan" <?= ($_POST['jenis_kelamin'] ?? '') === 'Perempuan' ? 'checked' : '' ?> required> Perempuan</label>
            </div>
        </div>
        <div class="baris">
            <label class="judul" for="ttl">TTL</label>
            <input id="ttl" name="ttl" type="text" maxlength="150" placeholder="Contoh: Depok, 10 Januari 2008" value="<?= aman($_POST['ttl'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <label class="judul" for="asal_sekolah">ASAL SEKOLAH</label>
            <input id="asal_sekolah" name="asal_sekolah" type="text" maxlength="150" value="<?= aman($_POST['asal_sekolah'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <label class="judul" for="pekerjaan_ortu">PEKERJAAN ORTU</label>
            <input id="pekerjaan_ortu" name="pekerjaan_ortu" type="text" maxlength="100" value="<?= aman($_POST['pekerjaan_ortu'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <label class="judul" for="matematika">NILAI TES MATEMATIKA</label>
            <input id="matematika" name="matematika" type="number" step="0.01" min="0" max="100" value="<?= aman($_POST['matematika'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <label class="judul" for="inggris">BAHASA INGGRIS</label>
            <input id="inggris" name="inggris" type="number" step="0.01" min="0" max="100" value="<?= aman($_POST['inggris'] ?? '') ?>" required>
        </div>
        <div class="baris">
            <label class="judul" for="umum">UMUM</label>
            <input id="umum" name="umum" type="number" step="0.01" min="0" max="100" value="<?= aman($_POST['umum'] ?? '') ?>" required>
        </div>
        <div class="aksi">
            <button type="submit" <?= $koneksi === null ? 'disabled' : '' ?>>SUBMIT</button>
            <button type="reset">BATAL</button>
        </div>
    </form>

    <?php if ($notifikasi !== ''): ?>
        <div class="pesan sukses" role="status"><?= aman($notifikasi) ?></div>
    <?php endif; ?>
    <?php if ($kesalahan !== ''): ?>
        <div class="pesan gagal" role="alert"><?= aman($kesalahan) ?></div>
    <?php endif; ?>

    <h3>TABEL DATA</h3>
    <div class="geser-tabel">
        <table>
            <thead>
                <tr>
                    <th>No.</th><th>Kode</th><th>Kode Pendf.</th><th>Nama Pendft.</th>
                    <th>Jns Kel</th><th>TTL</th><th>Asal Sekolah</th><th>Pek. Ortu</th>
                    <th>Mat</th><th>B. Ingr</th><th>Umum</th><th>Rata-Rata</th><th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$dataPendaftar): ?>
                    <tr><td colspan="13" style="text-align:center;">Belum ada pendaftar di database.</td></tr>
                <?php else: ?>
                    <?php foreach ($dataPendaftar as $urutan => $baris): ?>
                        <tr>
                            <td class="angka"><?= $urutan + 1 ?></td>
                            <td class="angka"><?= aman(explode('-', $baris['kode'])[0]) ?></td>
                            <td class="angka"><?= aman($baris['kode']) ?></td>
                            <td><?= aman($baris['nama']) ?></td>
                            <td class="angka"><?= aman($baris['jenis_kelamin']) ?></td>
                            <td><?= aman($baris['ttl']) ?></td>
                            <td><?= aman($baris['asal_sekolah']) ?></td>
                            <td><?= aman($baris['pekerjaan_ortu']) ?></td>
                            <td class="angka"><?= nilaiTampil($baris['matematika']) ?></td>
                            <td class="angka"><?= nilaiTampil($baris['inggris']) ?></td>
                            <td class="angka"><?= nilaiTampil($baris['umum']) ?></td>
                            <td class="angka"><?= nilaiTampil($baris['rata_rata']) ?></td>
                            <td class="status"><?= aman($baris['keterangan']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr><td colspan="12" class="kanan">JUMLAH PENDAFTAR</td><td class="angka"><?= $statistik['semua'] ?></td></tr>
                <tr><td colspan="12" class="kanan">JUMLAH PESERTA LULUS</td><td class="angka"><?= $statistik['Lulus'] ?></td></tr>
                <tr><td colspan="12" class="kanan">JUMLAH PESERTA CADANGAN</td><td class="angka"><?= $statistik['Cadangan'] ?></td></tr>
                <tr><td colspan="12" class="kanan">JUMLAH PESERTA TIDAK LULUS</td><td class="angka"><?= $statistik['Tidak Lulus'] ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="keterangan">
        <div class="kotak">
            <strong>Kode pendaftaran:</strong>
            <ul>
                <li>Dua karakter awal: tempat tes dan gelombang.<br>A = Gedung A<br>B = Gedung B<br>V = Viktor</li>
                <li>Angka setelah tanda hubung pertama: nomor urut pendaftaran.</li>
                <li>Angka terakhir: bulan tes.</li>
                <li>Contoh: <b>A1-101-1</b></li>
            </ul>
        </div>
        <div class="kotak">
            <ul>
                <li>RATA-RATA = (MAT + B. INGGRIS + UMUM) / 3.</li>
                <li>Jika rata-rata minimal 70, keterangan <b>Lulus</b>.</li>
                <li>Jika rata-rata minimal 60 dan kurang dari 70, keterangan <b>Cadangan</b>.</li>
                <li>Jika rata-rata kurang dari 60, keterangan <b>Tidak Lulus</b>.</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
