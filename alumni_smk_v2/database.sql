-- ================================================
-- DATABASE: db_alumni_smk
-- Sistem Informasi Layanan Alumni SMK — v2
-- ================================================

CREATE DATABASE IF NOT EXISTS db_alumni_smk;
USE db_alumni_smk;

-- ------------------------------------------------
-- Tabel: alumni
-- ------------------------------------------------
CREATE TABLE alumni (
    id_alumni   INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    tahun_lulus YEAR NOT NULL,
    jurusan     VARCHAR(100) NOT NULL,
    pekerjaan   VARCHAR(100) DEFAULT '-',
    role        ENUM('admin','user') DEFAULT 'user',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------
-- Tabel: event (reuni, gathering, dll)
-- ------------------------------------------------
CREATE TABLE event (
    id_event    INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(200) NOT NULL,
    deskripsi   TEXT NOT NULL,
    lokasi      VARCHAR(200) NOT NULL,
    tanggal     DATE NOT NULL,
    waktu       TIME NOT NULL,
    poster      VARCHAR(255) DEFAULT NULL,
    id_alumni   INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alumni) REFERENCES alumni(id_alumni) ON DELETE SET NULL
);

-- ------------------------------------------------
-- Tabel: berita (pengumuman sekolah)
-- ------------------------------------------------
CREATE TABLE berita (
    id_berita   INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(200) NOT NULL,
    isi         TEXT NOT NULL,
    kategori    ENUM('Pengumuman','Berita','Prestasi','Lainnya') DEFAULT 'Berita',
    tanggal     DATE NOT NULL,
    id_alumni   INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alumni) REFERENCES alumni(id_alumni) ON DELETE SET NULL
);

-- ------------------------------------------------
-- Tabel: galeri (foto angkatan)
-- ------------------------------------------------
CREATE TABLE galeri (
    id_galeri   INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(200) NOT NULL,
    keterangan  VARCHAR(300) DEFAULT '',
    angkatan    YEAR NOT NULL,
    foto_url    VARCHAR(500) NOT NULL,
    id_alumni   INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alumni) REFERENCES alumni(id_alumni) ON DELETE SET NULL
);

-- ------------------------------------------------
-- Tabel: info_karir (ringan, bukan job board)
-- ------------------------------------------------
CREATE TABLE info_karir (
    id_karir    INT AUTO_INCREMENT PRIMARY KEY,
    judul       VARCHAR(200) NOT NULL,
    isi         TEXT NOT NULL,
    kategori    ENUM('Tips Karir','Sertifikasi','Beasiswa','Magang','Lainnya') DEFAULT 'Tips Karir',
    tanggal     DATE NOT NULL,
    id_alumni   INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alumni) REFERENCES alumni(id_alumni) ON DELETE SET NULL
);

-- ================================================
-- Data Awal
-- ================================================

-- Akun Admin (password: password)
INSERT INTO alumni (nama, email, password, tahun_lulus, jurusan, pekerjaan, role) VALUES
('Administrator', 'admin@smk.sch.id',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 2020, 'Teknik Komputer Jaringan', 'Admin Sekolah', 'admin');

-- Akun Alumni Contoh (password: password)
INSERT INTO alumni (nama, email, password, tahun_lulus, jurusan, pekerjaan, role) VALUES
('Budi Santoso', 'budi@email.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 2022, 'Rekayasa Perangkat Lunak', 'Web Developer', 'user'),
('Siti Rahayu', 'siti@email.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 2021, 'Multimedia', 'Desainer Grafis', 'user');

-- Contoh Event
INSERT INTO event (judul, deskripsi, lokasi, tanggal, waktu, id_alumni) VALUES
('Reuni Akbar Angkatan 2022',
 'Reuni tahunan alumni angkatan 2022. Acara meliputi makan malam bersama, games, dan sharing pengalaman karir. Dresscode: Kemeja Putih.',
 'Aula SMK Negeri 1, Jl. Pendidikan No. 10',
 DATE_ADD(CURDATE(), INTERVAL 30 DAY),
 '18:00:00', 1),
('Gathering Alumni TKJ & RPL',
 'Gathering santai khusus alumni jurusan TKJ dan RPL. Ada demo project, diskusi teknologi terkini, dan networking session.',
 'Cafe Teknologi, Jl. Sudirman No. 5',
 DATE_ADD(CURDATE(), INTERVAL 14 DAY),
 '14:00:00', 1);

-- Contoh Berita
INSERT INTO berita (judul, isi, kategori, tanggal, id_alumni) VALUES
('Selamat! SMK Meraih Juara 1 LKS Provinsi',
 'Dengan bangga kami umumkan bahwa siswa SMK kita berhasil meraih Juara 1 di ajang Lomba Kompetensi Siswa (LKS) tingkat provinsi bidang Web Technology. Keberhasilan ini merupakan buah dari kerja keras dan dedikasi siswa serta guru pembimbing.',
 'Prestasi', CURDATE(), 1),
('Pendaftaran Beasiswa Alumni Berprestasi Dibuka',
 'Program beasiswa khusus alumni berprestasi kembali dibuka. Alumni yang lulus tahun 2020-2023 dan sedang menempuh pendidikan D3/S1 dapat mendaftar melalui tata usaha sekolah. Kuota terbatas!',
 'Pengumuman', CURDATE(), 1);

-- Contoh Galeri
INSERT INTO galeri (judul, keterangan, angkatan, foto_url, id_alumni) VALUES
('Foto Wisuda Angkatan 2022',
 'Momen wisuda angkatan 2022 yang penuh kebahagiaan',
 2022, 'https://picsum.photos/seed/wisuda2022/800/600', 1),
('Perpisahan Angkatan 2021',
 'Kenangan indah hari perpisahan angkatan 2021',
 2021, 'https://picsum.photos/seed/perpisahan2021/800/600', 1),
('Prakerin Angkatan 2022',
 'Dokumentasi kegiatan praktek kerja industri',
 2022, 'https://picsum.photos/seed/prakerin22/800/600', 1);

-- Contoh Info Karir
INSERT INTO info_karir (judul, isi, kategori, tanggal, id_alumni) VALUES
('5 Sertifikasi IT yang Wajib Dimiliki Fresh Graduate',
 'Sebagai lulusan SMK jurusan TKJ atau RPL, memiliki sertifikasi dapat meningkatkan nilai jual kamu di dunia kerja. Berikut 5 sertifikasi yang paling dicari: 1) CompTIA A+, 2) Cisco CCNA, 3) AWS Cloud Practitioner, 4) Google Associate Android Developer, 5) Microsoft Azure Fundamentals.',
 'Sertifikasi', CURDATE(), 1),
('Tips Mempersiapkan CV Untuk Fresh Graduate SMK',
 'Belum punya pengalaman kerja? Jangan khawatir! Kamu bisa menonjolkan: pengalaman prakerin, proyek saat sekolah, organisasi yang diikuti, dan keterampilan teknis yang dikuasai. Pastikan CV kamu tidak lebih dari 1 halaman dan gunakan format yang bersih.',
 'Tips Karir', CURDATE(), 1);

-- ================================================
-- LOGIN DEFAULT:
-- Admin  : admin@smk.sch.id | password: password
-- Alumni : budi@email.com   | password: password
-- ================================================
