-- DATABASE PENDAFTARAN CALON MAHASISWA
-- Impor file ini melalui phpMyAdmin > Import.
-- CREATE TABLE IF NOT EXISTS tidak menghapus data pada tabel yang sudah ada.

CREATE DATABASE IF NOT EXISTS `calon_mhs`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `calon_mhs`;

CREATE TABLE IF NOT EXISTS `calon_mhs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kode` VARCHAR(25) NOT NULL,
    `nama` VARCHAR(150) NOT NULL,
    `jenis_kelamin` ENUM('Laki-laki', 'Perempuan') NOT NULL,
    `ttl` VARCHAR(150) NOT NULL,
    `asal_sekolah` VARCHAR(150) NOT NULL,
    `pekerjaan_ortu` VARCHAR(100) NOT NULL,
    `matematika` DECIMAL(5,2) NOT NULL,
    `inggris` DECIMAL(5,2) NOT NULL,
    `umum` DECIMAL(5,2) NOT NULL,
    `rata_rata` DECIMAL(5,2) NOT NULL,
    `keterangan` ENUM('Lulus', 'Cadangan', 'Tidak Lulus') NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Untuk mengecek hasil setelah mengisi dan klik SUBMIT di website:
SELECT * FROM `calon_mhs` ORDER BY `id` ASC;
