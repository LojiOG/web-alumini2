<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

$berita_list = $koneksi->query("SELECT * FROM berita ORDER BY tanggal DESC");
$warna = ['Pengumuman'=>'primary','Berita'=>'success','Prestasi'=>'warning','Lainnya'=>'secondary'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Pengumuman — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .berita-card { border: none; border-radius: 14px; transition: transform .2s; cursor: pointer; }
        .berita-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.1) !important; }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="font-size:40px">📰</div>
        <div>
            <h4 class="fw-bold mb-0">Berita & Pengumuman</h4>
            <p class="text-muted mb-0 small">Informasi terbaru dari sekolah dan komunitas alumni</p>
        </div>
    </div>

    <?php if ($berita_list->num_rows > 0): ?>
    <div class="row g-3">
        <?php while ($b = $berita_list->fetch_assoc()): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card berita-card shadow-sm h-100"
                data-bs-toggle="modal" data-bs-target="#modalBerita"
                data-judul="<?= htmlspecialchars($b['judul']) ?>"
                data-isi="<?= htmlspecialchars($b['isi']) ?>"
                data-kategori="<?= htmlspecialchars($b['kategori']) ?>"
                data-tanggal="<?= tglIndo($b['tanggal']) ?>">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-<?= $warna[$b['kategori']] ?>"><?= $b['kategori'] ?></span>
                        <small class="text-muted"><?= tglIndo($b['tanggal']) ?></small>
                    </div>
                    <h6 class="fw-bold mb-2"><?= htmlspecialchars($b['judul']) ?></h6>
                    <p class="text-muted small flex-grow-1">
                        <?= htmlspecialchars(substr($b['isi'], 0, 120)) ?>...
                    </p>
                    <div class="text-primary small fw-semibold mt-2">Baca selengkapnya →</div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <span style="font-size:64px">📭</span>
        <h5 class="text-muted mt-3">Belum ada berita.</h5>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Baca Berita -->
<div class="modal fade" id="modalBerita" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <div>
                    <span class="badge mb-1" id="mKategori"></span>
                    <h5 class="modal-title fw-bold" id="mJudul"></h5>
                    <small class="text-muted" id="mTanggal"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p id="mIsi" style="white-space:pre-wrap;line-height:1.8"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const warna = {Pengumuman:'primary',Berita:'success',Prestasi:'warning',Lainnya:'secondary'};
document.getElementById('modalBerita').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    const kat = b.dataset.kategori;
    const el = document.getElementById('mKategori');
    el.textContent = kat;
    el.className = 'badge bg-' + (warna[kat] || 'secondary') + ' mb-1';
    document.getElementById('mJudul').textContent   = b.dataset.judul;
    document.getElementById('mTanggal').textContent = '📅 ' + b.dataset.tanggal;
    document.getElementById('mIsi').textContent     = b.dataset.isi;
});
</script>
</body>
</html>
