# Student Portal — Dashboard Mahasiswa, Admin Panel & Login

Website sederhana untuk menampilkan data mahasiswa (nama, NIM, mata kuliah
yang telah diselesaikan, dan nilai), dilengkapi panel admin untuk mengelola
data tersebut. Dibangun dengan **HTML + CSS** untuk tampilan dan **PHP**
sebagai penghubung ke database **MariaDB** (PHP diperlukan karena HTML/CSS
murni tidak bisa berkomunikasi langsung dengan database).

## Struktur Folder

```
student-portal/
├── database/
│   └── schema.sql        # skema tabel + seed akun admin & contoh mata kuliah
├── includes/
│   ├── db.php             # koneksi PDO ke MariaDB
│   └── auth.php           # session, login guard, helper
├── css/
│   └── style.css          # 1 stylesheet dipakai semua halaman
├── login.php               # halaman login
├── logout.php
├── index.php               # redirect ke login.php
├── admin/
│   ├── dashboard.php       # daftar semua mahasiswa
│   ├── add_student.php     # tambah mahasiswa + buat akun login mahasiswa
│   ├── add_course.php      # tambah data master mata kuliah
│   └── student_detail.php  # catat/hapus nilai mata kuliah per mahasiswa
└── student/
    └── dashboard.php       # mahasiswa melihat data & nilainya sendiri
```

## Cara Menjalankan

### 1. Siapkan MariaDB
Pastikan MariaDB sudah terinstal dan berjalan (via XAMPP, Laragon, Docker,
atau instalasi native). Lalu import skema:

```bash
mysql -u root -p < database/schema.sql
```

Ini akan membuat database `student_portal` beserta:
- Tabel `mahasiswa`, `users`, `mata_kuliah`, `nilai`
- Akun admin default: **username `admin`**, **password `admin123`**
- 5 contoh mata kuliah (boleh dihapus/diedit lewat panel admin)

### 2. Atur koneksi database
Edit `includes/db.php`, sesuaikan `$DB_HOST`, `$DB_USER`, `$DB_PASS` dengan
konfigurasi MariaDB Anda.

### 3. Jalankan server PHP
Untuk uji coba lokal cepat (tanpa Apache/Nginx):

```bash
php -S localhost:8000
```

Lalu buka `http://localhost:8000/login.php` di browser.

Atau letakkan folder ini di `htdocs` (XAMPP) / `www` (Laragon) jika memakai
Apache, lalu akses lewat `http://localhost/student-portal/login.php`.

### 4. Login
- **Admin**: `admin` / `admin123` (segera ganti password setelah login
  pertama — saat ini belum ada halaman ganti password, bisa ditambahkan
  lewat panel admin atau langsung update kolom `password` di tabel `users`
  dengan hash baru dari `password_hash()`).
- **Mahasiswa**: dibuat oleh admin lewat menu "Tambah Mahasiswa" di admin
  panel — setiap mahasiswa baru otomatis mendapat username & password yang
  diinput admin.

## Alur Penggunaan

1. **Admin login** → masuk ke Admin Panel.
2. **Tambah Mata Kuliah** (sekali di awal, atau kapan pun perlu) — ini data
   master mata kuliah yang tersedia untuk dipilih saat mencatat nilai.
3. **Tambah Mahasiswa** — admin mengisi NIM, nama, program studi, sekaligus
   username & password untuk akun login mahasiswa tersebut.
4. Dari **Daftar Mahasiswa**, klik "Kelola nilai" pada mahasiswa tertentu
   untuk mencatat mata kuliah yang telah diselesaikan beserta nilainya
   (per semester). Bisa juga menghapus entri yang salah.
5. **Mahasiswa login** dengan akun yang dibuatkan admin → melihat dashboard
   pribadi: NIM, jumlah mata kuliah selesai, total SKS, IPK (dihitung
   otomatis), dan tabel riwayat nilai.

## Keamanan yang Sudah Diterapkan
- Password disimpan sebagai **hash bcrypt** (`password_hash` / `password_verify`),
  bukan plain text.
- Semua query database memakai **prepared statement** (PDO), aman dari SQL
  injection.
- Session-based login dengan pemisahan akses admin vs mahasiswa
  (`require_admin()` / `require_mahasiswa()`).
- Output ke HTML di-escape lewat fungsi `e()` (mencegah XSS).

## Pengembangan Lanjutan (opsional, belum termasuk)
- Halaman ganti password untuk admin & mahasiswa.
- Edit/hapus data mahasiswa dan mata kuliah.
- Export data ke Excel/PDF.
- Validasi format NIM, konfirmasi email, dsb.
