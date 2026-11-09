# MANUAL BOOK ADMIN
## Aplikasi Samudera Fresh Strawberry

**Buku Panduan Penggunaan untuk Admin**  
**Versi dokumen:** 1.0  
**Tanggal:** 9 November 2026  
**Bahasa:** Indonesia

> **Catatan:** Panduan ini khusus untuk aktor Admin dan disusun berdasarkan fitur yang tersedia pada project. Nama domain/URL produksi belum ditentukan di kode, sehingga alamat resmi perlu diperoleh dari pengelola aplikasi. Screenshot belum disertakan dalam bahan penyusunan. Tampilan dapat berbeda mengikuti ukuran layar atau perubahan versi aplikasi.

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Persiapan dan Akses](#2-persiapan-dan-akses)
3. [Gambaran Tugas Admin](#3-gambaran-tugas-admin)
4. [Panduan Penggunaan Admin](#4-panduan-penggunaan-admin)
5. [Status yang Perlu Dipahami](#5-status-yang-perlu-dipahami)
6. [Penanganan Kendala](#6-penanganan-kendala)
7. [Penutup](#7-penutup)

---

## 1. Pendahuluan

### 1.1 Kata Pengantar

Manual Book ini disusun sebagai panduan operasional bagi Admin Aplikasi Samudera Fresh Strawberry. Panduan menjelaskan cara mengakses dashboard, mengelola produk dan tarif, memproses pesanan dan pembayaran, menugaskan pengiriman, membaca laporan, mengelola petugas, serta menangani Return Barang.

### 1.2 Pengenalan Aplikasi

Samudera Fresh Strawberry adalah aplikasi berbasis web yang mendukung katalog dan pemesanan produk stroberi serta produk olahannya. Admin berperan mengelola data operasional dan memastikan setiap pesanan ditangani sesuai tahapnya. Akses Admin dilindungi berdasarkan peran akun.

Panduan ini hanya membahas hak akses **Admin**. Fitur yang dijelaskan tersedia pada project; jika alamat domain, konfigurasi layanan email, atau prosedur bisnis di luar aplikasi diperlukan, hal tersebut harus dikonfirmasi kepada pengelola.

### 1.3 Tanggung Jawab Admin

Admin dapat:

- Memantau ringkasan operasional pada **Dashboard**.
- Menambah, mencari, mengubah, dan menghapus produk sesuai ketentuan.
- Memperbarui harga dan stok serta tarif ongkir.
- Memeriksa pesanan, menetapkan ongkir pesanan, dan meninjau pembayaran.
- Menugaskan Kurir dan memantau status pengiriman.
- Melihat dan mengunduh laporan penjualan.
- Mengelola akun petugas, menyetujui pendaftaran Kurir, serta menangani permintaan pemulihan akun.
- Mengelola profil Admin.
- Meninjau pengajuan **Return Barang**.

### 1.4 Persiapan Sebelum Menggunakan

1. Siapkan perangkat, browser modern, dan koneksi internet.
2. Pastikan akun Admin telah dibuat dan berstatus aktif.
3. Siapkan username dan password Admin.
4. Untuk operasional, siapkan informasi produk, harga, stok aktual, tarif pengiriman, dan akses untuk memeriksa transaksi.
5. Untuk penugasan, pastikan akun Kurir yang akan dipilih telah disetujui dan aktif.

---

## 2. Persiapan dan Akses

1. Buka browser pada komputer atau perangkat yang digunakan.
2. Masukkan alamat domain aplikasi yang diberikan pengelola, lalu tekan Enter. Domain resmi tidak ditetapkan pada source code dan perlu dikonfirmasi kepada pengelola.
3. Buka halaman **Login**. Rute `/admin-login` juga mengarahkan ke halaman Login bersama.
4. Masukkan username dan password akun Admin, lalu pilih **LOG IN**.
5. Jika autentikasi berhasil, aplikasi membuka **Dashboard Admin**.

Akun Admin dibuat atau dikelola oleh petugas berwenang; pendaftaran pelanggan tidak memberikan akses Admin. Bila akun tidak aktif atau kredensial salah, akses akan ditolak.

---

## 3. Gambaran Tugas Admin

Alur operasional Admin secara umum:

1. Periksa dashboard dan pesanan baru.
2. Pastikan produk, harga, stok, dan tarif ongkir tetap sesuai.
3. Buka rincian pesanan; pastikan ongkir tersedia sebelum pelanggan membayar.
4. Periksa bukti transfer lalu pilih **Terima** atau **Tolak**.
5. Untuk pembayaran yang diverifikasi, siapkan pesanan dan tugaskan Kurir.
6. Pantau status pengiriman serta laporan penjualan.
7. Tinjau pengajuan Return Barang dan kelola petugas bila diperlukan.

Tindakan Admin memengaruhi tahapan berikutnya. Misalnya, pesanan belum dapat masuk daftar pengiriman sebelum pembayaran diverifikasi. Ongkir tidak dapat diubah setelah bukti pembayaran diunggah atau pembayaran diverifikasi.

---

## 4. Panduan Penggunaan Admin

### 4.1 Dashboard Admin

**Tujuan:** Memantau ringkasan kegiatan usaha.

1. Setelah login, halaman **Dashboard Admin** menampilkan:
   - **Total Produk**.
   - **Total Pesanan**.
   - **Total Penjualan**, berdasarkan pembayaran yang telah diverifikasi.
   - **Pesanan Baru**, yaitu jumlah pesanan berstatus menunggu.
   - **Grafik Penjualan** harian selama tujuh hari terakhir.
   - **Pesanan Baru**, berupa beberapa pesanan terbaru yang dapat dipilih untuk melihat detail.
2. Pilih pesanan terbaru untuk membuka detailnya.
3. Gunakan menu pada sisi kiri untuk berpindah ke area operasional.

Jika ringkasan kosong, periksa apakah data pesanan/produk memang sudah tercatat. Nilai penjualan tidak memasukkan pembayaran yang belum diverifikasi.

### 4.2 Mengelola Produk

**Menu:** **Produk**.

**Mencari dan melihat produk**

1. Pilih **Produk** pada navigasi.
2. Untuk mencari, isi kolom **Cari nama produk** lalu pilih **Cari**.
3. Daftar menampilkan nama, kategori yang dikenali dari nama produk, dan berat bila tercantum.
4. Pilih baris produk untuk membuka formulir **Edit Produk**.
5. Pilih **Produk Terlaris** untuk melihat hingga sepuluh produk teratas berdasarkan jumlah terjual. Pilih **Kelola Produk** untuk kembali.

**Menambahkan produk**

1. Pada halaman Produk, pilih ikon **＋**.
2. Isi **Nama Produk**, **Harga**, dan **Stok**. Nama, harga, dan stok wajib diisi.
3. Isi **Deskripsi** dan **Foto Produk** bila tersedia. Foto yang diterima JPG, JPEG, PNG, atau WEBP, maksimal 5 MB.
4. Kolom **Kategori** ditentukan otomatis dari nama: nama yang memuat “keripik” menjadi Keripik, “selai” menjadi Selai, “segar” menjadi Segar, dan lainnya menjadi Olahan. Kategori tidak diedit secara langsung.
5. Pilih **Simpan**. Aplikasi kembali ke daftar dan menampilkan pemberitahuan hasil.

Formulir Admin yang tersedia saat ini tidak menampilkan kolom untuk mengubah berat gram. Daftar produk hanya menampilkan berat jika data berat sudah tersedia.

**Mengubah produk**

1. Pilih produk dari daftar.
2. Ubah informasi yang diperlukan.
3. Jika mengganti foto, pilih file baru dan periksa pratinjau.
4. Pilih **Simpan Perubahan**.

**Menghapus produk**

1. Buka formulir produk yang akan dihapus.
2. Pilih **Hapus Produk** dan konfirmasi tindakan pada dialog.
3. Produk yang tercatat dalam detail pesanan tidak dapat dihapus. Jika penolakan muncul, pertahankan catatan produk dan kelola ketersediaannya melalui stok.

Jika simpan gagal, periksa kolom wajib, nilai harga/stok, tipe dan ukuran foto, serta pesan validasi.

### 4.3 Memperbarui Harga dan Stok

**Menu:** **Harga & Stok**.

1. Buka **Harga & Stok**.
2. Temukan produk pada tabel. Gunakan navigasi halaman jika daftar lebih dari satu halaman.
3. Perbarui nilai pada kolom harga dan/atau stok.
4. Pilih ikon edit/simpan pada baris yang sama.
5. Periksa pemberitahuan keberhasilan dan nilai yang tersimpan.

Harga harus angka nonnegatif, sedangkan stok berupa bilangan bulat nonnegatif. Pastikan stok mencerminkan persediaan aktual, karena jumlah tersebut digunakan untuk membatasi pembelian pelanggan.

### 4.4 Mengelola Tarif Ongkir

**Menu:** **Tarif Ongkir**.

1. Buka **Tarif Ongkir**.
2. Periksa tabel nama wilayah dan tarif.
3. Ubah tarif yang perlu disesuaikan pada kolom **Tarif ongkir (Rp)**.
4. Pilih **Simpan Tarif Wilayah**.
5. Pastikan pemberitahuan berhasil muncul.

Tarif berlaku untuk pesanan baru. Perubahan tarif tidak memperbarui otomatis ongkir pesanan yang telah dibuat. Tarif harus diisi sebagai bilangan bulat nonnegatif. Aplikasi mencocokkan alamat Checkout ke wilayah yang dikenal, seperti Ciwidey & Rancabali, Pasirjambu & Soreang, Bandung Raya, atau Luar Bandung Raya.

### 4.5 Melihat Pesanan dan Rinciannya

**Menu:** **Pesanan**.

1. Buka **Pesanan** untuk melihat daftar terbaru, status, pelanggan, tanggal, wilayah, dan total.
2. Pilih baris pesanan untuk membuka **Detail Pesanan (Admin)**.
3. Periksa pelanggan, alamat, rincian produk, jumlah, subtotal produk, wilayah tujuan, ongkir, dan total pembayaran.
4. Jika ongkir belum tersedia, tetapkan ongkir pada form di detail sebelum pelanggan dapat menyelesaikan pembayaran.
5. Pilih **Kembali ke Daftar Pesanan** untuk kembali ke daftar.

Jika daftar kosong, belum ada pesanan tercatat. Gunakan nomor pesanan dan data penerima saat berkoordinasi mengenai transaksi.

### 4.6 Menetapkan Ongkir Pesanan

**Lokasi:** Detail pesanan, bagian **Tetapkan ongkir (Rp)**.

1. Periksa wilayah tujuan dan subtotal produk.
2. Masukkan tarif ongkir dalam rupiah. Ongkir dibatasi maksimal sebesar subtotal produk.
3. Pilih **Simpan Ongkir**.
4. Periksa nilai ongkir dan total pembayaran terbaru.

Ongkir tidak dapat diubah setelah bukti pembayaran diunggah atau pembayaran diverifikasi. Jika aplikasi menyatakan ongkir terkunci, jangan mencoba mengganti nominal; periksa status pembayaran pesanan.

### 4.7 Memeriksa Bukti dan Memverifikasi Pembayaran

**Lokasi:** **Pesanan** → pilih pesanan → **Verifikasi Pembayaran**.

1. Pastikan ongkir sudah ditetapkan.
2. Periksa subtotal, ongkir, total, bank pengirim, nama pelanggan, dan status pembayaran.
3. Pada bagian **Bukti Transfer**, buka/lihat gambar bukti.
4. Cocokkan bukti dengan nominal dan informasi pesanan.
5. Jika pembayaran sesuai, pilih **Terima**. Status pembayaran menjadi **diverifikasi**. Jika status pesanan masih **menunggu**, aplikasi otomatis mengubahnya menjadi **diproses** dan membuat nomor struk.
6. Jika pembayaran tidak sesuai atau belum dapat dipastikan, pilih **Tolak**. Pelanggan melihat status pembayaran ditolak dan dapat mengunggah bukti pengganti selama pembayaran belum diverifikasi.
7. Setelah pembayaran diverifikasi, pilih **Cetak Struk** jika perlu.
8. Gunakan **Kembali ke Detail Pesanan** untuk kembali ke rincian pesanan.

Tombol **Terima** dan **Tolak** tersedia saat pembayaran berstatus menunggu dan bukti telah diunggah. Bila bukti belum ada, ongkir belum ditetapkan, atau pembayaran sudah diproses, aplikasi menolak perubahan dan menampilkan keterangan.

### 4.8 Menugaskan Kurir dan Memantau Pengiriman

**Menu:** **Pengiriman**.

1. Buka **Pengiriman**. Daftar menampilkan pesanan yang pembayaran sudah diverifikasi.
2. Periksa nama penerima, alamat, status, serta nama Kurir jika telah ditugaskan.
3. Pada **Pilih kurir**, pilih Kurir yang akan menangani pesanan.
4. Pilih **Tugaskan**. Pemberitahuan keberhasilan muncul dan nama Kurir ditampilkan pada baris pesanan.
5. Jika diperlukan, gunakan pilihan status pengiriman pada baris pesanan dan pilih **Simpan** untuk memperbarui status.
6. Periksa kembali status terbaru pada daftar.

Pilihan status Admin adalah **Menunggu**, **Diproses**, **Dikirim**, dan **Diterima**. Pembaruan status **Diterima** menandai pesanan sebagai **selesai**. Pilih Kurir yang akun dan penugasannya benar. Jika tidak ada pesanan di daftar, pastikan pembayaran pesanan sudah diverifikasi.

### 4.9 Melihat dan Mengunduh Laporan

**Menu:** **Laporan**.

1. Buka **Laporan**.
2. Isi **Dari** dan **Sampai** untuk memilih rentang tanggal.
3. Pilih **Terapkan**.
4. Tinjau ringkasan **Total Penjualan**, grafik penjualan bulanan, serta **Data Penjualan** yang memuat ID pesanan, tanggal, pelanggan, subtotal, ongkir, total, status pesanan, metode pembayaran, dan status pembayaran.
5. Pilih **Unduh Excel (.xlsx)** untuk mengunduh laporan pada rentang tanggal tersebut.

Jika tanggal tidak diisi, aplikasi menggunakan awal bulan sampai tanggal hari ini. Tanggal **Sampai** harus sama atau lebih akhir dari tanggal **Dari**. Total penjualan hanya menjumlahkan pembayaran terverifikasi. Jika tidak ada transaksi pada periode, tabel menampilkan keterangan bahwa data tidak tersedia.

### 4.10 Mengelola Petugas

**Menu:** **Petugas**.

**Menambah petugas**

1. Buka **Petugas** dan pilih **+ Tambah Petugas**.
2. Isi **Nama Petugas**, **Username**, **Password**, **Konfirmasi Password**, dan **Role** (Admin atau Kurir).
3. Nomor telepon dan alamat dapat diisi sebagai data tambahan.
4. Pilih **Simpan Petugas**.

Username harus unik. Password minimal 8 karakter dan konfirmasi harus sama. Akun petugas yang dibuat melalui menu ini aktif.

**Menyetujui pendaftaran Kurir**

1. Cari petugas dengan **Role Kurir** dan status akun **Menunggu Persetujuan**.
2. Periksa informasi akun.
3. Pilih **Setujui Akun** untuk mengaktifkan akun.
4. Pemberitahuan keberhasilan ditampilkan.

Akun Kurir yang belum disetujui tidak dapat login.

### 4.11 Pemulihan Password Admin dan Kurir

**Pemulihan password Admin**

1. Admin yang lupa password membuka tautan **Admin? Lupa Password** pada halaman Login.
2. Masukkan username Admin lalu pilih **Ajukan Pemulihan**.
3. Admin lain yang masih dapat login membuka **Petugas** dan memeriksa bagian **Permintaan Pemulihan Password Admin**.
4. Admin peninjau memilih **Setujui Pemulihan** atau **Tolak**. Hanya Admin lain yang dapat menyetujui.
5. Jika disetujui, Admin pemohon membuka kembali halaman pemulihan pada browser yang sama dan membuat password baru dalam waktu 30 menit.
6. Setelah menyetujui permintaan Admin lain, Admin peninjau perlu logout agar pemohon dapat melanjutkan dari browser yang sama.

Jika semua Admin tidak dapat login, aplikasi menyarankan menghubungi developer untuk pemulihan.

**Pemulihan password Kurir**

1. Kurir mengajukan reset dari tautan **Kurir? Ajukan Reset Password** pada halaman Login.
2. Di **Petugas**, periksa permintaan Kurir dengan status **Menunggu Verifikasi**.
3. Pilih **Verifikasi & Setujui Reset** untuk menyetujui atau **Tolak** untuk menolak.
4. Jika disetujui, aplikasi menampilkan password sementara satu kali pada halaman Petugas.
5. Berikan password tersebut langsung kepada Kurir melalui cara yang aman. Kurir wajib menggantinya saat login.

Jangan menyimpan atau membagikan password sementara secara terbuka. Jika pesan/status permintaan tidak tampil, periksa kembali akun dan status permintaan.

### 4.12 Mengelola Profil Admin dan Logout

**Menu:** **Profil**.

1. Periksa bagian **Profil Tersimpan**.
2. Ubah nama, username, nomor telepon, atau alamat bila diperlukan.
3. Untuk mengganti password, isi **Password Baru** dan **Konfirmasi Password Baru**. Biarkan kosong jika tidak ingin mengganti password.
4. Pilih **Simpan Profil**.
5. Periksa pemberitahuan keberhasilan atau kesalahan.

Username harus unik dan password baru minimal 8 karakter. Untuk keluar, pilih **Keluar dari Admin** pada bagian bawah navigasi atau gunakan ikon keluar di bagian atas. Sesi akan diakhiri dan aplikasi kembali ke Login.

### 4.13 Menangani Return Barang

**Menu:** **Return Barang**.

1. Buka **Return Barang** untuk melihat pengajuan dan rincian pelanggan/produk.
2. Pilih **Lihat foto bukti** untuk meninjau bukti yang dikirim.
3. Pada status **Diajukan**, periksa alasan dan bukti; isi catatan bila diperlukan, kemudian pilih **Setujui** atau **Tolak**.
4. Jika disetujui, gunakan **Catat Barang Diterima** saat barang benar-benar diterima.
5. Setelah barang diterima dan diperiksa, pilih **Layak Dijual** atau **Tidak Layak**. Isi catatan pemeriksaan bila diperlukan. Jika **Layak Dijual**, stok produk ditambahkan kembali oleh aplikasi.
6. Setelah pemeriksaan selesai, pilih **Mulai Transfer** untuk menandai proses pengembalian dana sebagai **Diproses**.
7. Setelah dana benar-benar ditransfer, isi **Referensi transfer** bila tersedia, lalu pilih **Tandai Transfer Selesai**.

Urutan status yang digunakan adalah **Diajukan**, **Disetujui**, **Barang Diterima**, lalu **Layak Dijual** atau **Tidak Layak**. Pengajuan yang ditolak berstatus **Ditolak**. Status pengembalian dana bergerak dari **Menunggu** ke **Diproses**, kemudian **Selesai**. Aplikasi membatasi perubahan jika prasyarat status belum terpenuhi.

---

## 5. Status yang Perlu Dipahami

| Jenis | Status | Keterangan |
|---|---|---|
| Pesanan | **menunggu** | Pesanan baru menunggu pembayaran. |
| Pesanan | **diproses** | Pembayaran diterima/pesanan masuk proses pemenuhan. |
| Pesanan | **selesai** | Pengiriman ditandai diterima. |
| Pesanan | **dibatalkan** | Pesanan dibatalkan. |
| Pembayaran | **menunggu** | Menunggu pemeriksaan Admin. |
| Pembayaran | **diverifikasi** | Bukti dan pembayaran diterima. |
| Pembayaran | **ditolak** | Bukti pembayaran ditolak. |
| Pengiriman | **menunggu** | Pengiriman belum dimulai. |
| Pengiriman | **diproses** | Pengiriman sedang dipersiapkan/diambil. |
| Pengiriman | **dikirim** | Barang sedang dalam pengiriman. |
| Pengiriman | **diterima** | Barang sudah diterima dan pesanan ditandai selesai. |

Status di atas adalah bidang proses yang berbeda. Verifikasi pembayaran tidak otomatis berarti barang sudah dikirim. Pesanan masuk daftar pengiriman setelah pembayaran diverifikasi dan perlu ditugaskan kepada Kurir.

---

## 6. Penanganan Kendala

| Kendala | Penyebab yang mungkin | Penanganan |
|---|---|---|
| Login Admin gagal | Username/password salah atau akun tidak aktif. | Periksa kredensial dan status akun. Gunakan pemulihan Admin melalui Admin lain. |
| Produk gagal disimpan | Kolom wajib tidak lengkap, harga/stok tidak valid, foto tidak sesuai format/ukuran. | Periksa pesan validasi, isi nama/harga/stok, dan unggah foto JPG/JPEG/PNG/WEBP maksimal 5 MB. |
| Produk tidak dapat dihapus | Produk tercatat dalam riwayat pesanan. | Pertahankan data riwayat dan sesuaikan stok/ketersediaannya. |
| Harga atau stok gagal diperbarui | Nilai tidak valid atau koneksi terganggu. | Pastikan harga nonnegatif dan stok bilangan bulat nonnegatif, lalu simpan ulang. |
| Ongkir tidak dapat diubah | Bukti pembayaran telah diunggah atau pembayaran diverifikasi. | Periksa status pembayaran; ongkir terkunci setelah tahap tersebut. |
| Pembayaran tidak dapat diverifikasi | Ongkir belum ditetapkan, bukti belum diunggah, atau pembayaran sudah diproses. | Pastikan ongkir dan bukti tersedia serta status masih menunggu. |
| Pesanan tidak muncul pada Pengiriman | Pembayaran belum diverifikasi. | Periksa Pesanan dan selesaikan verifikasi pembayaran terlebih dahulu. |
| Kurir tidak dapat login | Akun Kurir masih menunggu persetujuan atau belum aktif. | Setujui akun Kurir dari menu Petugas setelah pemeriksaan. |
| Kurir tidak muncul/penugasan gagal | Tidak ada akun Kurir yang tersedia atau pilihan tidak valid. | Periksa daftar petugas, status akun, dan pilih akun dengan Role Kurir. |
| Laporan gagal ditampilkan | Rentang tanggal tidak valid. | Pastikan tanggal Dari dan Sampai diisi dengan urutan yang benar. |
| Permintaan pemulihan Admin tidak terlihat | Tidak ada permintaan aktif, permintaan kedaluwarsa, atau Admin membuka browser yang berbeda untuk melanjutkan. | Pastikan pemohon memakai browser yang sama; Admin lain memeriksa bagian pemulihan pada Petugas. |
| Password sementara Kurir hilang | Password hanya ditampilkan sekali. | Ajukan ulang reset password Kurir dan setujui permintaan terbaru jika diperlukan. |
| Tindakan Return tidak tersedia | Status return belum memenuhi prasyarat untuk langkah selanjutnya. | Ikuti urutan pemeriksaan: setujui, catat barang diterima, periksa kondisi, kemudian proses pengembalian dana. |

Pesan keberhasilan, kesalahan, dan validasi ditampilkan pada halaman. Sebelum mengulangi tindakan yang gagal, muat ulang halaman dan periksa status agar tidak mengirim tindakan ganda. Saat meminta bantuan, sampaikan nomor pesanan dan pesan kesalahan tanpa membagikan password atau informasi rahasia.

---

## 7. Penutup

Admin berperan penting menjaga data produk, harga, stok, ongkir, pembayaran, dan pengiriman tetap akurat. Dengan menjalankan langkah secara berurutan serta memeriksa pesan dan status pada aplikasi, proses pesanan dapat ditangani dengan lebih tertib dan pelanggan memperoleh informasi yang jelas.

Semoga Manual Book ini membantu Admin menggunakan Aplikasi Samudera Fresh Strawberry dengan tepat. Terima kasih atas perhatian dan kerja sama Anda.

**— Selesai —**
