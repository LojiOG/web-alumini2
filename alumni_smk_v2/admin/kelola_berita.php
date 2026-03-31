<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM berita WHERE id_berita=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    redirect('kelola_berita.php?pesan=hapus');
}

$pesan=''; $tipe_pesan='';
if (isset($_GET['pesan'])) {
    $map=['tambah'=>['Berita berhasil ditambahkan!','success'],'edit'=>['Berita berhasil diperbarui!','success'],'hapus'=>['Berita berhasil dihapus!','warning']];
    if(isset($map[$_GET['pesan']])){$pesan=$map[$_GET['pesan']][0];$tipe_pesan=$map[$_GET['pesan']][1];}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = bersihkan($_POST['judul']);
    $isi      = bersihkan($_POST['isi']);
    $kategori = bersihkan($_POST['kategori']);
    $tanggal  = bersihkan($_POST['tanggal']);
    $edit_id  = (int)$_POST['edit_id'];
    $id_admin = $_SESSION['id_alumni'];

    if (!kosong($judul) && !kosong($isi) && !kosong($tanggal)) {
        if ($edit_id > 0) {
            $stmt = $koneksi->prepare("UPDATE berita SET judul=?,isi=?,kategori=?,tanggal=? WHERE id_berita=?");
            $stmt->bind_param("ssssi",$judul,$isi,$kategori,$tanggal,$edit_id);
            $stmt->execute(); $stmt->close();
            redirect('kelola_berita.php?pesan=edit');
        } else {
            $stmt = $koneksi->prepare("INSERT INTO berita (judul,isi,kategori,tanggal,id_alumni) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssssi",$judul,$isi,$kategori,$tanggal,$id_admin);
            $stmt->execute(); $stmt->close();
            redirect('kelola_berita.php?pesan=tambah');
        }
    }
}

$berita_list = $koneksi->query("SELECT * FROM berita ORDER BY tanggal DESC");
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $koneksi->prepare("SELECT * FROM berita WHERE id_berita=?");
    $stmt->bind_param("i",(int)$_GET['edit']); $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}

$kategori_opt = ['Pengumuman','Berita','Prestasi','Lainnya'];
$warna = ['Pengumuman'=>'primary','Berita'=>'success','Prestasi'=>'warning','Lainnya'=>'secondary'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>body{background:#f0f2f5;}</style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="row g-3">
        <!-- Form -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0"><?= $edit_data ? '✏️ Edit Berita' : '➕ Tambah Berita' ?></h6>
                </div>
                <div class="card-body p-4">
                    <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
                    <form method="POST">
                        <input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id_berita'] : 0 ?>">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Judul *</label>
                            <input type="text" name="judul" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['judul']) : '' ?>" required>
                        </div>
                        <div class="row">
                            <div class="col mb-2">
                                <label class="form-label small fw-semibold">Kategori</label>
                                <select name="kategori" class="form-select form-select-sm">
                                    <?php foreach ($kategori_opt as $k): ?>
                                    <option value="<?= $k ?>" <?= ($edit_data && $edit_data['kategori']===$k)?'selected':'' ?>><?= $k ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col mb-2">
                                <label class="form-label small fw-semibold">Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm"
                                    value="<?= $edit_data ? $edit_data['tanggal'] : date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Isi Berita *</label>
                            <textarea name="isi" class="form-control form-control-sm" rows="6" required><?= $edit_data ? htmlspecialchars($edit_data['isi']) : '' ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-save me-1"></i><?= $edit_data ? 'Simpan' : 'Tambah' ?>
                            </button>
                            <?php if ($edit_data): ?>
                            <a href="kelola_berita.php" class="btn btn-outline-secondary btn-sm">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-newspaper text-success me-2"></i>Daftar Berita</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Judul</th><th>Kategori</th><th>Tanggal</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($berita_list->num_rows > 0):
                                while ($r = $berita_list->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-semibold small"><?= htmlspecialchars(substr($r['judul'],0,40)) ?>...</td>
                                <td><span class="badge bg-<?= $warna[$r['kategori']] ?? 'secondary' ?>"><?= $r['kategori'] ?></span></td>
                                <td class="small"><?= date('d M Y',strtotime($r['tanggal'])) ?></td>
                                <td class="text-center">
                                    <a href="?edit=<?= $r['id_berita'] ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?hapus=<?= $r['id_berita'] ?>" class="btn btn-danger btn-sm ms-1"
                                        onclick="return confirm('Hapus berita ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada berita.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
