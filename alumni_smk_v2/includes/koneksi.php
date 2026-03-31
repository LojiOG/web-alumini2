<?php
// ================================================
// koneksi.php - Koneksi ke Database
// ================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');    // Sesuaikan
define('DB_PASS', '');        // Sesuaikan
define('DB_NAME', 'db_alumni_smk');

$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($koneksi->connect_error) {
    die("<div style='padding:20px;font-family:Arial;color:red'>
        <h3>❌ Koneksi Gagal!</h3>
        <p>" . $koneksi->connect_error . "</p>
        <p>Pastikan XAMPP berjalan dan database sudah diimport.</p>
    </div>");
}

$koneksi->set_charset("utf8");
?>
