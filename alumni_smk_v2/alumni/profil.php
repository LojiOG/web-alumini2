<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

$id = $_SESSION['id_alumni'];
$pesan = ''; $tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = bersihkan($_POST['nama']);
    $jurusan   = bersihkan($_POST['jurusan']);
    $pekerjaan = bersihkan($_POST['pekerjaan']);
    $pw_baru   = $_POST['password_baru'];

    if (kosong($nama) || kosong($jurusan)) {
        $pesan = "Nama dan jurusan wajib diisi!";
        $tipe_pesan = 'danger';
    } else {
        if (!empty($pw_baru)) {
            $hash = password_hash($pw_baru, PASSWORD_DEFAULT);
            $stmt = $koneksi->prepare("UPDATE alumni SET nama=?,jurusan=?,pekerjaan=?,password=? WHERE id_alumni=?");
            $stmt->bind_param("ssssi", $nama, $jurusan, $pekerjaan, $hash, $id);
        } else {
            $stmt = $koneksi->prepare("UPDATE alumni SET nama=?,jurusan=?,pekerjaan=? WHERE id_alumni=?");
            $stmt->bind_param("sssi", $nama, $jurusan, $pekerjaan, $id);
        }
        if ($stmt->execute()) {
            $_SESSION['nama'] = $nama;
            $pesan = "Profil berhasil diperbarui!";
            $tipe_pesan = 'success';
        } else {
            $pesan = "Gagal memperbarui profil.";
            $tipe_pesan = 'danger';
        }
        $stmt->close();
    }
}

$stmt = $koneksi->prepare("SELECT * FROM alumni WHERE id_alumni=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$alumni = $stmt->get_result()->fetch_assoc();
$stmt->close();

$jurusan_list = ['Teknik Komputer Jaringan','Rekayasa Perangkat Lunak','Multimedia',
    'Akuntansi','Administrasi Perkantoran','Teknik Kendaraan Ringan','Teknik Instalasi Listrik'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>body { background: #f0f2f5; }</style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <!-- Avatar Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-3 text-center p-4"
                style="background:linear-gradient(135deg,#1a237e,#3949ab)">
                <div style="width:80px;height:80px;background:white;border-radius:50%;
                    margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:40px">
                    👤
                </div>
                <h5 class="text-white fw-bold mb-1"><?= htmlspecialchars($alumni['nama']) ?></h5>
                <p class="text-white-50 small mb-0">
                    <?= htmlspecialchars($alumni['jurusan']) ?> — Angkatan <?= $alumni['tahun_lulus'] ?>
                </p>
            </div>

            <!-- Form Edit -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-person-gear text-primary me-2"></i>Edit Profil</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($pesan): tampilPesan($pesan, $tipe_pesan); endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                value="<?= htmlspecialchars($alumni['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Email</label>
                            <input type="email" class="form-control bg-light"
                                value="<?= htmlspecialchars($alumni['email']) ?>" disabled>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small">Tahun Lulus</label>
                                <input type="text" class="form-control bg-light"
                                    value="<?= $alumni['tahun_lulus'] ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small">Jurusan</label>
                                <select name="jurusan" class="form-select">
                                    <?php foreach ($jurusan_list as $j): ?>
                                    <option value="<?= $j ?>" <?= $alumni['jurusan']===$j?'selected':'' ?>><?= $j ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Pekerjaan Saat Ini</label>
                            <input type="text" name="pekerjaan" class="form-control"
                                placeholder="Contoh: Web Developer di PT ABC"
                                value="<?= htmlspecialchars($alumni['pekerjaan']) ?>">
                        </div>
                        <hr>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">
                                Password Baru <span class="text-muted fw-normal">(kosongkan jika tidak ingin ganti)</span>
                            </label>
                            <input type="password" name="password_baru" class="form-control"
                                placeholder="Isi jika ingin ganti password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
