# 🌟 FullCMS - Premium Custom PHP Native MVC Content Management System

**FullCMS** adalah sebuah Content Management System (CMS) premium, modern, dan berkinerja tinggi yang dibangun dari dasar menggunakan arsitektur **Native PHP MVC (Model-View-Controller)**. Dilengkapi dengan antarmuka pengguna (UI/UX) premium bertema _glassmorphic_, sistem keamanan tingkat tinggi (Hardened Security Suite), serta manajemen hak akses berbasis peran (RBAC) yang sangat fleksibel.

---

## 🚀 Fitur Utama

### 🎨 1. Desain Antarmuka Premium (UI/UX)

- **Aesthetics Glassmorphism:** Tampilan modern berbasis Tailwind CSS v4 dengan efek transparansi, _blur backdrop_, dan _vibrant gradients_.
- **Mikro-Animasi Responsif:** Animasi transisi yang halus berbasis Alpine.js untuk memanjakan mata pengguna.
- **Tipografi Elegan:** Integrasi font Google premium (_Outfit_ untuk tajuk utama & _Plus Jakarta Sans* untuk teks bacaan).
- **Dual Mode Form:** Halaman masuk/daftar dinamis dalam satu kartu otentikasi tanpa memuat ulang halaman (_Single Page Experience_).

### 🛡️ 2. Paket Keamanan Tingkat Tinggi (Hardened Security Suite)

- **Proteksi CSRF Dinamis:** Sistem _Token-Based CSRF_ otomatis pada setiap permintaan POST/PUT/DELETE untuk mencegah manipulasi sesi.
- **Session Hardening:** Perlindungan pembajakan sesi (_Session Hijacking Prevention_) berbasis kecocokan sidik jari IP Subnet dan User-Agent, serta regenerasi ID sesi otomatis setiap 30 menit.
- **Rate Limiting & Lockout Engine:** Pencegahan serangan _Brute Force_ dengan membatasi percobaan masuk yang gagal (maksimal 5 kali) per kombinasi IP & Username dalam rentang 15 menit.
- **Penyaringan XSS & Escape Data Global:** Penanganan output dinamis menggunakan fungsi pembungkus `e()` serta sanitasi input berlapis untuk mencegah injeksi skrip berbahaya.
- **Secure Media Upload Validator:** Unggah media aman yang memverifikasi ekstensi file sekaligus konten _MIME-type_ secara mendalam untuk mencegah serangan _RCE (Remote Code Execution)_.

### 👥 3. Sistem Otorisasi & Peran Dinamis (RBAC Engine)

- **Role Permission Matrix:** Pembagian tugas yang aman di dashboard backend menggunakan filter otorisasi:
  - **Administrator:** Kendali penuh sistem (Manajemen Pengguna, Moderasi Komentar, Publikasi Konten, dll).
  - **Editor:** Mengedit dan menyunting seluruh artikel/halaman di dalam sistem.
  - **Author:** Menulis, mengelola, dan mempublikasikan artikel buatan mereka sendiri.
  - **Subscriber:** Pembaca terdaftar yang dapat memberikan komentar di artikel publik.

### 📝 4. Manajemen Konten Komprehensif

- **Manajemen Artikel & Halaman Statis:** Sistem CRUD (_Create, Read, Update, Delete_) lengkap dengan integrasi editor visual **Quill Rich-Text Editor**.
- **Pengolahan Kategori & Tag:** Pengorganisasian artikel secara modular berbasis tag dinamis dan taksonomi kategori.
- **Moderasi Komentar Real-time:** Dashboard khusus bagi Admin/Editor untuk menyetujui (_Approve_), menandai sebagai spam (_Spam_), atau menghapus (_Delete_) komentar pembaca.
- **Audit Logging System:** Setiap aksi administratif dicatat secara detail di tabel `audit_logs` (IP, User-Agent, Aksi, Waktu) untuk transparansi keamanan.

---

## 📁 Struktur Proyek (MVC Architecture)

```text
fullcms/
├── app/
│   ├── Controllers/       # Pengolah logika bisnis dan kontrol alur aplikasi
│   │   ├── AdminController.php
│   │   ├── ArticleController.php
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── CommentController.php
│   │   ├── HomeController.php
│   │   ├── PageController.php
│   │   └── TagController.php
│   ├── Core/              # Komponen inti framework buatan sendiri
│   │   ├── Controller.php # Base Controller untuk rendering views & data binding
│   │   ├── Database.php   # PDO DB wrapper dengan SQL parameter binding aman
│   │   └── Router.php     # Dynamic HTTP Status-Based Router
│   ├── Helpers/           # Pustaka utilitas dan fungsionalitas pendukung
│   │   ├── Auth.php       # Validasi otorisasi & peran pengguna
│   │   ├── Security.php   # CSRF, Session, Upload, RateLimit, & Audit Log
│   │   └── Session.php    # Pembungkus dinamis session PHP
│   └── Models/            # Model data untuk transaksi database aman
│       ├── Article.php
│       ├── ArticleTag.php
│       ├── Category.php
│       ├── Comment.php
│       ├── Page.php
│       ├── Tag.php
│       └── User.php
├── config/                # Berkas konfigurasi sistem
│   ├── config.php         # Konfigurasi koneksi MySQL & Helper XSS
│   └── permissions.json   # Matriks perizinan hak akses (RBAC)
├── public/                # Direktori publik (Akses langsung via server)
│   ├── assets/            # CSS terkompilasi, pustaka JavaScript, & gambar
│   ├── uploads/           # Penyimpanan berkas unggahan gambar artikel secara aman
│   └── index.php          # Titik masuk utama aplikasi (Single Entry Point)
└── views/                 # File antarmuka / template tampilan (PHP & HTML)
    ├── backend/           # Panel dashboard admin & profil pengguna
    └── frontend/          # Tampilan halaman publik (Home, Read, Page)
```

---

## 🛠️ Instalasi & Konfigurasi

### 1. Prasyarat Sistem

- PHP >= 8.0 (Pastikan ekstensi `pdo_mysql` dan `fileinfo` aktif).
- MySQL Server / MariaDB.
- Web Server Apache dengan modul `mod_rewrite` aktif (atau Nginx dengan konfigurasi redirection).

### 2. Impor Database

1. Buat database baru di MySQL Anda dengan nama **`cms_db`**:
   ```sql
   CREATE DATABASE cms_db;
   ```
2. Impor skema dan data default dari SQL Dump yang tersedia (atau gunakan data default yang sudah ada di database lokal Anda).

### 3. Konfigurasi Sistem

Buka berkas **[config/config.php](file:///var/www/html/fullcms/config/config.php)** dan sesuaikan dengan kredensial server database lokal Anda:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Masukkan sandi MySQL Anda
define('DB_NAME', 'cms_db');
```

---

## 🔑 Kredensial Akun Pengujian (Testing Credentials)

Untuk memudahkan peninjauan fitur berdasarkan hak akses (_role-based authorization_), Anda dapat menggunakan akun-akun pengujian aktif berikut:

| No  | Username          | Email                   | Hak Akses (Role) | Kata Sandi (Password)   | Hak Istimewa (Privileges)                          |
| :-- | :---------------- | :---------------------- | :--------------- | :---------------------- | :------------------------------------------------- |
| 1   | **`admin`**       | `admin@cms.com`         | **`admin`**      | 🔑 **`Admin123!`**      | Kendali penuh, manajemen user, moderasi komentar   |
| 2   | **`citrasd`**     | `citrasd@cms.com`       | **`editor`**     | 🔑 **`Editor123!`**     | Menyunting seluruh konten artikel & halaman statis |
| 3   | **`Chandra`**     | `buchan@cms.com`        | **`author`**     | 🔑 **`Author123!`**     | Membuat & menerbitkan artikel buatan sendiri       |
| 4   | **`fandy`**       | `fandyp@cms.com`        | **`subscriber`** | 🔑 **`Subscriber123!`** | Mengomentari artikel di halaman depan              |
| 5   | **`aliefrahman`** | `aliefrahman@gmail.com` | **`subscriber`** | 🔑 **`Subscriber123!`** | Mengomentari artikel di halaman depan              |

---

## 🛡️ Standar Penulisan Kode Aman (Secure Coding Best Practices)

- - **Pencegahan SQL Injection:** Selalu gunakan `$this->db->bind()` di dalam model. Hindari penggabungan string mentah pada query SQL.
- - **Pencegahan XSS:** Saat mencetak variabel dinamis di views, selalu bungkus dengan fungsi pembungkus global `e()` atau `\App\Helpers\Security::escape()`.
- - **Validasi Peran Pengguna:** Di setiap aksi dashboard backend yang sensitif, panggil `\App\Helpers\Auth::requirePermission('nama_izin')` untuk memblokir akses tidak sah.
- - **Audit Trail:** Selalu catat operasi kritis (tambah/edit/hapus data penting) menggunakan `\App\Helpers\Security::logAudit('NAMA_AKSI', 'Rincian aktivitas')`.

---

_Dikembangkan dengan penuh dedikasi untuk menghadirkan pengalaman Content Management System modern berkelas dunia._ 🚀
