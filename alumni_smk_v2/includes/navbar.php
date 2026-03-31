<?php
// includes/navbar.php
?>
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background:linear-gradient(135deg,#1a237e,#283593);">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold fs-5" href="#">
            🎓 Alumni SMK
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto gap-1">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="../admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/kelola_alumni.php"><i class="bi bi-people me-1"></i>Alumni</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-grid me-1"></i>Konten
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../admin/kelola_event.php">🗓️ Event & Reuni</a></li>
                            <li><a class="dropdown-item" href="../admin/kelola_berita.php">📰 Berita & Pengumuman</a></li>
                            <li><a class="dropdown-item" href="../admin/kelola_galeri.php">🖼️ Galeri Foto</a></li>
                            <li><a class="dropdown-item" href="../admin/kelola_karir.php">💡 Info Karir</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="../alumni/dashboard.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alumni/event.php"><i class="bi bi-calendar-event me-1"></i>Event</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alumni/berita.php"><i class="bi bi-newspaper me-1"></i>Berita</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alumni/galeri.php"><i class="bi bi-images me-1"></i>Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alumni/karir.php"><i class="bi bi-lightbulb me-1"></i>Info Karir</a></li>
                    <li class="nav-item"><a class="nav-link" href="../alumni/profil.php"><i class="bi bi-person me-1"></i>Profil</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <span class="rounded-circle bg-white d-inline-flex align-items-center justify-content-center"
                            style="width:30px;height:30px;font-size:14px;">👤</span>
                        <?= htmlspecialchars($_SESSION['nama']) ?>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <span class="badge bg-warning text-dark">Admin</span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($_SESSION['role'] !== 'admin'): ?>
                        <li><a class="dropdown-item" href="../alumni/profil.php"><i class="bi bi-person-gear me-2"></i>Edit Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-danger" href="../logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
