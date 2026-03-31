<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

$galeri_list = $koneksi->query("SELECT * FROM galeri ORDER BY angkatan DESC, created_at DESC");

// Kelompokkan per angkatan
$per_angkatan = [];
while ($g = $galeri_list->fetch_assoc()) {
    $per_angkatan[$g['angkatan']][] = $g;
}
krsort($per_angkatan); // Angkatan terbaru di atas
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Foto — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .foto-wrap {
            border-radius: 12px; overflow: hidden; position: relative; cursor: pointer;
            aspect-ratio: 4/3;
        }
        .foto-wrap img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform .3s;
        }
        .foto-wrap:hover img { transform: scale(1.06); }
        .foto-overlay {
            position: absolute; bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,.7));
            color: white; padding: 20px 12px 10px; font-size: 13px;
            opacity: 0; transition: opacity .3s;
        }
        .foto-wrap:hover .foto-overlay { opacity: 1; }
        .angkatan-header {
            background: linear-gradient(135deg,#1a237e,#3949ab);
            color: white; border-radius: 10px; padding: 10px 18px;
            display: inline-flex; align-items: center; gap: 10px;
        }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="font-size:40px">🖼️</div>
        <div>
            <h4 class="fw-bold mb-0">Galeri Foto Angkatan</h4>
            <p class="text-muted mb-0 small">Kenangan indah bersama teman-teman</p>
        </div>
    </div>

    <?php if (!empty($per_angkatan)):
        foreach ($per_angkatan as $angkatan => $fotos): ?>
    <div class="mb-5">
        <div class="angkatan-header mb-3">
            🎓 <span class="fw-bold">Angkatan <?= $angkatan ?></span>
            <span class="badge bg-white text-primary ms-2"><?= count($fotos) ?> foto</span>
        </div>
        <div class="row g-3">
            <?php foreach ($fotos as $f): 
                // Tentukan URL foto yang benar
                $foto_src = '';
                if (strpos($f['foto_url'], 'http') === 0) {
                    // URL eksternal (Google Drive, Imgur, dll)
                    $foto_src = htmlspecialchars($f['foto_url']);
                } elseif (strpos($f['foto_url'], '/') === 0) {
                    // Absolute path
                    $foto_src = htmlspecialchars($f['foto_url']);
                } else {
                    // Relative path - tambahkan prefix
                    $foto_src = '/alumni_smk_v2/' . htmlspecialchars($f['foto_url']);
                }
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="foto-wrap shadow-sm"
                    data-bs-toggle="modal" data-bs-target="#modalFoto"
                    data-src="<?= $foto_src ?>"
                    data-judul="<?= htmlspecialchars($f['judul']) ?>"
                    data-ket="<?= htmlspecialchars($f['keterangan']) ?>">
                    <img src="<?= $foto_src ?>" alt="<?= htmlspecialchars($f['judul']) ?>">
                    <div class="foto-overlay"><?= htmlspecialchars($f['judul']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach;
    else: ?>
    <div class="text-center py-5">
        <span style="font-size:64px">📭</span>
        <h5 class="text-muted mt-3">Belum ada foto di galeri.</h5>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Foto Besar -->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border-0 rounded-3 overflow-hidden">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="fotoModal" src="" class="img-fluid" style="max-height:70vh;width:100%;object-fit:contain">
            </div>
            <div class="modal-footer border-0 flex-column align-items-start px-4">
                <div class="fw-bold text-white" id="fotoJudul"></div>
                <div class="text-white-50 small" id="fotoKet"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('modalFoto').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('fotoModal').src      = b.dataset.src;
    document.getElementById('fotoJudul').textContent = b.dataset.judul;
    document.getElementById('fotoKet').textContent   = b.dataset.ket;
});
</script>
</body>
</html>