# 📝 Changelog - FullCMS Premium Web Application

Semua perubahan dan perkembangan penting pada proyek **FullCMS** didokumentasikan di berkas ini.

---

## [1.3.0] - 2026-05-19

### Added

- **Integrasi Highlight.js & Modul Syntax Highlighting:**
  - Menambahkan pustaka `Highlight.js` (skrip JS dan tema CSS *Atom One Dark*) pada tata letak admin (`admin_header.php` dan `admin_footer.php`).
  - Mengaktifkan modul `syntax` pada inisialisasi editor QuillJS agar mengenali dan mempertahankan format tag `<pre>` dengan metadata bahasa pemrograman khusus (seperti `data-language="plain"`).
- **Restorasi Gaya Tampilan Konten Kaya (Rich Content Styling Engine):**
  - Menyusun skema CSS kustom lengkap untuk elemen `.ql-editor` di `layouts/header.php` dan `layouts/admin_header.php`.
  - Mengembalikan visual penanda daftar berurutan/tidak berurutan (bullet & numbered lists ul/ol) yang sebelumnya hilang akibat Tailwind CSS Preflight reset.
  - Menambahkan gaya premium bertema glassmorphism untuk kutipan `blockquote`, desain tajuk yang proporsional, tautan inline tebal, dan kartu tampilan kode `<pre>` dengan sudut melengkung.

### Fixed

- **Resolusi Rendering Konten di Editor Artikel (Quill Parsing Fixes):**
  - Mengosongkan kontainer `<div id="editor">` saat pemuatan awal di halaman `edit.php` untuk mencegah konflik double-escaping HTML dari teks ter-escape, serta mengalihkan pengisian konten ke metode `quill.root.innerHTML` secara dinamis.
  - Memperbaiki pengisian textarea input lama (`$old['content']`) pada `create.php` dengan tag PHP unescaped agar data lama tidak ter-escape ganda saat validasi form gagal.

### Enhanced

- **Optimalisasi Lebar Halaman Kerja Editor:**
  - Memperlebar pembungkus halaman kerja editor (`max-w-6xl` di halaman edit dan `max-w-8xl` pada admin layout) untuk kenyamanan antarmuka saat menulis konten yang panjang.
- **Pembersihan Navigasi Menu Utama Frontend:**
  - Mengeliminasi tautan navigasi berulang (Home, Articles, About) pada file `views/frontend/layouts/header.php` guna menciptakan antarmuka bar navigasi yang lebih bersih, minimalis, dan berfokus pada kategori dinamis.

---

## [1.2.0] - 2026-05-19

### Added

- **Struktur Folder Media Dinamis & Izin Akses Aman:**
  - Membuat direktori `/public/uploads/` beserta sub-direktori `/articles` dan `/avatars`.
  - Menambahkan berkas `.gitkeep` pada setiap folder agar direktori kosong tetap terdeteksi oleh Git.
  - Memberikan konfigurasi kepemilikan web-server (`chown www-data:www-data`) dan izin akses (`chmod 775`) untuk memastikan fitur unggah gambar berjalan lancar tanpa hambatan keamanan.
- **Berkas Dokumentasi Proyek:**
  - Membuat berkas `README.md` premium yang berisi panduan lengkap instalasi, arsitektur sistem, dan daftar kredensial pengujian.
  - Membuat berkas `CHANGELOG.md` (berkas ini) untuk mendokumentasikan riwayat pengembangan aplikasi secara berkala.

### Fixed

- **Pemulihan Berkas Core & Frontend yang Hilang:**
  - Memulihkan berkas basis pengontrol utama **`app/Core/Controller.php`** yang sebelumnya tidak sengaja terhapus.
  - Memulihkan berkas halaman depan utama **`views/frontend/home.php`** dan tata letak (`layouts/header.php`, `layouts/footer.php`).
  - Memulihkan halaman error HTTP terintegrasi (**`views/error/403.php`**, **`404.php`**, **`500.php`**).
- **Konfigurasi Git (.gitignore):**
  - Mengonfigurasi pengecualian unggahan berkas gambar dengan pola `public/uploads/*` namun tetap meloloskan berkas `.gitkeep` (`!public/uploads/.gitkeep`).

---

## [1.1.0] - 2026-05-18

### Added

- **Sistem Keamanan Berlapis (Hardened Security Suite):**
  - **Dynamic CSRF Protection:** Proteksi token CSRF otomatis pada setiap form POST, PUT, dan DELETE.
  - **Session Hardening Engine:** Perlindungan pembajakan sesi menggunakan pencocokan sidik jari IP Subnet & User-Agent, serta regenerasi ID sesi otomatis setiap 30 menit.
  - **Rate Limiting Engine:** Pencegahan serangan Brute Force yang membatasi percobaan masuk yang gagal (maksimal 5 kali) per kombinasi IP & Username dalam rentang 15 menit.
  - **Secure Media Validator:** Validasi berlapis pada unggahan gambar (verifikasi ekstensi file dan konten _MIME-type_ dinamis untuk mencegah serangan Remote Code Execution).
- **Sistem Log Audit Administratif (Audit Trail):**
  - Pencatatan otomatis setiap aksi sensitif (Tambah/Edit/Hapus data) ke dalam tabel database `audit_logs` lengkap dengan detail IP, User-Agent, dan penanda waktu.

### Enhanced

- **Penyelarasan Desain Form Masuk (Auth UI):**
  - Menyelaraskan tata letak tombol _Sign in with Google_ agar berada tepat di tengah (_flex justify-center_) dan memiliki lebar penuh (_w-full_) agar terlihat premium dan menyatu dengan input teks form.
- **Pemulihan Sesi Pengujian:**
  - Mengatur ulang dan menghapus riwayat blokir masuk pada tabel `login_attempts` agar pengembang/penguji dapat masuk kembali menggunakan akun-akun uji coba aktif.

---

## [1.0.0] - 2026-05-17

### Added

- **Arsitektur Framework Native PHP MVC:**
  - Membangun Router dinamis berbasis status HTTP status code.
  - Membangun PDO Database Wrapper aman dengan binding parameter otomatis untuk mencegah celah SQL Injection.
- **Tampilan Premium bertema Glassmorphism:**
  - Mengintegrasikan Tailwind CSS v4 dengan kurasi font premium (_Outfit_ untuk tajuk & _Plus Jakarta Sans_ untuk teks isi).
  - Mengintegrasikan pustaka animasi mikro Alpine.js dan transisi yang halus.
  - Halaman autentikasi dual-mode dinamis (Masuk/Daftar) dalam satu kartu interaktif tanpa _reload_ halaman.
- **Sistem Otorisasi Berbasis Peran (RBAC Engine):**
  - Mengimplementasikan sistem hak akses berdasarkan matriks otoritas yang didefinisikan dalam `config/permissions.json` untuk 4 peran utama: Administrator, Editor, Author, dan Subscriber.
- **Manajemen Konten (CRUD) Lengkap:**
  - Mengintegrasikan Quill Rich-Text Editor pada modul penulisan Artikel dan Halaman Statis.
  - Membangun dashboard moderasi komentar secara _real-time_ (Setujui, Tandai Spam, Hapus).
