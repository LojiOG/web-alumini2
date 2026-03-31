<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM event WHERE id_event=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    redirect('kelola_event.php?pesan=hapus');
}

$pesan=''; $tipe_pesan='';
if (isset($_GET['pesan'])) {
    $map=['tambah'=>['Event berhasil ditambahkan!','success'],'edit'=>['Event berhasil diperbarui!','success'],'hapus'=>['Event berhasil dihapus!','warning']];
    if(isset($map[$_GET['pesan']])){$pesan=$map[$_GET['pesan']][0];$tipe_pesan=$map[$_GET['pesan']][1];}
}

// Proses form tambah/edit inline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = bersihkan($_POST['judul']);
    $deskripsi = bersihkan($_POST['deskripsi']);
    $lokasi    = bersihkan($_POST['lokasi']);
    $tanggal   = bersihkan($_POST['tanggal']);
    $waktu     = bersihkan($_POST['waktu']);
    $edit_id   = (int)$_POST['edit_id'];
    $id_admin  = $_SESSION['id_alumni'];

    if (!kosong($judul) && !kosong($lokasi) && !kosong($tanggal) && !kosong($waktu)) {
        if ($edit_id > 0) {
            $stmt = $koneksi->prepare("UPDATE event SET judul=?,deskripsi=?,lokasi=?,tanggal=?,waktu=? WHERE id_event=?");
            $stmt->bind_param("sssssi",$judul,$deskripsi,$lokasi,$tanggal,$waktu,$edit_id);
            $stmt->execute(); $stmt->close();
            redirect('kelola_event.php?pesan=edit');
        } else {
            $stmt = $koneksi->prepare("INSERT INTO event (judul,deskripsi,lokasi,tanggal,waktu,id_alumni) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sssssi",$judul,$deskripsi,$lokasi,$tanggal,$waktu,$id_admin);
            $stmt->execute(); $stmt->close();
            redirect('kelola_event.php?pesan=tambah');
        }
    }
}

$event_list = $koneksi->query("SELECT * FROM event ORDER BY tanggal ASC");

// Ambil data untuk edit jika ada ?edit=
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $koneksi->prepare("SELECT * FROM event WHERE id_event=?");
    $stmt->bind_param("i",(int)$_GET['edit']); $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>body{background:#f0f2f5;}</style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="row g-3">
        <!-- Form Tambah/Edit -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0">
                        <?= $edit_data ? '✏️ Edit Event' : '➕ Tambah Event' ?>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
                    <form method="POST">
                        <input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id_event'] : 0 ?>">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Judul Event *</label>
                            <input type="text" name="judul" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['judul']) : '' ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Lokasi *</label>
                            <input type="text" name="lokasi" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['lokasi']) : '' ?>" required>
                        </div>
                        <div class="row">
                            <div class="col mb-2">
                                <label class="form-label small fw-semibold">Tanggal *</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm"
                                    value="<?= $edit_data ? $edit_data['tanggal'] : '' ?>" required>
                            </div>
                            <div class="col mb-2">
                                <label class="form-label small fw-semibold">Waktu *</label>
                                <input type="time" name="waktu" class="form-control form-control-sm"
                                    value="<?= $edit_data ? substr($edit_data['waktu'],0,5) : '' ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="4"><?= $edit_data ? htmlspecialchars($edit_data['deskripsi']) : '' ?></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-save me-1"></i><?= $edit_data ? 'Simpan Perubahan' : 'Tambah Event' ?>
                            </button>
                            <?php if ($edit_data): ?>
                            <a href="kelola_event.php" class="btn btn-outline-secondary btn-sm">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Event -->
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event text-primary me-2"></i>Daftar Event</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Judul</th><th>Tanggal</th><th>Lokasi</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($event_list->num_rows > 0):
                                while ($r = $event_list->fetch_assoc()):
                                $lewat = $r['tanggal'] < date('Y-m-d');
                            ?>
                            <tr class="<?= $lewat ? 'text-muted' : '' ?>">
                                <td class="fw-semibold small"><?= htmlspecialchars($r['judul']) ?>
                                    <?= $lewat ? '<span class="badge bg-secondary ms-1" style="font-size:9px">Selesai</span>' : '' ?>
                                </td>
                                <td class="small"><?= date('d M Y',strtotime($r['tanggal'])) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars(substr($r['lokasi'],0,25)) ?>...</td>
                                <td class="text-center">
                                    <a href="?edit=<?= $r['id_event'] ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?hapus=<?= $r['id_event'] ?>" class="btn btn-danger btn-sm ms-1"
                                        onclick="return confirm('Hapus event ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada event.</td></tr>
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
