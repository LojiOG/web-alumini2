<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

$events = $koneksi->query("SELECT * FROM event ORDER BY tanggal ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event & Reuni — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .event-card { border: none; border-radius: 14px; transition: transform .2s; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.1) !important; }
        .date-badge {
            background: linear-gradient(135deg,#1a237e,#3949ab);
            color: white; border-radius: 10px; padding: 8px 14px; text-align: center; min-width: 60px;
        }
        .past-event { opacity: .65; }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="font-size:40px">📅</div>
        <div>
            <h4 class="fw-bold mb-0">Event & Reuni Alumni</h4>
            <p class="text-muted mb-0 small">Tetap terhubung dengan sesama alumni</p>
        </div>
    </div>

    <?php if ($events->num_rows > 0):
        $ada_mendatang = false;
        $ada_lewat = false;
        // Pisahkan event mendatang & lewat
        $semua = [];
        while ($r = $events->fetch_assoc()) $semua[] = $r;

        $mendatang = array_filter($semua, fn($e) => $e['tanggal'] >= date('Y-m-d'));
        $lewat     = array_filter($semua, fn($e) => $e['tanggal'] < date('Y-m-d'));

        if (!empty($mendatang)): ?>
    <h6 class="text-primary fw-bold mb-3">🔜 Akan Datang</h6>
    <div class="row g-3 mb-4">
        <?php foreach ($mendatang as $e): ?>
        <div class="col-md-6">
            <div class="card event-card shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex gap-3">
                        <div class="date-badge flex-shrink-0">
                            <div class="fw-bold fs-4"><?= date('d', strtotime($e['tanggal'])) ?></div>
                            <div class="small"><?= date('M', strtotime($e['tanggal'])) ?></div>
                            <div style="font-size:11px"><?= date('Y', strtotime($e['tanggal'])) ?></div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1"><?= htmlspecialchars($e['judul']) ?></h6>
                            <p class="text-muted small mb-2"><?= nl2br(htmlspecialchars(substr($e['deskripsi'],0,100))) ?>...</p>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($e['lokasi']) ?><br>
                                <i class="bi bi-clock me-1"></i><?= substr($e['waktu'],0,5) ?> WIB
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm w-100"
                            data-bs-toggle="modal" data-bs-target="#modalEvent"
                            data-judul="<?= htmlspecialchars($e['judul']) ?>"
                            data-deskripsi="<?= htmlspecialchars($e['deskripsi']) ?>"
                            data-lokasi="<?= htmlspecialchars($e['lokasi']) ?>"
                            data-tanggal="<?= tglIndo($e['tanggal']) ?>"
                            data-waktu="<?= substr($e['waktu'],0,5) ?>">
                            Lihat Detail
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif;

    if (!empty($lewat)): ?>
    <h6 class="text-muted fw-bold mb-3">📁 Event Telah Lewat</h6>
    <div class="row g-3">
        <?php foreach ($lewat as $e): ?>
        <div class="col-md-6">
            <div class="card event-card shadow-sm past-event">
                <div class="card-body p-3 d-flex gap-3">
                    <div class="text-center flex-shrink-0">
                        <div class="fw-bold"><?= date('d M', strtotime($e['tanggal'])) ?></div>
                        <div class="text-muted small"><?= date('Y', strtotime($e['tanggal'])) ?></div>
                    </div>
                    <div>
                        <div class="fw-semibold small"><?= htmlspecialchars($e['judul']) ?></div>
                        <div class="text-muted" style="font-size:12px">📍 <?= htmlspecialchars($e['lokasi']) ?></div>
                    </div>
                    <span class="badge bg-secondary ms-auto align-self-start">Selesai</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif;
    else: ?>
    <div class="text-center py-5">
        <span style="font-size:64px">📭</span>
        <h5 class="text-muted mt-3">Belum ada event.</h5>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Event -->
<div class="modal fade" id="modalEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 overflow-hidden">
            <div class="modal-header text-white" style="background:linear-gradient(135deg,#1a237e,#3949ab)">
                <h5 class="modal-title fw-bold" id="mJudul"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex gap-3 mb-3">
                    <span class="text-muted"><i class="bi bi-geo-alt me-1"></i><span id="mLokasi"></span></span>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <span class="text-muted"><i class="bi bi-calendar me-1"></i><span id="mTanggal"></span></span>
                    <span class="text-muted"><i class="bi bi-clock me-1"></i><span id="mWaktu"></span> WIB</span>
                </div>
                <hr>
                <p id="mDeskripsi" style="white-space:pre-wrap"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('modalEvent').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('mJudul').textContent   = b.dataset.judul;
    document.getElementById('mLokasi').textContent  = b.dataset.lokasi;
    document.getElementById('mTanggal').textContent = b.dataset.tanggal;
    document.getElementById('mWaktu').textContent   = b.dataset.waktu;
    document.getElementById('mDeskripsi').textContent = b.dataset.deskripsi;
});
</script>
</body>
</html>
