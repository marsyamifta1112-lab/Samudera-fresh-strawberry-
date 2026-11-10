# Samudera Fresh Strawberry

Sistem Informasi Penjualan Berbasis Web untuk produk UMKM stroberi Ciwidey.

Repository GitHub: https://github.com/marsyamifta1112-lab/Samudera-fresh-strawberry-

## 1. Judul Proyek

Samudera Fresh Strawberry

## 2. Deskripsi Aplikasi

Samudera Fresh Strawberry adalah sistem informasi penjualan berbasis web yang dirancang untuk mendukung operasional UMKM stroberi Ciwidey. Aplikasi ini mengelola seluruh proses bisnis dari katalog produk, keranjang belanja, checkout, pembayaran, pengiriman, hingga monitoring data penjualan oleh Admin dan Kurir.

Aplikasi ini dikembangkan menggunakan Laravel 12 dengan arsitektur MVC, serta front-end berbasis Blade dan Vite. Berdasarkan analisis source code, proyek ini berfokus pada kebutuhan transaksi e-commerce lokal yang melibatkan tiga aktor utama, yaitu pelanggan, admin, dan kurir.

## 3. Latar Belakang Pengembangan

UMKM stroberi Ciwidey membutuhkan sistem yang dapat membantu proses penjualan secara lebih terstruktur dan efisien. Dalam kegiatan bisnisnya, terdapat kebutuhan untuk mengelola katalog produk, stok, pembelian pelanggan, pembayaran, pengiriman, dan pengawasan administrasi secara digital.

Berdasarkan kebutuhan tersebut, aplikasi ini dikembangkan sebagai solusi digital untuk menyederhanakan proses penjualan dan pengelolaan operasional. Sistem ini juga dilengkapi dengan mekanisme keamanan seperti verifikasi email pelanggan, reset password, serta pembatasan akses berdasarkan peran pengguna.

## 4. Tujuan Pengembangan

Tujuan pengembangan aplikasi ini adalah untuk:

- mempermudah pelanggan dalam memilih dan membeli produk stroberi
- menyediakan proses transaksi yang lebih rapi dan terdokumentasi
- membantu Admin dalam mengelola produk, pesanan, stok, pembayaran, dan laporan
- memudahkan Kurir dalam memantau dan memperbarui status pengiriman
- meningkatkan profesionalitas operasional UMKM melalui sistem berbasis web

## 5. Fitur Utama Aplikasi

Berikut fitur utama yang terdapat dalam aplikasi sesuai analisis source code:

- katalog produk dengan filter pencarian, kategori, harga, dan berat
- keranjang belanja
- checkout dan perhitungan ongkos kirim berdasarkan wilayah
- manajemen pesanan dan detail produk
- mekanisme pembayaran dan unggah bukti transfer
- verifikasi pembayaran oleh Admin
- penugasan kurir pada pesanan
- monitoring status pengiriman
- laporan penjualan dan ekspor laporan Excel
- pengelolaan produk, stok, dan harga
- verifikasi email pelanggan
- reset password dan pemulihan akun
- fitur return / pengembalian produk dan dana
- dashboard untuk admin, pelanggan, dan kurir

## 6. Fitur per Aktor

### 6.1 Pelanggan

Pelanggan dapat:

- mendaftar dan login ke aplikasi
- menggunakan username atau email saat login
- melihat katalog dan detail produk
- menambah produk ke keranjang
- melakukan checkout dengan data penerima dan alamat pengiriman
- melihat riwayat dan detail pesanan
- memilih metode pembayaran
- mengunggah bukti pembayaran
- melihat struk pembayaran dan status pesanan
- memperbarui profil dan data akun
- menerima verifikasi email
- menerbitkan dan menggunakan kode pemulihan akun
- mengajukan return untuk produk tertentu yang memenuhi syarat

### 6.2 Admin

Admin dapat:

- mengakses dashboard utama
- menambah, mengedit, dan menghapus produk
- mengelola stok dan harga produk
- melihat daftar pesanan dan detail transaksi
- mengatur tarif ongkos kirim per wilayah
- meninjau dan memverifikasi pembayaran
- menugaskan kurir untuk pengiriman
- memperbarui status pengiriman
- melihat laporan penjualan dan mengekspor laporan Excel
- mengelola akun petugas dan persetujuan kurir
- menangani permintaan reset password Admin dan Kurir
- mengelola return dan status pengembalian dana
- memperbarui profil admin

### 6.3 Kurir

Kurir dapat:

- mendaftar dan menunggu persetujuan Admin
- login ke aplikasi dan mengelola tugas pengiriman
- melihat daftar pengiriman dan detail pengiriman
- memperbarui status pengiriman dari menunggu hingga diterima
- mengonfirmasi penerimaan barang
- melihat profil dan memperbarui data diri
- mengelola password dengan mekanisme perubahan password sementara bila diperlukan

## 7. Teknologi dan Tools yang Digunakan

Teknologi yang terdeteksi dalam proyek adalah:

- PHP 8.2
- Laravel Framework 12
- Composer
- Node.js dan npm
- Vite
- Tailwind CSS
- Blade Template Engine
- MySQL / MariaDB (default konfigurasi pada Laravel)
- SQLite sebagai opsi konfigurasi basis data
- PHPUnit untuk pengujian
- File system lokal untuk menyimpan bukti pembayaran dan return

Komponen pendukung yang ada di source code:

- `app/Support/ShippingZoneResolver.php` untuk pengaturan tarif dan wilayah ongkir
- `app/Support/RecoveryCode.php` untuk kode pemulihan akun pelanggan
- `app/Support/OrderMetadata.php` untuk menyimpan metadata return
- `app/Support/CustomerEmailVerificationLink.php` untuk verifikasi email pelanggan
- `app/Http/Middleware/RoleMiddleware.php` untuk pengaturan hak akses per peran

## 8. Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan sistem memenuhi ketentuan berikut:

- PHP versi 8.2 ke atas
- Composer
- Node.js dan npm
- Web server lokal atau server deployment yang mendukung Laravel
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
  - `gd` atau `imagick`

## 9. Instalasi Proyek dari Awal

Ikuti langkah berikut:

1. Clone repository dari GitHub

```bash
git clone https://github.com/marsyamifta1112-lab/Samudera-fresh-strawberry-
cd Samudera-fresh-strawberry-
```

2. Instal dependency PHP

```bash
composer install
```

3. Instal dependency frontend

```bash
npm install
```

4. Siapkan file `.env`

File `.env.example` tidak terlihat jelas di repo saat ini, sehingga konfigurasi `.env` perlu dibuat secara manual sesuai contoh di bagian berikut.

5. Generate application key

```bash
php artisan key:generate
```

6. Konfigurasi database pada file `.env`

7. Jalankan migrasi database

```bash
php artisan migrate
```

8. Jika ingin membangun asset frontend untuk produksi, jalankan:

```bash
npm run build
```

9. Jalankan aplikasi lokal

```bash
php artisan serve
```

Salah satu script yang tersedia di `composer.json` yaitu `composer run setup` juga bisa digunakan untuk pendirian awal; namun karena file `.env.example` tidak terlihat secara eksplisit dalam struktur saat ini, konfigurasi `.env` tetap perlu dilakukan secara manual agar proses berjalan benar.

## 10. Konfigurasi File `.env`

Berikut contoh konfigurasi `.env` yang dapat digunakan untuk menjalankan aplikasi di lingkungan lokal:

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

- `APP_KEY` harus dihasilkan melalui perintah `php artisan key:generate`
- sesuaikan parameter database sesuai environment Anda
- konfigurasi email perlu diatur agar fitur verifikasi email dan pemulihan akun dapat berfungsi dengan benar

## 11. Konfigurasi dan Menjalankan Database

Proyek ini menggunakan basis data relasional dengan migrasi Laravel. Tabel utama yang terlihat pada source code meliputi:

- `produk`
- `pelanggan`
- `petugas`
- `pesanan`
- `detail_pesanan`
- `pembayaran`
- `pengiriman`
- `ongkir_wilayah`
- `admin_password_recovery_requests`

Langkah konfigurasi database:

1. buat database baru di MySQL/MariaDB
2. atur nilai `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada file `.env`
3. jalankan migrasi

```bash
php artisan migrate
```

4. Pastikan tabel dan kolom yang dibutuhkan berhasil dibuat
5. Setelah migrasi, data wilayah ongkir dan tarif awal akan tersedia melalui migrasi terkait, dan dapat diubah melalui panel Admin

> Informasi penting: struktur migration tampak berisi serangkaian perubahan terhadap tabel yang sudah ada, sehingga perlu dijalankan dalam konteks Laravel yang benar dan hati-hati.

## 12. Menjalankan Aplikasi Secara Lokal

Untuk menjalankan aplikasi lokal:

```bash
php artisan serve
```

Akses aplikasi melalui browser di:

```text
http://localhost:8000
```

Jika ingin menjalankan seluruh kebutuhan pengembangan sekaligus, gunakan:

```bash
composer run dev
```

Untuk build asset produksi:

```bash
npm run build
```

## 13. Struktur Folder Proyek

```text
samudera_fresh_strawberry/
├─ app/
│  ├─ Console/
│  ├─ Http/
│  │  ├─ Controllers/
│  │  ├─ Middleware/
│  │  └─ ...
│  ├─ Mail/
│  ├─ Models/
│  └─ Support/
├─ bootstrap/
├─ config/
├─ database/
│  ├─ factories/
│  ├─ migrations/
│  └─ seeders/
├─ docs/
├─ public/
│  ├─ images/
│  └─ index.php
├─ resources/
│  ├─ css/
│  ├─ js/
│  └─ views/
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

- `app/` berisi logika bisnis, controller, model, middleware, dan class pendukung
- `routes/` berisi rute aplikasi web dan API
- `resources/views/` berisi tampilan Blade untuk login, katalog, checkout, pembayaran, admin, kurir, dan lain-lain
- `database/migrations/` berisi skema dan perubahan tabel database
- `public/` berisi file publik seperti gambar dan entry point aplikasi
- `docs/` berisi manual book pengguna
- `config/` berisi konfigurasi Laravel seperti database, mail, auth, dan session

## 14. Database dan Tabel Utama

### 14.1 Tabel `produk`
Menyimpan data produk, seperti:

- `id_produk`
- `nama_produk`
- `deskripsi`
- `harga_produk`
- `stok_produk`
- `berat_gram`
- `foto_produk`

### 14.2 Tabel `pelanggan`
Menyimpan data pelanggan, seperti:

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

### 14.3 Tabel `petugas`
Menyimpan data admin dan kurir, seperti:

- `id_petugas`
- `nama_petugas`
- `username`
- `password`
- `no_telp`
- `alamat`
- `role`
- `status_akun`
- `must_change_password`
- `password_reset_status`

### 14.4 Tabel `pesanan`
Menyimpan data transaksi utama, seperti:

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

### 14.5 Tabel `detail_pesanan`
Menyimpan rincian setiap produk dalam pesanan, seperti:

- `id_detail`
- `id_pesanan`
- `id_produk`
- `jumlah`
- `harga`
- `subtotal`

### 14.6 Tabel `pembayaran`
Menyimpan data pembayaran pesanan, seperti:

- `id_pembayaran`
- `id_pesanan`
- `metode_pembayaran`
- `jumlah_bayar`
- `status_pembayaran`
- `tanggal_pembayaran`
- `bukti_pembayaran`
- `nomor_struk`
- `tanggal_verifikasi`

### 14.7 Tabel `pengiriman`
Menyimpan data pengiriman untuk kurir, seperti:

- `id_pengiriman`
- `id_pesanan`
- `id_petugas`
- `nama_penerima`
- `no_telp_penerima`
- `alamat_pengiriman`
- `status_pengiriman`
- `tanggal_pengiriman`

### 14.8 Tabel `ongkir_wilayah`
Menyimpan tarif ongkos kirim berdasarkan wilayah, antara lain:

- Ciwidey & Rancabali
- Pasirjambu & Soreang
- Bandung Raya
- Luar Bandung Raya

### 14.9 Metadata return
Fitur return tidak sepenuhnya menggunakan tabel khusus; metadata return disimpan dalam kolom `catatan` pada tabel `pesanan` dengan format JSON melalui class `OrderMetadata`.

## 15. Deployment dan Hosting

File `Procfile` menunjukkan bahwa aplikasi ini dirancang untuk dijalankan pada platform hosting yang mendukung variabel `PORT`, seperti Railway atau platform sejenis.

Isi `Procfile` adalah:

```text
web: php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
```

Beberapa variabel deployment yang biasanya perlu disiapkan:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sesuai domain deployment
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER`
- `CACHE_STORE`
- `QUEUE_CONNECTION`
- `MAIL_*` untuk fitur verifikasi email

> URL produksi dan detail konfigurasi hosting final belum tercantum secara eksplisit di source code. Informasi tersebut perlu dikonfirmasi kembali ke pengembang atau pemilik proyek.

## 16. Tim Pengembang

Proyek ini dikembangkan oleh:

- Kelompok Twinbyte
- SMK Budi Bakti Ciwidey

> Informasi nama anggota tim, kontak, dan detail penanggung jawab proyek belum tercantum secara eksplisit di source code. Informasi ini perlu dikonfirmasi kembali jika ingin ditampilkan secara lengkap di dokumentasi publik.

## 17. Penutup

Samudera Fresh Strawberry merupakan aplikasi berbasis web yang dirancang untuk mendukung proses penjualan produk stroberi Ciwidey secara lebih modern, tertata, dan efisien. Aplikasi ini mencakup seluruh alur bisnis utama, mulai dari katalog produk, pemesanan, pembayaran, pengiriman, hingga pengelolaan data oleh Admin dan Kurir.

Dokumen ini dibuat berdasarkan hasil analisis terhadap source code yang tersedia pada repositori. Semoga README ini dapat menjadi dokumentasi resmi yang siap digunakan untuk kebutuhan GitHub, penilaian akhir, dan pengelolaan proyek di masa depan.

Terima kasih atas perhatian dan dukungannya.

---

© 2026 Samudera Fresh Strawberry — Kelompok Twinbyte, SMK Budi Bakti Ciwidey.

