<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $koneksi->prepare("SELECT * FROM alumni WHERE id_alumni=? AND role='user'");
$stmt->bind_param("i",$id); $stmt->execute();
$alumni = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$alumni) redirect('kelola_alumni.php');

$pesan=''; $tipe_pesan='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama=bersihkan($_POST['nama']??'');
    $tahun=bersihkan($_POST['tahun_lulus']??'');
    $jurusan=bersihkan($_POST['jurusan']??'');
    $pekerjaan=bersihkan($_POST['pekerjaan']??'');
    $pw_baru=$_POST['password_baru']??'';

    if (kosong($nama)||kosong($tahun)||kosong($jurusan)) {
        $pesan="Field wajib tidak boleh kosong!"; $tipe_pesan='danger';
    } else {
        if (!empty($pw_baru)) {
            $hash=password_hash($pw_baru,PASSWORD_DEFAULT);
            $stmt=$koneksi->prepare("UPDATE alumni SET nama=?,tahun_lulus=?,jurusan=?,pekerjaan=?,password=? WHERE id_alumni=?");
            $stmt->bind_param("sssssi",$nama,$tahun,$jurusan,$pekerjaan,$hash,$id);
        } else {
            $stmt=$koneksi->prepare("UPDATE alumni SET nama=?,tahun_lulus=?,jurusan=?,pekerjaan=? WHERE id_alumni=?");
            $stmt->bind_param("ssssi",$nama,$tahun,$jurusan,$pekerjaan,$id);
        }
        if ($stmt->execute()) redirect('kelola_alumni.php?pesan=edit');
        else { $pesan="Gagal update!"; $tipe_pesan='danger'; }
        $stmt->close();
    }
}
$jurusan_list=['Teknik Komputer Jaringan','Rekayasa Perangkat Lunak','Multimedia',
    'Akuntansi','Administrasi Perkantoran','Teknik Kendaraan Ringan','Teknik Instalasi Listrik'];
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Alumni — Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>body{background:#f0f2f5;}</style></head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4"><div class="row justify-content-center"><div class="col-md-7">
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Alumni</h5>
        <a href="kelola_alumni.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
    <div class="card-body p-4">
        <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($alumni['nama']) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Email</label>
                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($alumni['email']) ?>" disabled>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Tahun Lulus *</label>
                    <input type="number" name="tahun_lulus" class="form-control"
                        value="<?= $alumni['tahun_lulus'] ?>" min="2000" max="2099" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold small">Jurusan *</label>
                    <select name="jurusan" class="form-select" required>
                        <?php foreach ($jurusan_list as $j): ?>
                        <option value="<?= $j ?>" <?= $alumni['jurusan']===$j?'selected':'' ?>><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Pekerjaan</label>
                <input type="text" name="pekerjaan" class="form-control" value="<?= htmlspecialchars($alumni['pekerjaan']) ?>">
            </div>
            <hr>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Password Baru <span class="text-muted fw-normal">(kosongkan jika tidak ganti)</span></label>
                <input type="password" name="password_baru" class="form-control" placeholder="Isi jika ingin ganti password">
            </div>
            <button type="submit" class="btn btn-warning w-100">
                <i class="bi bi-save me-1"></i>Simpan Perubahan
            </button>
        </form>
    </div>
</div>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
