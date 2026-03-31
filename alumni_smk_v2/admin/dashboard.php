<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin(); cekAdmin();

$stats = [
    'alumni'  => $koneksi->query("SELECT COUNT(*) as t FROM alumni WHERE role='user'")->fetch_assoc()['t'],
    'event'   => $koneksi->query("SELECT COUNT(*) as t FROM event WHERE tanggal >= CURDATE()")->fetch_assoc()['t'],
    'berita'  => $koneksi->query("SELECT COUNT(*) as t FROM berita")->fetch_assoc()['t'],
    'galeri'  => $koneksi->query("SELECT COUNT(*) as t FROM galeri")->fetch_assoc()['t'],
];
$alumni_terbaru = $koneksi->query("SELECT * FROM alumni WHERE role='user' ORDER BY created_at DESC LIMIT 5");
$event_terbaru  = $koneksi->query("SELECT * FROM event ORDER BY tanggal ASC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .stat-card { border: none; border-radius: 14px; transition: transform .2s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,.12) !important; }
        .welcome { background: linear-gradient(135deg,#1a237e,#283593); border-radius:16px; color:white; padding:28px 32px; }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>
<div class="container mt-4 pb-5">

    <div class="welcome mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="fw-bold mb-1">⚙️ Panel Admin</h4>
                <p class="opacity-75 mb-0">Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! Kelola konten web alumni dari sini.</p>
            </div>
            <div class="col-auto" style="font-size:64px">🛡️</div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row g-3 mb-4">
        <?php
        $stat_items = [
            ['👥','Alumni Terdaftar',$stats['alumni'],'primary'],
            ['📅','Event Mendatang',$stats['event'],'success'],
            ['📰','Total Berita',$stats['berita'],'warning'],
            ['🖼️','Foto Galeri',$stats['galeri'],'info'],
        ];
        foreach ($stat_items as [$icon, $label, $val, $color]):
        ?>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center py-3">
                <div style="font-size:32px"><?= $icon ?></div>
                <div class="fs-2 fw-bold text-<?= $color ?>"><?= $val ?></div>
                <div class="text-muted small"><?= $label ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Akses Cepat -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <span class="fw-bold"><i class="bi bi-lightning-charge text-warning me-2"></i>Kelola Konten</span>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <a href="kelola_alumni.php" class="btn btn-outline-primary w-100 py-3">
                                <div style="font-size:28px">👥</div>
                                <div class="small fw-semibold">Alumni</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="kelola_event.php" class="btn btn-outline-success w-100 py-3">
                                <div style="font-size:28px">📅</div>
                                <div class="small fw-semibold">Event</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="kelola_berita.php" class="btn btn-outline-warning w-100 py-3">
                                <div style="font-size:28px">📰</div>
                                <div class="small fw-semibold">Berita</div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="kelola_galeri.php" class="btn btn-outline-info w-100 py-3">
                                <div style="font-size:28px">🖼️</div>
                                <div class="small fw-semibold">Galeri</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alumni & Event Terbaru -->
    <div class="row g-3">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
                    <span class="fw-bold"><i class="bi bi-people text-primary me-2"></i>Alumni Terbaru</span>
                    <a href="kelola_alumni.php" class="btn btn-primary btn-sm">Kelola</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Nama</th><th>Jurusan</th><th>Angkatan</th></tr></thead>
                        <tbody>
                            <?php while ($r = $alumni_terbaru->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-semibold small"><?= htmlspecialchars($r['nama']) ?></td>
                                <td class="small text-muted"><?= htmlspecialchars($r['jurusan']) ?></td>
                                <td><span class="badge bg-primary"><?= $r['tahun_lulus'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
                    <span class="fw-bold"><i class="bi bi-calendar-event text-success me-2"></i>Event Mendatang</span>
                    <a href="kelola_event.php" class="btn btn-success btn-sm">Kelola</a>
                </div>
                <div class="card-body p-3">
                    <?php while ($e = $event_terbaru->fetch_assoc()): ?>
                    <div class="d-flex gap-2 mb-2 pb-2 border-bottom">
                        <div class="text-center flex-shrink-0" style="min-width:44px;background:#e8eaf6;border-radius:8px;padding:4px">
                            <div class="fw-bold text-primary" style="font-size:18px"><?= date('d',strtotime($e['tanggal'])) ?></div>
                            <div class="text-muted" style="font-size:10px"><?= date('M',strtotime($e['tanggal'])) ?></div>
                        </div>
                        <div>
                            <div class="fw-semibold small"><?= htmlspecialchars($e['judul']) ?></div>
                            <div class="text-muted" style="font-size:11px">📍 <?= htmlspecialchars(substr($e['lokasi'],0,30)) ?></div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
