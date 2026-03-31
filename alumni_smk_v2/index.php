<?php
session_start();
require_once 'includes/koneksi.php';
require_once 'includes/fungsi.php';

if (isset($_SESSION['id_alumni'])) {
    redirect($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'alumni/dashboard.php');
}

$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = bersihkan($_POST['email']);
    $password = $_POST['password'];

    if (kosong($email) || kosong($password)) {
        $pesan = "Email dan password wajib diisi!";
        $tipe_pesan = 'danger';
    } else {
        $stmt = $koneksi->prepare("SELECT * FROM alumni WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $alumni = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($alumni && password_verify($password, $alumni['password'])) {
            $_SESSION['id_alumni'] = $alumni['id_alumni'];
            $_SESSION['nama']      = $alumni['nama'];
            $_SESSION['email']     = $alumni['email'];
            $_SESSION['role']      = $alumni['role'];
            redirect($alumni['role'] === 'admin' ? 'admin/dashboard.php' : 'alumni/dashboard.php');
        } else {
            $pesan = "Email atau password salah!";
            $tipe_pesan = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Alumni SMK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            display: flex; align-items: center;
        }
        .login-card { border: none; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.35); }
        .login-left {
            background: linear-gradient(160deg, #1a237e, #283593);
            padding: 50px 35px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }
        .btn-masuk {
            background: linear-gradient(135deg, #283593, #1a237e);
            border: none; border-radius: 10px; padding: 13px; font-weight: 600;
        }
        .btn-masuk:hover { opacity: 0.9; transform: translateY(-1px); transition: all .2s; }
        .form-control:focus { border-color: #3949ab; box-shadow: 0 0 0 .25rem rgba(57,73,171,.2); }
        .hint-box { background: #e8eaf6; border-left: 4px solid #3949ab; border-radius: 8px; padding: 12px 15px; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-10">
            <div class="card login-card">
                <div class="row g-0">
                    <!-- Sisi Kiri: Branding -->
                    <div class="col-md-5 login-left text-center text-white d-none d-md-flex">
                        <div>
                            <div style="font-size:80px;margin-bottom:20px;">🎓</div>
                            <h3 class="fw-bold">Sistem Alumni</h3>
                            <h4 class="fw-bold mb-3">SMK</h4>
                            <p class="opacity-75 small">Portal komunitas & informasi untuk alumni SMK. Tetap terhubung, tetap berkembang!</p>
                            <hr class="border-white opacity-25 my-4">
                            <div class="d-flex justify-content-center gap-4">
                                <div class="text-center">
                                    <div class="fs-4 fw-bold">📅</div>
                                    <div class="small opacity-75">Event</div>
                                </div>
                                <div class="text-center">
                                    <div class="fs-4 fw-bold">📰</div>
                                    <div class="small opacity-75">Berita</div>
                                </div>
                                <div class="text-center">
                                    <div class="fs-4 fw-bold">🖼️</div>
                                    <div class="small opacity-75">Galeri</div>
                                </div>
                                <div class="text-center">
                                    <div class="fs-4 fw-bold">💡</div>
                                    <div class="small opacity-75">Karir</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Sisi Kanan: Form Login -->
                    <div class="col-md-7 bg-white">
                        <div class="p-4 p-md-5">
                            <h5 class="fw-bold mb-1">Selamat Datang! 👋</h5>
                            <p class="text-muted small mb-4">Masuk ke akun alumni kamu</p>

                            <?php if ($pesan): tampilPesan($pesan, $tipe_pesan); endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-envelope text-muted"></i>
                                        </span>
                                        <input type="email" name="email" class="form-control border-start-0 ps-0"
                                            placeholder="email@kamu.com"
                                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold small">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock text-muted"></i>
                                        </span>
                                        <input type="password" name="password" id="pwdInput"
                                            class="form-control border-start-0 border-end-0 ps-0"
                                            placeholder="Password kamu" required>
                                        <button type="button" class="btn btn-light border" onclick="togglePwd()">
                                            <i class="bi bi-eye" id="pwdIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-masuk btn-primary w-100 text-white mb-3">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                </button>
                            </form>

                            <div class="hint-box mt-3">
                                <p class="fw-bold mb-1 text-primary">🔑 Akun Demo:</p>
                                <p class="mb-0"><b>Admin:</b> admin@smk.sch.id &nbsp;|&nbsp; <code>password</code></p>
                                <p class="mb-0"><b>Alumni:</b> budi@email.com &nbsp;|&nbsp; <code>password</code></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center text-white-50 mt-3 small">&copy; <?= date('Y') ?> Sistem Informasi Layanan Alumni SMK</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const i = document.getElementById('pwdInput');
    const ic = document.getElementById('pwdIcon');
    i.type = i.type === 'password' ? 'text' : 'password';
    ic.className = i.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
