# Samudera Fresh Strawberry

Sistem Informasi Penjualan Berbasis Web untuk produk UMKM stroberi Ciwidey.

Repository GitHub: https://github.com/marsyamifta1112-lab/Samudera-fresh-strawberry-

## 1. Deskripsi Aplikasi

Samudera Fresh Strawberry adalah aplikasi berbasis web yang dikembangkan untuk mendukung proses penjualan produk stroberi asal Ciwidey secara lebih terstruktur dan efisien. Aplikasi ini mencakup proses dari katalog produk, keranjang belanja, pemesanan, pengaturan ongkos kirim, pembayaran, pengiriman, hingga pengelolaan data oleh Admin dan Kurir.

Berdasarkan analisis terhadap source code, aplikasi ini dibangun dengan Laravel 12 dan menggunakan pendekatan full-stack MVC dengan Blade template serta asset frontend dari Vite dan Tailwind CSS. Fokus bisnis yang terlihat pada proyek ini adalah penjualan produk stroberi dengan alur operasional yang mencakup pelanggan, admin, dan kurir.

## 2. Latar Belakang Pengembangan

Aplikasi ini dikembangkan sebagai solusi sistem informasi penjualan berbasis web untuk UMKM stroberi Ciwidey. Tujuan utamanya adalah mempermudah proses transaksi produk, mengelola stok, menyesuaikan pengiriman berdasarkan wilayah, serta memberikan pengalaman yang lebih rapi bagi pelanggan dan petugas operasional.

Pada source code, terlihat bahwa aplikasi memiliki fitur yang cukup lengkap untuk bisnis e-commerce skala lokal, termasuk:

- katalog produk dan pencarian
- keranjang belanja
- checkout dengan hitung ongkir wilayah
- pembayaran dengan unggah bukti transfer
- penjadwalan dan monitoring pengiriman
- review pembayaran dan laporan penjualan
- pengelolaan akun Admin dan Kurir
- mekanisme pemulihan akun dan reset password

## 3. Tujuan Pengembangan

Tujuan dari aplikasi ini adalah untuk:

- mempermudah pelanggan dalam mencari, memilih, dan membeli produk stroberi
- menyediakan sistem transaksi yang terdokumentasi dengan jelas
- memudahkan Admin dalam manajemen produk, pesanan, pembayaran, dan laporan
- membantu Kurir dalam mengelola pengiriman dan status pengiriman
- meningkatkan profesionalitas operasional UMKM melalui sistem berbasis web

## 4. Fitur Utama Aplikasi

Berikut fitur utama yang benar-benar terdeteksi di source code:

- katalog produk dengan filter pencarian, kategori, harga, dan berat
- keranjang belanja dengan penambahan, pembaruan, dan penghapusan item
- proses checkout dan perhitungan ongkir berdasarkan wilayah pengiriman
- pengelolaan pesanan dan detail produk per transaksi
- pembayaran dengan pemilihan metode bank dan unggah bukti transfer
- verifikasi pembayaran oleh Admin
- penugasan kurir untuk pengiriman
- pembaruan status pengiriman dari menunggu sampai diterima
- halaman laporan penjualan dan ekspor laporan Excel
- pengelolaan produk, stok, dan harga oleh Admin
- mekanisme email verifikasi pelanggan
- kode pemulihan akun pelanggan untuk proses reset password
- mekanisme reset password Admin dan verifikasi oleh Admin lain
- fitur return dan pengembalian dana untuk pesanan tertentu
- dashboard untuk masing-masing aktor

## 5. Penjelasan Fitur per Aktor

### 5.1 Pelanggan

Fitur pelanggan yang terdeteksi dari controller dan route adalah:

- registrasi akun pelanggan
- login menggunakan username atau email
- verifikasi email pelanggan melalui tautan yang dikirimkan
- melihat katalog produk dan detail produk
- menambah produk ke keranjang belanja
- melakukan checkout dengan data penerima dan alamat
- melihat riwayat pesanan dan detail pesanan
- memilih metode pembayaran dan mengunggah bukti pembayaran
- melihat halaman pembayaran dan struk
- mengelola profil akun
- mengeluarkan kode pemulihan akun untuk proses lupa password
- menggunakan mekanisme reset password berbasis kode pemulihan
- mengajukan return/claim produk tertentu jika memenuhi syarat

### 5.2 Admin

Fitur admin yang terdeteksi:

- dashboard admin dengan ringkasan data penjualan dan pesanan terbaru
- manajemen produk (tambah, edit, hapus, lihat daftar, stok, harga)
- pengelolaan inventaris produk
- melihat daftar pesanan dan detail pesanan
- menata tarif ongkos kirim per wilayah
- meninjau bukti pembayaran dan memverifikasi atau menolak pembayaran
- menugaskan kurir ke pesanan
- memperbarui status pengiriman
- melihat laporan penjualan dan mengekspor laporan dalam format Excel
- mengelola data petugas dan approval akun kurir
- mengelola permintaan reset password untuk akun Admin dan Kurir
- mengelola return dan status pengembalian dana
- mengelola profil admin

### 5.3 Kurir

Fitur kurir yang terdeteksi:

- registrasi akun kurir yang menunggu persetujuan Admin
- login dengan mekanisme yang memaksa mengganti password sementara jika perlu
- dashboard informasi pengiriman
- melihat daftar pengiriman dan detail pengiriman
- memperbarui status pengiriman (`menunggu`, `diproses`, `dikirim`, `diterima`)
- konfirmasi penerimaan pesanan
- profil kurir dan pembaruan data diri
- pengelolaan password dan reset password yang diajukan

## 6. Teknologi dan Tools yang Digunakan

Berdasarkan file proyek yang ada, teknologi yang digunakan adalah:

- PHP 8.2
- Laravel Framework 12
- Composer
- Node.js + npm
- Vite
- Tailwind CSS
- Blade Template Engine
- MySQL / MariaDB (default driver MySQL pada konfigurasi Laravel)
- SQLite dapat dipilih sebagai alternatif konfigurasi pada Laravel
- PHP Unit untuk pengujian
- File system lokal untuk penyimpanan bukti pembayaran dan return

Beberapa komponen tambahan yang terlihat dalam source code:

- `app/Support/ShippingZoneResolver.php` untuk logika wilayah dan tarif ongkir
- `app/Support/RecoveryCode.php` untuk kode pemulihan akun pelanggan
- `app/Support/OrderMetadata.php` untuk menyimpan metadata return dalam kolom catatan pesanan
- `app/Support/CustomerEmailVerificationLink.php` untuk verifikasi email pelanggan

## 7. Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat Anda memenuhi:

- PHP minimal versi 8.2
- Composer
- Node.js dan npm
- Web server lokal (misalnya Laravel built-in server atau server web lain)
- Database MySQL/MariaDB atau SQLite
- Ekstensi PHP yang umum untuk Laravel, seperti:
  - `pdo`
  - `pdo_mysql`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `json`
  - `fileinfo`
  - `gd` atau `imagick` untuk upload gambar

## 8. Cara Instalasi Proyek dari Awal

Ikuti langkah berikut:

1. Clone repository dari GitHub:

```bash
git clone https://github.com/marsyamifta1112-lab/Samudera-fresh-strawberry-
cd Samudera-fresh-strawberry-
```

2. Instal dependency PHP:

```bash
composer install
```

3. Instal dependency frontend:

```bash
npm install
```

4. Buat file konfigurasi environment:

Karena proyek ini tidak menampilkan file `.env.example` secara eksplisit di struktur direktori, biasanya Anda membuat file `.env` secara manual sesuai contoh yang diberikan di bagian berikut.

5. Generate key aplikasi:

```bash
php artisan key:generate
```

6. Konfigurasi database pada `.env` sesuai database lokal Anda.

7. Jalankan migrasi database:

```bash
php artisan migrate
```

8. Jika Anda ingin membangun asset frontend produksi:

```bash
npm run build
```

9. Jalankan aplikasi:

```bash
php artisan serve
```

> Catatan: Di file `composer.json`, terdapat script `composer run setup` yang secara umum dipakai untuk menginstal dependency, membuat `.env`, menjalankan `key:generate`, migrasi, dan build frontend. Namun karena file `.env.example` tidak terlihat pada repositori saat ini, proses manual pembuatan `.env` tetap diperlukan agar langkah tersebut berjalan dengan benar.

## 9. Konfigurasi File `.env`

Berikut contoh konfigurasi `.env` yang sesuai dengan kebutuhan aplikasi. Sesuaikan nilai sesuai environment Anda dan jangan menyimpan credential asli pada repositori publik.

```env
APP_NAME="Samudera Fresh Strawberry"
APP_ENV=local
APP_KEY=base64:generated_key_here
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=samudera_fresh_strawberry
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@localhost"
MAIL_FROM_NAME="Samudera Fresh Strawberry"

APP_TIMEZONE=Asia/Jakarta
```

Catatan penting:

- Nilai `APP_KEY` harus dihasilkan dengan perintah `php artisan key:generate`.
- Jika Anda menggunakan database lain, sesuaikan `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
- Fitur verifikasi email dan pemulihan akun membutuhkan konfigurasi email yang benar agar tautan dapat dikirim.

## 10. Cara Konfigurasi dan Menjalankan Database

Aplikasi ini menggunakan database relasional dengan tabel utama yang terbentuk melalui migration Laravel. Pada source code, migration yang terlihat adalah:

- `produk`
- `pelanggan`
- `petugas`
- `pesanan`
- `detail_pesanan`
- `pembayaran`
- `pengiriman`
- `ongkir_wilayah`
- `admin_password_recovery_requests`
- `returns` / metadata return yang disimpan pada kolom `catatan` pesanan

Langkah konfigurasi database:

1. Buat database baru di MySQL/MariaDB.
2. Atur nilai `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.
3. Jalankan migrasi:

```bash
php artisan migrate
```

4. Jika Anda menjalankan aplikasi untuk pertama kali, periksa apakah kolom dan relasi yang diharapkan sudah terbentuk dengan benar.
5. Data wilayah ongkir dan tarif awal akan ditambahkan melalui migrasi tertentu, lalu dapat diubah melalui menu Admin.

> Informasi penting: source code tampaknya mengandung serangkaian migration yang menambahkan kolom ke tabel yang sudah ada. Oleh karena itu, sebaiknya migrasi dijalankan dalam urutan yang benar sesuai Laravel dan tidak dibatalkan secara asal.

## 11. Cara Menjalankan Aplikasi Secara Lokal

Untuk menjalankan aplikasi tanpa build produksi, lakukan:

```bash
php artisan serve
```

Akses aplikasi di browser:

```text
http://localhost:8000
```

Jika Anda ingin menjalankan semua kebutuhan pengembangan sekaligus (server Laravel, queue, dan frontend Vite), gunakan:

```bash
composer run dev
```

Untuk build frontend produksi:

```bash
npm run build
```

## 12. Struktur Folder dan Penjelasan

```text
samudera_fresh_strawberry/
├─ app/
│  ├─ Console/
│  │  └─ Commands/
│  ├─ Http/
│  │  ├─ Controllers/
│  │  │  ├─ AuthController.php
│  │  │  ├─ AdminController.php
│  │  │  ├─ KurirController.php
│  │  │  ├─ PelangganController.php
│  │  │  ├─ ProdukController.php
│  │  │  ├─ CheckoutController.php
│  │  │  ├─ PembayaranController.php
│  │  │  ├─ ReturnController.php
│  │  │  └─ ...
│  │  └─ Middleware/
│  │     └─ RoleMiddleware.php
│  ├─ Mail/
│  │  ├─ CustomerEmailVerification.php
│  │  └─ CustomerRecoveryCode.php
│  ├─ Models/
│  │  ├─ Produk.php
│  │  ├─ Pelanggan.php
│  │  ├─ Petugas.php
│  │  ├─ Pesanan.php
│  │  ├─ Pembayaran.php
│  │  ├─ Pengiriman.php
│  │  └─ ...
│  └─ Support/
│     ├─ CustomerEmailVerificationLink.php
│     ├─ OrderMetadata.php
│     ├─ RecoveryCode.php
│     └─ ShippingZoneResolver.php
├─ bootstrap/
├─ config/
│  ├─ app.php
│  ├─ auth.php
│  ├─ database.php
│  ├─ mail.php
│  └─ ...
├─ database/
│  ├─ factories/
│  ├─ migrations/
│  └─ seeders/
├─ docs/
│  ├─ Manual-Book-Admin-Samudera-Fresh-Strawberry.md
│  ├─ Manual-Book-Kurir-Samudera-Fresh-Strawberry.md
│  ├─ Manual-Book-Pelanggan-Samudera-Fresh-Strawberry.md
│  └─ Manual-Book-Samudera-Fresh-Strawberry.md
├─ public/
│  ├─ images/
│  └─ index.php
├─ resources/
│  ├─ css/
│  ├─ js/
│  └─ views/
│     ├─ admin/
│     ├─ kurir/
│     ├─ pelanggan/
│     ├─ passwords/
│     ├─ produk.blade.php
│     ├─ checkout.blade.php
│     ├─ pembayaran.blade.php
│     └─ ...
├─ routes/
│  ├─ api.php
│  ├─ web.php
│  └─ console.php
├─ storage/
├─ tests/
├─ .env
├─ .gitignore
├─ artisan
├─ composer.json
├─ package.json
├─ phpunit.xml
├─ Procfile
├─ vite.config.js
├─ README.md
└─ ...
```

Penjelasan singkat:

- `app/` berisi logika aplikasi, controller, model, dan support class.
- `routes/` berisi definisi rute web dan API.
- `resources/views/` berisi tampilan Blade untuk pelanggan, admin, kurir, login, checkout, dan laporan.
- `database/migrations/` berisi skema database dan perubahan struktur tabel.
- `public/` berisi asset publik dan gambar aplikasi.
- `docs/` berisi manual book yang dapat digunakan untuk dokumentasi pengguna.
- `config/` berisi konfigurasi Laravel seperti database, mail, session, dan auth.

## 13. Penjelasan Database dan Tabel Utama

Berikut tabel utama yang terdeteksi dari migration dan model:

### `produk`
Menyimpan data produk stroberi, seperti:

- `id_produk`
- `nama_produk`
- `deskripsi`
- `harga_produk`
- `stok_produk`
- `berat_gram`
- `foto_produk`

### `pelanggan`
Menyimpan data pelanggan:

- `id_pelanggan`
- `nama_pelanggan`
- `email`
- `username`
- `password`
- `email_verified_at`
- `recovery_code_hash`
- `recovery_code_expires_at`
- `recovery_code_attempts`
- `recovery_code_locked_until`

### `petugas`
Menyimpan data karyawan dan kurir:

- `id_petugas`
- `nama_petugas`
- `username`
- `password`
- `no_telp`
- `alamat`
- `role` (`Admin` atau `Kurir`)
- `status_akun`
- `must_change_password`
- `password_reset_status`

### `pesanan`
Menyimpan data transaksi utama:

- `id_pesanan`
- `id_pelanggan`
- `tanggal_pemesanan`
- `total_harga`
- `ongkir`
- `wilayah_ongkir`
- `status_pesanan`
- `nama_penerima`
- `alamat_pengiriman`
- `no_hp`
- `catatan`

### `detail_pesanan`
Menyimpan rincian produk per pesanan:

- `id_detail`
- `id_pesanan`
- `id_produk`
- `jumlah`
- `harga`
- `subtotal`

### `pembayaran`
Menyimpan data pembayaran pesanan:

- `id_pembayaran`
- `id_pesanan`
- `metode_pembayaran`
- `jumlah_bayar`
- `status_pembayaran`
- `tanggal_pembayaran`
- `bukti_pembayaran`
- `nomor_struk`
- `tanggal_verifikasi`

### `pengiriman`
Menyimpan data pengiriman:

- `id_pengiriman`
- `id_pesanan`
- `id_petugas`
- `nama_penerima`
- `no_telp_penerima`
- `alamat_pengiriman`
- `status_pengiriman`
- `tanggal_pengiriman`

### `ongkir_wilayah`
Menyimpan tarif ongkir per wilayah, seperti:

- Ciwidey & Rancabali
- Pasirjambu & Soreang
- Bandung Raya
- Luar Bandung Raya

### `admin_password_recovery_requests`
Digunakan untuk model penyetujan reset password akun Admin oleh Admin lain.

### Metadata return
Pada source code, mekanisme return tidak disimpan dalam tabel terpisah secara penuh; metadata return dikelola melalui kolom `catatan` di tabel `pesanan` dengan struktur JSON. Fitur ini diatur oleh `app/Support/OrderMetadata.php`.

## 14. Informasi Deployment dan Hosting

File `Procfile` yang ada di root repository menunjukkan bahwa aplikasi ini didesain untuk dijalankan pada platform hosting yang menyediakan variabel `PORT`, seperti Railway, Heroku, atau lingkungan serupa.

Isi `Procfile` adalah:

```text
web: php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
```

Beberapa hal yang dapat diatur di lingkungan deployment:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sesuai domain hosting
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`
- `CACHE_STORE`
- `QUEUE_CONNECTION`
- `MAIL_*` untuk email verifikasi

> Informasi domain produksi dan detail hosting final belum tercantum secara eksplisit di source code. Oleh karena itu, URL produksi spesifik dan detail konfigurasi hosting perlu dikonfirmasi ke pengembang/pemilik proyek.

## 15. Tim Pengembang

Proyek ini dikembangkan oleh:

- Kelompok Twinbyte
- SMK Budi Bakti Ciwidey

> Informasi nama anggota tim individu dan detail kontak tim belum tercantum secara eksplisit di source code. Informasi tersebut perlu dikonfirmasi dari pembuat proyek jika ingin ditampilkan pada dokumentasi publik.

## 16. Penutup

Samudera Fresh Strawberry merupakan sistem informasi penjualan berbasis web yang dirancang untuk mendukung operasional UMKM stroberi Ciwidey secara lebih modern, terstruktur, dan efisien. Aplikasi ini mencakup seluruh alur bisnis utama, mulai dari katalog produk, pemesanan, pembayaran, pengiriman, hingga pengelolaan data oleh Admin dan Kurir.

Dokumentasi ini dibuat berdasarkan analisis source code yang tersedia di repositori. Semoga README ini dapat menjadi acuan resmi untuk kebutuhan penilaian Sprint Akhir, pengelolaan proyek, maupun publikasi di GitHub.

Terima kasih atas perhatian dan dukungannya.

---

© 2026 Samudera Fresh Strawberry — Kelompok Twinbyte, SMK Budi Bakti Ciwidey.

