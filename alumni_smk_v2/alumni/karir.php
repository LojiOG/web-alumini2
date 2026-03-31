<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

$karir_list = $koneksi->query("SELECT * FROM info_karir ORDER BY tanggal DESC");
$ikon = ['Tips Karir'=>'💡','Sertifikasi'=>'📜','Beasiswa'=>'🎓','Magang'=>'💼','Lainnya'=>'📌'];
$warna = ['Tips Karir'=>'success','Sertifikasi'=>'primary','Beasiswa'=>'warning','Magang'=>'info','Lainnya'=>'secondary'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Karir — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .karir-card { border: none; border-radius: 14px; transition: transform .2s; cursor: pointer; }
        .karir-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.1) !important; }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="d-flex align-items-center gap-3 mb-2">
        <div style="font-size:40px">💡</div>
        <div>
            <h4 class="fw-bold mb-0">Info Karir</h4>
            <p class="text-muted mb-0 small">Tips, sertifikasi, beasiswa, dan peluang untuk kamu</p>
        </div>
    </div>
    <p class="text-muted small mb-4 ms-5 ps-3">
        ℹ️ Bukan job board — ini tentang <b>pengembangan diri</b> dan <b>peluang</b> untuk alumni SMK.
    </p>

    <?php if ($karir_list->num_rows > 0): ?>
    <div class="row g-3">
        <?php while ($k = $karir_list->fetch_assoc()):
            $kat = $k['kategori'];
        ?>
        <div class="col-md-6">
            <div class="card karir-card shadow-sm h-100"
                data-bs-toggle="modal" data-bs-target="#modalKarir"
                data-judul="<?= htmlspecialchars($k['judul']) ?>"
                data-isi="<?= htmlspecialchars($k['isi']) ?>"
                data-kategori="<?= htmlspecialchars($kat) ?>"
                data-tanggal="<?= tglIndo($k['tanggal']) ?>"
                data-ikon="<?= $ikon[$kat] ?? '📌' ?>">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div style="font-size:36px;flex-shrink:0"><?= $ikon[$kat] ?? '📌' ?></div>
                        <div>
                            <span class="badge bg-<?= $warna[$kat] ?? 'secondary' ?> mb-1"><?= $kat ?></span>
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($k['judul']) ?></h6>
                            <p class="text-muted small mb-1">
                                <?= htmlspecialchars(substr($k['isi'], 0, 100)) ?>...
                            </p>
                            <small class="text-muted">📅 <?= tglIndo($k['tanggal']) ?></small>
                        </div>
                    </div>
                    <div class="text-primary small fw-semibold mt-3 text-end">Baca selengkapnya →</div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <span style="font-size:64px">📭</span>
        <h5 class="text-muted mt-3">Belum ada info karir.</h5>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Info Karir -->
<div class="modal fade" id="modalKarir" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <span style="font-size:36px" id="mIkon"></span>
                    <div>
                        <span class="badge mb-1" id="mKategori"></span>
                        <h5 class="modal-title fw-bold mb-0" id="mJudul"></h5>
                        <small class="text-muted" id="mTanggal"></small>
                    </div>
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
const warnaMap = {
    'Tips Karir':'success','Sertifikasi':'primary',
    'Beasiswa':'warning','Magang':'info','Lainnya':'secondary'
};
document.getElementById('modalKarir').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    const kat = b.dataset.kategori;
    const el = document.getElementById('mKategori');
    el.textContent = kat;
    el.className = 'badge bg-' + (warnaMap[kat] || 'secondary') + ' mb-1';
    document.getElementById('mIkon').textContent    = b.dataset.ikon;
    document.getElementById('mJudul').textContent   = b.dataset.judul;
    document.getElementById('mTanggal').textContent = '📅 ' + b.dataset.tanggal;
    document.getElementById('mIsi').textContent     = b.dataset.isi;
});
</script>
</body>
</html>
