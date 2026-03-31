<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM alumni WHERE id_alumni=? AND role='user'");
    $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    redirect('kelola_alumni.php?pesan=hapus');
}

$pesan=''; $tipe_pesan='';
if (isset($_GET['pesan'])) {
    $map=['tambah'=>['Alumni ditambahkan!','success'],'edit'=>['Data diperbarui!','success'],'hapus'=>['Alumni dihapus!','warning']];
    if(isset($map[$_GET['pesan']])){$pesan=$map[$_GET['pesan']][0];$tipe_pesan=$map[$_GET['pesan']][1];}
}

$cari_nama  = isset($_GET['nama'])  ? bersihkan($_GET['nama'])  : '';
$cari_tahun = isset($_GET['tahun']) ? bersihkan($_GET['tahun']) : '';

$sql = "SELECT * FROM alumni WHERE role='user'";
$params = []; $types = '';
if (!empty($cari_nama))  { $sql .= " AND nama LIKE ?"; $params[] = "%$cari_nama%"; $types .= 's'; }
if (!empty($cari_tahun)) { $sql .= " AND tahun_lulus=?"; $params[] = $cari_tahun; $types .= 's'; }
$sql .= " ORDER BY nama ASC";

$stmt = $koneksi->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$alumni_list = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Alumni — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>body{background:#f0f2f5;}</style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0"><i class="bi bi-people text-primary me-2"></i>Kelola Data Alumni</h5>
            <a href="tambah_alumni.php" class="btn btn-primary btn-sm">
                <i class="bi bi-person-plus me-1"></i>Tambah Alumni
            </a>
        </div>
        <div class="card-body">
            <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="nama" class="form-control"
                            placeholder="Cari nama alumni..."
                            value="<?= htmlspecialchars($cari_nama) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="number" name="tahun" class="form-control form-control-sm"
                        placeholder="Tahun lulus" value="<?= htmlspecialchars($cari_tahun) ?>"
                        min="2000" max="2099">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    <a href="kelola_alumni.php" class="btn btn-outline-secondary btn-sm ms-1">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr><th>#</th><th>Nama</th><th>Email</th><th>Jurusan</th><th>Angkatan</th><th>Pekerjaan</th><th class="text-center">Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if ($alumni_list->num_rows > 0): $no=1;
                            while ($r = $alumni_list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="fw-semibold small"><?= htmlspecialchars($r['nama']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($r['email']) ?></td>
                            <td class="small"><?= htmlspecialchars($r['jurusan']) ?></td>
                            <td><span class="badge bg-info text-dark"><?= $r['tahun_lulus'] ?></span></td>
                            <td class="small text-muted"><?= htmlspecialchars($r['pekerjaan']) ?></td>
                            <td class="text-center">
                                <a href="edit_alumni.php?id=<?= $r['id_alumni'] ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="?hapus=<?= $r['id_alumni'] ?>" class="btn btn-danger btn-sm ms-1"
                                    onclick="return confirm('Hapus alumni <?= htmlspecialchars($r['nama']) ?>?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data alumni ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Menampilkan <?= $alumni_list->num_rows ?> alumni</small>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
