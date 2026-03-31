<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = $koneksi->prepare("DELETE FROM galeri WHERE id_galeri=?");
    $stmt->bind_param("i",$id); $stmt->execute(); $stmt->close();
    redirect('kelola_galeri.php?pesan=hapus');
}

$pesan=''; $tipe_pesan='';
if (isset($_GET['pesan'])) {
    $map=['tambah'=>['Foto berhasil ditambahkan!','success'],'edit'=>['Foto berhasil diperbarui!','success'],'hapus'=>['Foto berhasil dihapus!','warning']];
    if(isset($map[$_GET['pesan']])){$pesan=$map[$_GET['pesan']][0];$tipe_pesan=$map[$_GET['pesan']][1];}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul      = bersihkan($_POST['judul']);
    $keterangan = bersihkan($_POST['keterangan']);
    $angkatan   = bersihkan($_POST['angkatan']);
    $foto_url   = bersihkan($_POST['foto_url']);
    $edit_id    = (int)$_POST['edit_id'];
    $id_admin   = $_SESSION['id_alumni'];
    
    // Proses upload foto jika ada
    $upload_path = '';
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $filename = $_FILES['foto_upload']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = 'galeri_' . time() . '_' . uniqid() . '.' . $ext;
            $target = '../uploads/galeri/' . $new_name;
            
            if (move_uploaded_file($_FILES['foto_upload']['tmp_name'], $target)) {
                // Simpan sebagai absolute path dari root
                $upload_path = 'uploads/galeri/' . $new_name;
            }
        }
    }
    
    // Prioritas: upload > URL
    $final_url = !empty($upload_path) ? $upload_path : $foto_url;

    if (!kosong($judul) && !kosong($angkatan) && !kosong($final_url)) {
        if ($edit_id > 0) {
            $stmt = $koneksi->prepare("UPDATE galeri SET judul=?,keterangan=?,angkatan=?,foto_url=? WHERE id_galeri=?");
            $stmt->bind_param("ssisi",$judul,$keterangan,$angkatan,$final_url,$edit_id);
            $stmt->execute(); $stmt->close();
            redirect('kelola_galeri.php?pesan=edit');
        } else {
            $stmt = $koneksi->prepare("INSERT INTO galeri (judul,keterangan,angkatan,foto_url,id_alumni) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssisi",$judul,$keterangan,$angkatan,$final_url,$id_admin);
            $stmt->execute(); $stmt->close();
            redirect('kelola_galeri.php?pesan=tambah');
        }
    }
}

$galeri_list = $koneksi->query("SELECT * FROM galeri ORDER BY angkatan DESC, created_at DESC");
$edit_data = null;
if (isset($_GET['edit'])) {
    $stmt = $koneksi->prepare("SELECT * FROM galeri WHERE id_galeri=?");
    $stmt->bind_param("i",(int)$_GET['edit']); $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>body{background:#f0f2f5;} .thumb{width:60px;height:45px;object-fit:cover;border-radius:6px;}</style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="row g-3">
        <!-- Form -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0"><?= $edit_data ? '✏️ Edit Foto' : '➕ Tambah Foto' ?></h6>
                </div>
                <div class="card-body p-4">
                    <?php if ($pesan): tampilPesan($pesan,$tipe_pesan); endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="edit_id" value="<?= $edit_data ? $edit_data['id_galeri'] : 0 ?>">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Judul Foto *</label>
                            <input type="text" name="judul" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['judul']) : '' ?>" required
                                placeholder="Contoh: Wisuda Angkatan 2022">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Angkatan *</label>
                            <input type="number" name="angkatan" class="form-control form-control-sm"
                                value="<?= $edit_data ? $edit_data['angkatan'] : date('Y') ?>"
                                min="2000" max="2099" required>
                        </div>
                        
                        <!-- Opsi 1: Upload File -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                📁 Upload Foto <span class="text-muted fw-normal">(atau isi URL di bawah)</span>
                            </label>
                            <input type="file" name="foto_upload" id="fotoUpload" 
                                class="form-control form-control-sm" accept="image/*"
                                onchange="previewUpload(this)">
                            <small class="text-muted">Format: JPG, PNG, GIF, WEBP (max 5MB)</small>
                        </div>
                        
                        <!-- Preview upload -->
                        <div id="uploadPreview" class="mb-2 d-none">
                            <img id="uploadImg" src="" class="img-fluid rounded" style="max-height:120px;width:100%;object-fit:cover">
                        </div>
                        
                        <div class="text-center my-2 small text-muted">— ATAU —</div>
                        
                        <!-- Opsi 2: URL -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                🔗 URL Foto <span class="text-muted fw-normal">(dari Google Drive, Imgur, dll)</span>
                            </label>
                            <input type="url" name="foto_url" id="fotoUrl" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['foto_url']) : '' ?>"
                                placeholder="https://..." 
                                oninput="previewUrl(this.value)">
                        </div>
                        
                        <!-- Preview URL -->
                        <div id="urlPreview" class="mb-2 d-none">
                            <img id="urlImg" src="" class="img-fluid rounded" style="max-height:120px;width:100%;object-fit:cover">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Keterangan</label>
                            <input type="text" name="keterangan" class="form-control form-control-sm"
                                value="<?= $edit_data ? htmlspecialchars($edit_data['keterangan']) : '' ?>"
                                placeholder="Deskripsi singkat (opsional)">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="bi bi-save me-1"></i><?= $edit_data ? 'Simpan' : 'Tambah Foto' ?>
                            </button>
                            <?php if ($edit_data): ?>
                            <a href="kelola_galeri.php" class="btn btn-outline-secondary btn-sm">Batal</a>
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
                    <h6 class="fw-bold mb-0"><i class="bi bi-images text-warning me-2"></i>Daftar Foto</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Foto</th><th>Judul</th><th>Angkatan</th><th class="text-center">Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($galeri_list->num_rows > 0):
                                while ($r = $galeri_list->fetch_assoc()): 
                                // Tentukan URL foto yang benar
                                $foto_src = ''; 
                                $foto_src = htmlspecialchars($r['foto_url']);
                            ?>
                            <tr>
                                <td>
                                    <img src="<?= $foto_src ?>" class="thumb" alt="<?= htmlspecialchars($r['judul']) ?>">
                                </td>
                                <td class="fw-semibold small"><?= htmlspecialchars($r['judul']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $r['angkatan'] ?></span></td>
                                <td class="text-center">
                                    <a href="?edit=<?= $r['id_galeri'] ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="?hapus=<?= $r['id_galeri'] ?>" class="btn btn-danger btn-sm ms-1"
                                        onclick="return confirm('Hapus foto ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada foto.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preview file upload
function previewUpload(input) {
    const uploadWrap = document.getElementById('uploadPreview');
    const uploadImg  = document.getElementById('uploadImg');
    const urlWrap    = document.getElementById('urlPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadImg.src = e.target.result;
            uploadWrap.classList.remove('d-none');
            urlWrap.classList.add('d-none');
            document.getElementById('fotoUrl').value = '';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        uploadWrap.classList.add('d-none');
    }
}

// Preview URL
function previewUrl(url) {
    const urlWrap    = document.getElementById('urlPreview');
    const urlImg     = document.getElementById('urlImg');
    const uploadWrap = document.getElementById('uploadPreview');
    
    if (url) {
        urlImg.src = url;
        urlWrap.classList.remove('d-none');
        document.getElementById('fotoUpload').value = '';
        uploadWrap.classList.add('d-none');
    } else {
        urlWrap.classList.add('d-none');
    }
}

// Init preview jika edit mode
const urlInput = document.getElementById('fotoUrl');
if (urlInput && urlInput.value) {
    if (urlInput.value.startsWith('http')) {
        previewUrl(urlInput.value);
    }
}
</script>
</body>
</html>