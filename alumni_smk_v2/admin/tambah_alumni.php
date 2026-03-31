<?php
// tambah_alumni.php & edit_alumni.php digabung di 1 file ini
// (edit_alumni.php dibuat terpisah di bawah)
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

$pesan=''; $tipe_pesan='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama=$_POST['nama']??''; $email=$_POST['email']??'';
    $password=$_POST['password']??''; $tahun=$_POST['tahun_lulus']??'';
    $jurusan=$_POST['jurusan']??''; $pekerjaan=$_POST['pekerjaan']??'';

    $nama=bersihkan($nama); $email=bersihkan($email);
    $tahun=bersihkan($tahun); $jurusan=bersihkan($jurusan); $pekerjaan=bersihkan($pekerjaan);

    if (kosong($nama)||kosong($email)||kosong($password)||kosong($tahun)||kosong($jurusan)) {
        $pesan="Semua field wajib diisi!"; $tipe_pesan='danger';
    } elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $pesan="Format email tidak valid!"; $tipe_pesan='danger';
    } else {
        $cek=$koneksi->prepare("SELECT id_alumni FROM alumni WHERE email=?");
        $cek->bind_param("s",$email); $cek->execute(); $cek->store_result();
        if ($cek->num_rows > 0) {
            $pesan="Email sudah digunakan!"; $tipe_pesan='danger';
        } else {
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $stmt=$koneksi->prepare("INSERT INTO alumni (nama,email,password,tahun_lulus,jurusan,pekerjaan,role) VALUES (?,?,?,?,?,?,'user')");
            $stmt->bind_param("ssssss",$nama,$email,$hash,$tahun,$jurusan,$pekerjaan);
            if ($stmt->execute()) redirect('kelola_alumni.php?pesan=tambah');
            else { $pesan="Gagal menyimpan!"; $tipe_pesan='danger'; }
            $stmt->close();
        }
        $cek->close();
    }
}
$jurusan_list=['Teknik Komputer Jaringan','Rekayasa Perangkat Lunak','Multimedia',
    'Akuntansi','Administrasi Perkantoran','Teknik Kendaraan Ringan','Teknik Instalasi Listrik'];
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Alumni — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f0f2f5;}</style></head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4"><div class="row justify-content-center"><div class="col-md-7">
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Tambah Alumni Baru</h5>
        <a href="kelola_alumni.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
    <div class="card-body p-4">
        <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control"
                        value="<?= isset($_POST['nama'])?htmlspecialchars($_POST['nama']):'' ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Email *</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= isset($_POST['email'])?htmlspecialchars($_POST['email']):'' ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Tahun Lulus *</label>
                    <input type="number" name="tahun_lulus" class="form-control"
                        value="<?= isset($_POST['tahun_lulus'])?$_POST['tahun_lulus']:date('Y') ?>"
                        min="2000" max="2099" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Jurusan *</label>
                <select name="jurusan" class="form-select" required>
                    <option value="">-- Pilih Jurusan --</option>
                    <?php foreach ($jurusan_list as $j): ?>
                    <option value="<?= $j ?>" <?= (isset($_POST['jurusan'])&&$_POST['jurusan']===$j)?'selected':'' ?>><?= $j ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Pekerjaan</label>
                <input type="text" name="pekerjaan" class="form-control"
                    value="<?= isset($_POST['pekerjaan'])?htmlspecialchars($_POST['pekerjaan']):'' ?>"
                    placeholder="Contoh: Web Developer (opsional)">
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-save me-1"></i>Simpan Alumni
            </button>
        </form>
    </div>
</div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
