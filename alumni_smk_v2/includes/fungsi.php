<?php
// ================================================
// fungsi.php - Fungsi Pembantu
// ================================================

function redirect($url) {
    header("Location: $url");
    exit();
}

function cekLogin() {
    if (!isset($_SESSION['id_alumni'])) {
        redirect('../index.php');
    }
}

function cekAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        redirect('../alumni/dashboard.php');
    }
}

function tampilPesan($pesan, $tipe = 'success') {
    $icon = ($tipe === 'success') ? '✅' : (($tipe === 'warning') ? '⚠️' : '❌');
    echo "<div class='alert alert-{$tipe} alert-dismissible fade show' role='alert'>
        {$icon} {$pesan}
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
}

function bersihkan($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function kosong($nilai) {
    return empty(trim($nilai));
}

// Format tanggal Indonesia
function tglIndo($tgl) {
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    $d = explode('-', date('Y-m-d', strtotime($tgl)));
    return $d[2] . ' ' . $bulan[(int)$d[1]] . ' ' . $d[0];
}
?>
