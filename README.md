# web-alumini2
# 🎓 Sistem Informasi Layanan Alumni SMK — v2
> Fokus pada **komunitas & informasi alumni**, bukan job board!

---

## 📁 Struktur Folder

```
alumni_smk/
├── index.php                    ← Login
├── logout.php                   ← Logout
├── database.sql                 ← Script database
│
├── includes/
│   ├── koneksi.php              ← Koneksi MySQL
│   ├── fungsi.php               ← Fungsi pembantu
│   └── navbar.php               ← Navigasi
│
├── alumni/                      ← Halaman untuk alumni
│   ├── dashboard.php            ← Beranda alumni
│   ├── event.php                ← Event & reuni
│   ├── berita.php               ← Berita & pengumuman
│   ├── galeri.php               ← Galeri foto angkatan
│   ├── karir.php                ← Info karir (tips, beasiswa, dll)
│   └── profil.php               ← Edit profil
│
└── admin/                       ← Halaman untuk admin
    ├── dashboard.php            ← Panel admin
    ├── kelola_alumni.php        ← CRUD + cari alumni
    ├── tambah_alumni.php        ← Tambah alumni
    ├── edit_alumni.php          ← Edit alumni
    ├── kelola_event.php         ← CRUD event & reuni
    ├── kelola_berita.php        ← CRUD berita & pengumuman
    ├── kelola_galeri.php        ← CRUD galeri foto
    └── kelola_karir.php         ← CRUD info karir
```

---

## ⚙️ Cara Instalasi di XAMPP

1. Copy folder `alumni_smk` ke `C:\xampp\htdocs\`
2. Buka **phpMyAdmin** → tab **SQL** → paste isi `database.sql` → klik **Go**
3. Akses: **http://localhost/alumni_smk**

---

## 🔑 Akun Login Default

| Role  | Email              | Password   |
|-------|--------------------|------------|
| Admin | admin@smk.sch.id   | `password` |
| Alumni| budi@email.com     | `password` |

---

## ✨ Fitur Lengkap v2

### Untuk Alumni
| Fitur | Keterangan |
|-------|-----------|
| 📅 Event & Reuni | Lihat event mendatang & yang sudah lewat |
| 📰 Berita & Pengumuman | Info dari sekolah (prestasi, pengumuman, dll) |
| 🖼️ Galeri Foto | Foto kenangan per angkatan |
| 💡 Info Karir | Tips karir, sertifikasi, beasiswa, magang |
| 👤 Edit Profil | Update data diri & password |

### Untuk Admin
| Fitur | Keterangan |
|-------|-----------|
| 👥 Kelola Alumni | CRUD + pencarian nama & tahun lulus |
| 📅 Kelola Event | Tambah/edit/hapus event |
| 📰 Kelola Berita | Tambah/edit/hapus berita & pengumuman |
| 🖼️ Kelola Galeri | **Upload foto langsung ATAU pakai URL** |
| 💡 Kelola Info Karir | Tambah/edit/hapus info karir |

---

## 📸 Upload Foto Galeri

Admin bisa upload foto dengan **2 cara**:

1. **Upload dari komputer** — Pilih file JPG/PNG/GIF/WEBP (max 5MB)
2. **Pakai URL** — Paste link dari Google Drive, Imgur, dll

Prioritas: Upload > URL (kalau keduanya diisi, yang dipakai adalah upload)

Folder upload: `uploads/galeri/` (sudah dilindungi .htaccess)

---

## 🔐 Keamanan
- Prepared Statement (anti SQL Injection)
- `password_hash()` (password terenkripsi)
- `htmlspecialchars()` (anti XSS)
- Session + Role Check (admin/user)

---

*Sistem Alumni SMK v2 — Komunitas & Informasi Alumni 🎓*
