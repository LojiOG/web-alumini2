<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/fungsi.php';
cekLogin();

// Ambil data alumni
$stmt = $koneksi->prepare("SELECT * FROM alumni WHERE id_alumni = ?");
$stmt->bind_param("i", $_SESSION['id_alumni']);
$stmt->execute();
$alumni = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Statistik
$total_event  = $koneksi->query("SELECT COUNT(*) as t FROM event WHERE tanggal >= CURDATE()")->fetch_assoc()['t'];
$total_berita = $koneksi->query("SELECT COUNT(*) as t FROM berita")->fetch_assoc()['t'];
$total_galeri = $koneksi->query("SELECT COUNT(*) as t FROM galeri")->fetch_assoc()['t'];
$total_alumni = $koneksi->query("SELECT COUNT(*) as t FROM alumni WHERE role='user'")->fetch_assoc()['t'];

// Event mendatang
$event_list = $koneksi->query("SELECT * FROM event WHERE tanggal >= CURDATE() ORDER BY tanggal ASC LIMIT 3");

// Berita terbaru
$berita_list = $koneksi->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; }
        .welcome-card {
            background: linear-gradient(135deg, #1a237e, #3949ab);
            border-radius: 16px; color: white; padding: 28px 32px;
        }
        .stat-card {
            border: none; border-radius: 14px;
            transition: transform .2s, box-shadow .2s; cursor: default;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0,0,0,.12) !important; }
        .section-card { border: none; border-radius: 14px; }
        .badge-kategori { font-size: 11px; font-weight: 500; }
        .event-item { border-left: 4px solid #3949ab; }
    </style>
</head>
<body>
<?php require_once '../includes/navbar.php'; ?>

<div class="container mt-4 pb-5">

    <!-- Welcome Banner -->
    <div class="welcome-card mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h4 class="fw-bold mb-1">Halo, <?= htmlspecialchars($alumni['nama']) ?>! 👋</h4>
                <p class="opacity-75 mb-2">Alumni <?= htmlspecialchars($alumni['jurusan']) ?> — Angkatan <?= $alumni['tahun_lulus'] ?></p>
                <span class="badge bg-white text-primary">
                    💼 <?= htmlspecialchars($alumni['pekerjaan']) ?>
                </span>
            </div>
            <div class="col-auto d-none d-md-block" style="font-size:72px">🎓</div>
        </div>
    </div>

    <!-- Kartu Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center py-3 h-100">
                <div style="font-size:32px">📅</div>
                <div class="fs-3 fw-bold text-primary"><?= $total_event ?></div>
                <div class="text-muted small">Event Mendatang</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center py-3 h-100">
                <div style="font-size:32px">📰</div>
                <div class="fs-3 fw-bold text-success"><?= $total_berita ?></div>
                <div class="text-muted small">Berita & Info</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center py-3 h-100">
                <div style="font-size:32px">🖼️</div>
                <div class="fs-3 fw-bold text-warning"><?= $total_galeri ?></div>
                <div class="text-muted small">Foto Galeri</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card shadow-sm text-center py-3 h-100">
                <div style="font-size:32px">👥</div>
                <div class="fs-3 fw-bold text-info"><?= $total_alumni ?></div>
                <div class="text-muted small">Total Alumni</div>
            </div>
        </div>
    </div>

    <!-- Event & Berita -->
    <div class="row g-3">
        <!-- Event Mendatang -->
        <div class="col-md-6">
            <div class="card section-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i>Event Mendatang</span>
                    <a href="event.php" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    <?php if ($event_list->num_rows > 0):
                        while ($e = $event_list->fetch_assoc()): ?>
                    <div class="event-item bg-light rounded p-3 mb-2">
                        <div class="fw-semibold"><?= htmlspecialchars($e['judul']) ?></div>
                        <div class="text-muted small mt-1">
                            📍 <?= htmlspecialchars($e['lokasi']) ?><br>
                            🗓️ <?= tglIndo($e['tanggal']) ?> &nbsp; ⏰ <?= substr($e['waktu'],0,5) ?> WIB
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="text-center text-muted py-3">Belum ada event mendatang.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Berita Terbaru -->
        <div class="col-md-6">
            <div class="card section-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-newspaper text-success me-2"></i>Berita Terbaru</span>
                    <a href="berita.php" class="btn btn-outline-success btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body p-3">
                    <?php if ($berita_list->num_rows > 0):
                        $warna = ['Pengumuman'=>'primary','Berita'=>'success','Prestasi'=>'warning','Lainnya'=>'secondary'];
                        while ($b = $berita_list->fetch_assoc()): ?>
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="badge bg-<?= $warna[$b['kategori']] ?> badge-kategori mb-1"><?= $b['kategori'] ?></span>
                        <div class="fw-semibold small"><?= htmlspecialchars($b['judul']) ?></div>
                        <div class="text-muted" style="font-size:12px">📅 <?= tglIndo($b['tanggal']) ?></div>
                    </div>
                    <?php endwhile; else: ?>
                    <div class="text-center text-muted py-3">Belum ada berita.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
