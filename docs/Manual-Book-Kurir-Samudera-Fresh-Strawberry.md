# MANUAL BOOK KURIR
## Aplikasi Samudera Fresh Strawberry

**Buku Panduan Penggunaan untuk Kurir**  
**Versi dokumen:** 1.0  
**Bahasa:** Indonesia

> **Catatan:** Panduan ini khusus untuk aktor Kurir dan disusun berdasarkan fitur yang tersedia pada project. Nama domain/URL produksi belum ditentukan di kode, sehingga alamat resmi perlu diperoleh dari pengelola aplikasi. Screenshot belum disertakan dalam bahan penyusunan. Tampilan dapat berbeda mengikuti ukuran layar atau perubahan versi aplikasi.

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Persiapan dan Akses Aplikasi](#2-persiapan-dan-akses-aplikasi)
3. [Gambaran Tugas Kurir](#3-gambaran-tugas-kurir)
4. [Panduan Penggunaan Kurir](#4-panduan-penggunaan-kurir)
5. [Arti Status Pengiriman](#5-arti-status-pengiriman)
6. [Penanganan Kendala](#6-penanganan-kendala)
7. [Penutup](#7-penutup)

---

## 1. Pendahuluan

### 1.1 Kata Pengantar

Manual Book ini disusun sebagai panduan bagi Kurir yang menggunakan Aplikasi Samudera Fresh Strawberry. Panduan menjelaskan cara mengakses akun, melihat tugas pengiriman, memeriksa informasi penerima, memperbarui status sesuai tahap perjalanan, dan mengelola akun Kurir.

### 1.2 Pengenalan Aplikasi

Samudera Fresh Strawberry adalah aplikasi berbasis web yang mendukung pemesanan dan pengiriman produk stroberi serta produk olahannya. Kurir menggunakan area kerja khusus untuk melihat pesanan yang telah ditugaskan kepadanya dan mencatat perkembangan pengiriman.

Panduan ini hanya membahas hak akses **Kurir**. Kurir hanya melihat tugas yang ditugaskan ke akun yang sedang digunakan; pengelolaan produk, pembayaran, dan penugasan merupakan tanggung jawab Admin.

### 1.3 Tujuan dan Manfaat

Melalui aplikasi, Kurir dapat:

1. Melihat ringkasan tugas pengiriman pada Dashboard.
2. Membuka daftar tugas yang telah ditugaskan.
3. Memeriksa nomor pesanan, nama dan nomor telepon penerima, alamat, serta catatan.
4. Memperbarui status pengiriman secara berurutan.
5. Memperbarui informasi profil dan mengajukan pemulihan password.

### 1.4 Persiapan Sebelum Menggunakan

1. Siapkan perangkat, browser modern, dan koneksi internet.
2. Siapkan username dan password akun Kurir.
3. Pastikan akun sudah disetujui Admin. Akun Kurir yang masih menunggu persetujuan tidak dapat digunakan untuk login.
4. Pastikan nomor telepon dan alamat penerima dapat dibaca pada detail pengiriman.
5. Jika menggunakan password sementara dari proses reset, siapkan password baru minimal 8 karakter dan konfirmasinya.

---

## 2. Persiapan dan Akses Aplikasi

1. Buka browser pada komputer atau telepon pintar.
2. Masukkan alamat domain aplikasi yang diberikan pengelola. Domain resmi tidak ditetapkan pada source code dan perlu dikonfirmasi kepada pengelola.
3. Buka halaman **Login**. Tautan **Kurir? Ajukan Reset Password** dan **Mendaftar sebagai Kurir? Daftar di sini** tersedia pada halaman tersebut.
4. Masukkan username dan password Kurir, kemudian pilih **LOG IN**.
5. Jika berhasil, aplikasi membuka **Dashboard Kurir**.

Rute `/kurir-login` mengarahkan ke halaman Login yang sama. Sistem menentukan akses berdasarkan peran akun.

### 2.1 Mengajukan Pendaftaran Kurir

Jika belum memiliki akun:

1. Pada halaman Login, pilih **Daftar di sini** pada bagian “Mendaftar sebagai Kurir?”.
2. Isi nama dan username yang belum digunakan.
3. Buat password minimal 8 karakter, lalu isi konfirmasi password.
4. Nomor telepon dan alamat dapat diisi sebagai informasi tambahan.
5. Kirim formulir pendaftaran.
6. Aplikasi menampilkan bahwa permohonan berhasil dikirim dan menunggu persetujuan Admin.
7. Setelah akun disetujui, login menggunakan username dan password yang didaftarkan.

Akun berstatus **Menunggu Persetujuan** tidak dapat login. Jika permohonan belum disetujui, hubungi Admin; jangan mengirim permohonan berulang kali dengan username berbeda.

---

## 3. Gambaran Tugas Kurir

1. Admin memverifikasi pembayaran pesanan dan menugaskan Kurir.
2. Tugas yang ditugaskan muncul pada Dashboard dan menu **Pengiriman** akun Kurir tersebut.
3. Kurir membuka detail tugas, memeriksa nama penerima, nomor telepon, alamat, catatan, dan nomor pesanan.
4. Kurir memperbarui status satu tahap setiap kali sesuai keadaan pengiriman.
5. Setelah pesanan benar-benar diterima penerima, Kurir memilih **Konfirmasi Diterima**.
6. Aplikasi mencatat status pengiriman **diterima**, menandai pesanan sebagai selesai, dan menampilkan konfirmasi. Pelanggan dapat melihat perkembangan pada menu Pesanan.

Jika belum ada tugas yang ditampilkan, Kurir perlu menunggu penugasan Admin atau menghubungi Admin untuk memastikan pesanan memenuhi syarat penugasan.

---

## 4. Panduan Penggunaan Kurir

### 4.1 Login dan Penggantian Password Sementara

**Login biasa**

1. Buka halaman **Login**.
2. Masukkan username dan password Kurir.
3. Pilih **LOG IN**.
4. Jika akun aktif, aplikasi mengarahkan ke **Dashboard Kurir**.

**Login dengan password sementara**

1. Setelah Admin menyetujui reset password, gunakan password sementara yang diberikan langsung oleh Admin.
2. Login menggunakan username dan password sementara.
3. Aplikasi meminta penggantian password sebelum Dashboard dibuka.
4. Isi **Password baru** dan **Konfirmasi password baru** dengan nilai yang sama. Password minimal 8 karakter.
5. Pilih **Simpan Password Baru**.
6. Setelah berhasil, aplikasi membuka Dashboard Kurir.

Password sementara hanya untuk satu kali masuk. Jika perubahan gagal, periksa panjang dan kecocokan password baru serta konfirmasinya.

### 4.2 Mengenal Dashboard Kurir

**Tujuan:** Melihat ringkasan pekerjaan pengiriman milik akun Kurir.

Dashboard **Dashboard Kurir** menampilkan:

- **Pengiriman Hari Ini:** jumlah pengiriman akun Kurir yang memiliki tanggal pengiriman hari ini.
- **Sedang Dikirim:** jumlah tugas berstatus **dikirim**.
- **Selesai:** jumlah tugas berstatus **diterima**.
- **Daftar Tujuan:** beberapa tujuan dari pengiriman yang belum berstatus diterima.

Pilih **Lihat Data Pengiriman** untuk membuka daftar tugas. Nilai dashboard hanya menghitung pengiriman yang ditugaskan kepada akun yang sedang login.

### 4.3 Melihat Daftar Pengiriman

**Menu:** **Pengiriman**.

1. Pilih **Pengiriman** pada navigasi atau **Lihat Data Pengiriman** dari Dashboard.
2. Halaman **Data Pengiriman** menampilkan tugas yang ditugaskan kepada akun Kurir.
3. Pada setiap baris, periksa status, nama penerima, alamat, nomor pesanan, dan nomor telepon.
4. Pilih **Lihat Detail** pada tugas yang akan dikerjakan.
5. Jika daftar memuat lebih dari satu halaman, gunakan navigasi halaman untuk melihat daftar berikutnya.

Jika daftar kosong, aplikasi menampilkan pesan **Belum ada pengiriman yang ditugaskan kepada Anda**. Periksa kembali nanti atau hubungi Admin.

### 4.4 Memeriksa Detail dan Informasi Penerima

**Tujuan:** Memastikan tujuan dan kontak pengiriman sebelum berangkat.

1. Dari daftar **Pengiriman**, pilih **Lihat Detail**.
2. Pada halaman **Detail Pengiriman (Kurir)**, periksa nomor pesanan dengan format seperti `SP000001`.
3. Periksa nama penerima dan nomor telepon.
4. Baca alamat pengiriman dan catatan pelanggan bila tersedia.
5. Periksa status saat ini dan indikator progres.
6. Jika informasi penerima/alamat kosong, tidak lengkap, atau tidak sesuai, hubungi Admin sebelum melanjutkan dan jangan mengonfirmasi pesanan diterima.

Informasi yang tampil berasal dari data pesanan. Gunakan hanya untuk menjalankan pengiriman yang ditugaskan.

### 4.5 Memperbarui Status Pengiriman

Status harus diperbarui berurutan dan sesuai kejadian sebenarnya:

| Status saat ini | Tindakan pada Detail Pengiriman | Status berikutnya |
|---|---|---|
| **menunggu** | Pilih **Update Status** | **diproses** |
| **diproses** | Pilih **Update Status** | **dikirim** |
| **dikirim** | Setelah penerima benar-benar menerima pesanan, pilih **Konfirmasi Diterima** | **diterima** |
| **diterima** | Pilih **Lihat Konfirmasi** jika ingin membuka halaman konfirmasi kembali | Tetap **diterima** |

Langkah penggunaan:

1. Buka detail tugas yang sedang dikerjakan.
2. Pastikan status saat ini dan kondisi barang sesuai.
3. Pilih tombol yang tersedia: **Update Status** atau **Konfirmasi Diterima**.
4. Periksa pemberitahuan hasil tindakan.
5. Setelah status **diterima**, aplikasi membuka halaman **Konfirmasi Diterima** yang memuat nomor pesanan dan tombol **Kembali ke Beranda**.

Kurir tidak dapat melewati tahap atau mengubah status dari **diterima** kembali. Jika tindakan ditolak karena status berbeda, muat ulang halaman untuk melihat status terbaru dan hubungi Admin jika masih ada kendala. Jangan memilih **Konfirmasi Diterima** sebelum pesanan benar-benar diterima pelanggan.

### 4.6 Mengelola Profil Kurir

**Menu:** **Profil**.

1. Buka **Profil** dari navigasi.
2. Tinjau bagian **Profil Tersimpan**.
3. Pada formulir **Ubah Profil**, perbarui **Nama** atau **Username** bila diperlukan.
4. Perbarui **Nomor Telepon** atau **Alamat** bila diperlukan.
5. Jika ingin mengubah password, isi **Password Baru** dan **Konfirmasi Password Baru**. Biarkan kedua kolom kosong jika password tidak ingin diubah.
6. Pilih **Simpan Profil**.
7. Periksa pesan keberhasilan atau validasi.

Username harus unik. Password baru minimal 8 karakter. Jika username sudah digunakan atau konfirmasi password tidak cocok, koreksi data dan simpan kembali.

### 4.7 Pemulihan Password Kurir

**Tujuan:** Meminta bantuan Admin ketika Kurir tidak dapat login karena lupa password.

1. Pada halaman Login, pilih **Kurir? Ajukan Reset Password**.
2. Masukkan **Username Kurir**.
3. Pilih **Kirim Permintaan**.
4. Aplikasi menampilkan pemberitahuan bahwa permintaan akan diteruskan kepada Admin untuk verifikasi. Formulir ini tidak mengirim email.
5. Hubungi Admin jika diperlukan agar permintaan ditinjau melalui menu **Petugas**.
6. Jika disetujui, Admin memberikan password sementara secara langsung.
7. Login dengan password sementara dan segera buat password pribadi baru. Aplikasi mewajibkan penggantian sebelum membuka Dashboard.

Permintaan yang sudah berstatus **Menunggu Verifikasi** tidak dibuat ulang sampai Admin menanganinya. Jika permintaan ditolak atau belum diproses, hubungi Admin. Jangan mengirim password melalui catatan pesanan.

### 4.8 Logout

1. Pilih **Keluar dari Kurir** pada bagian bawah navigasi, atau gunakan ikon keluar pada bagian atas.
2. Aplikasi mengakhiri sesi dan mengarahkan kembali ke Login.
3. Pada perangkat bersama, pastikan Anda sudah keluar sebelum meninggalkan perangkat.

---

## 5. Arti Status Pengiriman

| Status | Arti untuk Kurir |
|---|---|
| **menunggu** | Tugas telah tersedia dan menunggu Kurir memulai/menyiapkan pengiriman. |
| **diproses** | Tugas sedang disiapkan atau telah diambil untuk dilanjutkan. |
| **dikirim** | Pesanan sedang dalam perjalanan ke penerima. |
| **diterima** | Pesanan telah diserahkan dan diterima; pengiriman selesai. |

Status **diterima** juga menyebabkan status pesanan menjadi **selesai**. Status pembayaran ditangani pada proses terpisah dan bukan tanggung jawab perubahan status Kurir.

---

## 6. Penanganan Kendala

| Kendala | Penyebab yang mungkin | Penanganan |
|---|---|---|
| Login ditolak | Username/password salah atau akun belum aktif. | Periksa kredensial. Jika akun menunggu persetujuan, hubungi Admin. |
| Pendaftaran belum dapat digunakan | Akun berstatus **Menunggu Persetujuan**. | Tunggu persetujuan Admin sebelum mencoba login kembali. |
| Dashboard tidak menampilkan tujuan | Belum ada tugas pengiriman aktif yang ditugaskan. | Buka **Pengiriman** untuk memeriksa daftar, lalu hubungi Admin tentang penugasan. |
| Daftar pengiriman kosong | Admin belum menugaskan pesanan ke akun tersebut. | Pastikan menggunakan akun Kurir yang tepat dan tanyakan status tugas kepada Admin. |
| Alamat atau nomor telepon tidak tersedia | Data penerima pada pesanan tidak lengkap. | Hubungi Admin untuk klarifikasi sebelum melakukan pengiriman. Jangan mengonfirmasi penerimaan. |
| Status tidak dapat diperbarui | Tahapan tidak berurutan, status sudah berubah, atau tugas bukan milik akun Kurir. | Muat ulang detail, pilih tindakan yang tersedia untuk status saat ini, dan hubungi Admin jika gagal. |
| Tidak bisa mengganti password sementara | Password kurang dari 8 karakter atau konfirmasi tidak sama. | Buat password minimal 8 karakter dan pastikan kedua kolom sama. |
| Lupa password | Password akun tidak diketahui. | Ajukan reset dari tautan Kurir pada Login; Admin harus memverifikasi dan memberikan password sementara. |
| Permintaan reset belum diproses | Admin belum memeriksa permintaan. | Hubungi Admin dan sampaikan username. Formulir reset tidak mengirim email. |
| Perubahan profil gagal | Username telah digunakan atau data tidak sesuai. | Gunakan username unik dan periksa pesan validasi sebelum menyimpan kembali. |

Saat meminta bantuan, sampaikan username, nomor pesanan, status yang terlihat, dan pesan kesalahan. Jangan mengirim password atau informasi rahasia melalui catatan pesanan atau kepada pihak yang tidak berwenang.

---

## 7. Penutup

Aplikasi membantu Kurir melihat tugas yang ditugaskan, memperoleh informasi penerima, dan mencatat kemajuan pengiriman. Pembaruan status yang akurat dan berurutan membantu Admin serta pelanggan memahami perkembangan pesanan.

Semoga Manual Book ini membantu Kurir menggunakan aplikasi dengan mudah dan menjalankan pengiriman sesuai informasi yang tersedia. Terima kasih atas kerja sama Anda.

**— Selesai —**
