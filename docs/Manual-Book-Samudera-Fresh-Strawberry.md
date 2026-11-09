# MANUAL BOOK
## Aplikasi Samudera Fresh Strawberry

**Buku Panduan Penggunaan Aplikasi**  
**Versi dokumen:** 1.0  
**Tanggal:** 9 November 2026  
**Bahasa:** Indonesia

> **Catatan penerbitan:** Panduan ini disusun berdasarkan rute, tampilan, dan logika aplikasi yang tersedia pada project. Nama domain/URL produksi dan konfigurasi layanan email belum ditentukan oleh kode, sehingga perlu dikonfirmasi sebelum dokumen diterbitkan sebagai panduan operasional final. Tampilan dapat berbeda mengikuti ukuran layar dan perubahan versi aplikasi. Screenshot belum disertakan dalam bahan penyusunan ini.

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Gambaran Umum Penggunaan Aplikasi](#2-gambaran-umum-penggunaan-aplikasi)
3. [Bab I — Panduan Pelanggan](#3-bab-i--panduan-pelanggan)
4. [Bab II — Panduan Admin](#4-bab-ii--panduan-admin)
5. [Bab III — Panduan Kurir](#5-bab-iii--panduan-kurir)
6. [Alur Kerja Antaraktor](#6-alur-kerja-antaraktor)
7. [Penanganan Kendala dan Kesalahan](#7-penanganan-kendala-dan-kesalahan)
8. [Penutup](#8-penutup)

---

## 1. Pendahuluan

### 1.1 Kata Pengantar

Puji syukur ke hadirat Tuhan Yang Maha Esa. Manual Book Aplikasi Samudera Fresh Strawberry ini disusun sebagai panduan bagi pelanggan, Admin, dan Kurir dalam menggunakan aplikasi. Panduan menjelaskan fungsi serta urutan kerja berdasarkan fitur yang tersedia pada project, agar pengguna dapat menjalankan tugasnya dengan benar dan memahami tindak lanjut dari setiap tindakan.

### 1.2 Pengenalan Aplikasi

Samudera Fresh Strawberry adalah aplikasi berbasis web untuk menampilkan katalog produk stroberi dan produk olahannya, menerima pesanan pelanggan, mencatat pembayaran melalui transfer bank, serta membantu Admin dan Kurir mengelola proses pemenuhan pesanan dan pengiriman.

Pengguna mengakses aplikasi melalui browser. Aplikasi menyediakan halaman publik dan area kerja yang berbeda sesuai peran:

| Aktor | Fungsi utama |
|---|---|
| **Pelanggan** | Menjelajahi produk, membuat pesanan, mengunggah bukti transfer, memantau pesanan, dan mengelola profil. |
| **Admin** | Mengelola produk, harga dan stok, pesanan, ongkir, verifikasi pembayaran, pengiriman, laporan, petugas, profil, serta pengajuan Return Barang. |
| **Kurir** | Melihat pengiriman yang ditugaskan kepadanya, menggunakan informasi penerima, dan memperbarui kemajuan pengiriman. |

### 1.3 Latar Belakang dan Tujuan

Proses penjualan yang hanya mengandalkan komunikasi manual dapat menyulitkan pencatatan stok, pesanan, pembayaran, dan pengiriman. Aplikasi ini menyediakan alur terpusat agar pelanggan dapat memesan produk melalui katalog, sementara petugas dapat melihat dan menindaklanjuti data pesanan melalui area kerja masing-masing.

Tujuan aplikasi adalah:

1. Menyediakan katalog produk dan informasi harga serta stok.
2. Memudahkan pelanggan membuat pesanan dan mengirim bukti transfer.
3. Membantu Admin memeriksa transaksi, mengelola persediaan, dan mengatur pengiriman.
4. Memberi Kurir akses terhadap rincian tugas pengiriman yang ditugaskan.
5. Memungkinkan pelanggan dan petugas melihat perubahan status yang tercatat dalam aplikasi.

### 1.4 Manfaat bagi UMKM

- Informasi produk, harga, dan persediaan dapat dikelola melalui satu aplikasi.
- Rincian pesanan, penerima, alamat, ongkir, dan pembayaran tercatat bersama.
- Admin dapat memusatkan pemeriksaan pembayaran dan penugasan Kurir.
- Pelanggan dapat melihat status pesanan tanpa harus mengandalkan ingatan terhadap percakapan sebelumnya.
- Data penjualan dapat ditinjau dan diunduh oleh Admin untuk periode tertentu.

### 1.5 Fungsi Utama

Fungsi yang tersedia pada project mencakup katalog dan pencarian produk; keranjang; checkout; penghitungan ongkir berdasarkan wilayah dan alamat; pembayaran transfer dengan pilihan bank pengirim; unggah bukti pembayaran; riwayat dan detail pesanan; pengelolaan produk, stok dan tarif ongkir; verifikasi pembayaran; penugasan Kurir; pembaruan status pengiriman; laporan penjualan dan ekspor Excel; profil serta pemulihan akun; dan Return Barang.

### 1.6 Persiapan Sebelum Menggunakan

1. Siapkan perangkat seperti komputer, tablet, atau telepon pintar.
2. Pastikan browser modern dan koneksi internet berfungsi.
3. Siapkan username dan password yang terdaftar. Pelanggan dapat mendaftar sendiri; akun Admin disediakan/dikelola oleh Admin yang berwenang. Calon Kurir dapat mengajukan pendaftaran dan menunggu persetujuan Admin.
4. Pelanggan perlu menyiapkan alamat lengkap yang memuat kecamatan/kota, nomor HP penerima, serta metode untuk melakukan transfer.
5. Siapkan file gambar bukti transfer apabila akan melakukan pembayaran. Format yang diterima adalah JPG, JPEG, PNG, atau WEBP dengan ukuran maksimum 5 MB.
6. Untuk verifikasi email pelanggan, alamat email harus dapat menerima pesan dan layanan email aplikasi harus dikonfigurasi.

### 1.7 Cara Mengakses Aplikasi

1. Buka browser pada perangkat.
2. Ketik **alamat domain aplikasi yang diberikan oleh pengelola** pada bilah alamat, lalu tekan Enter. Alamat domain resmi tidak ditetapkan di dalam source code project dan perlu diinformasikan oleh pengelola saat aplikasi dipasang.
3. Halaman awal aplikasi akan terbuka. Pilih menu atau tautan yang sesuai dengan kebutuhan.
4. Untuk masuk, buka halaman **Login**. Rute `/admin-login` dan `/kurir-login` pada project mengarahkan pengguna ke halaman Login yang sama; hak akses ditentukan dari akun yang digunakan.

Jangan menggunakan alamat lokal pengembangan sebagai alamat layanan publik kecuali aplikasi memang sedang dijalankan pada komputer tersebut.

---

## 2. Gambaran Umum Penggunaan Aplikasi

Urutan kerja umum aplikasi adalah sebagai berikut:

1. **Pelanggan** membuka katalog, memilih produk, mengisi keranjang, lalu mengirim pesanan melalui Checkout.
2. Aplikasi menyimpan pesanan dengan status awal **menunggu**, menghitung ongkir dari wilayah yang dikenali pada alamat menggunakan tarif yang tersedia, dan menampilkan halaman **Pembayaran Transfer**.
3. Pelanggan memilih bank yang digunakan untuk transfer, membayar sesuai total yang ditampilkan, lalu mengunggah bukti pembayaran.
4. **Admin** memeriksa rincian pesanan, memastikan ongkir telah tersedia, memeriksa bukti transfer, kemudian memilih **Terima** atau **Tolak**.
5. Jika pembayaran diterima, status pembayaran menjadi **diverifikasi** dan pesanan yang masih menunggu berpindah menjadi **diproses**.
6. Admin membuka **Pengiriman** dan menugaskan Kurir. Daftar pengiriman Admin berisi pesanan dengan pembayaran yang sudah diverifikasi.
7. **Kurir** membuka tugas yang ditugaskan kepadanya, melihat alamat serta kontak penerima, lalu memperbarui status bertahap hingga **diterima**.
8. Pelanggan melihat perkembangan melalui menu **Pesanan** dan detail pesanan.

Hubungan antaraktor bersifat berurutan: tindakan pelanggan membuat data yang ditinjau Admin; keputusan pembayaran Admin memungkinkan proses pemenuhan dan pengiriman; pembaruan Kurir tercatat sehingga status pesanan dapat dilihat pelanggan. Aplikasi tidak menggantikan kebutuhan komunikasi operasional apabila pengguna mengalami masalah, alamat tidak dikenali, atau bukti transaksi perlu diperiksa ulang.

---

## 3. Bab I — Panduan Pelanggan

### 3.1 Membuka Aplikasi dan Mendaftar

**Tujuan:** Membuat akun pelanggan agar pesanan, pembayaran, dan profil dapat dikelola.

1. Buka domain aplikasi pada browser.
2. Pilih **Login**, kemudian pilih tautan **Daftar di sini** untuk pendaftaran pelanggan.
3. Isi nama pelanggan, username, password, dan email jika ingin menambahkannya. Email bersifat opsional pada formulir pendaftaran.
4. Kirim formulir pendaftaran.
5. Jika berhasil, aplikasi mengarahkan ke halaman Login dan menampilkan konfirmasi. Jika email diisi dan layanan email tersedia, tautan verifikasi dikirimkan ke alamat tersebut.

Username harus belum digunakan. Password pendaftaran pelanggan minimal 6 karakter. Username atau email yang sudah terdaftar akan ditolak; periksa kembali nilai yang dimasukkan dan gunakan nilai lain.

### 3.2 Login

**Tujuan:** Masuk ke akun Pelanggan.

1. Pada halaman **Login**, masukkan username atau email akun dan password.
2. Pilih tombol **Login**.
3. Jika berhasil sebagai pelanggan, aplikasi membuka halaman onboarding/beranda pelanggan.
4. Jika gagal, pesan kesalahan akan ditampilkan. Pastikan ejaan username/email dan password benar, serta gunakan akun pelanggan yang sudah terdaftar.

Halaman Login yang sama dapat digunakan akun petugas. Sistem mengarahkan Admin dan Kurir ke dashboard sesuai peran masing-masing. Akun Kurir yang masih berstatus menunggu persetujuan tidak dapat digunakan untuk masuk.

### 3.3 Mengenal Beranda dan Navigasi Pelanggan

Navigasi pelanggan menampilkan **Beranda**, **Produk**, **Pesanan**, **Profil**, dan ikon keranjang. Halaman katalog bertajuk **Beranda Pelanggan** juga menampilkan daftar produk dan filter. Pilih **Produk** untuk kembali ke katalog, **Pesanan** untuk membuka riwayat, **Profil** untuk pengaturan akun, atau ikon keranjang untuk melihat barang yang dipilih.

Beranda pelanggan berfungsi sebagai area untuk melihat produk; project tidak menampilkan ringkasan metrik pelanggan terpisah seperti dashboard Admin.

### 3.4 Melihat dan Mencari Produk

**Tujuan:** Meninjau informasi produk sebelum membeli.

1. Pilih menu **Produk**.
2. Telusuri daftar kartu produk. Gunakan pencarian dan filter yang tersedia jika diperlukan: kata kunci, kategori, rentang harga, atau rentang berat.
3. Pilih **Terapkan Filter** untuk menerapkan kriteria yang dimasukkan.
4. Pilih kartu/nama produk untuk membuka detail.
5. Pada detail, periksa nama, foto, harga, deskripsi, berat (jika tercantum), dan stok yang tersedia.

Kategori filter yang digunakan aplikasi adalah produk **segar**, **keripik**, dan **selai**. Jika hasil tidak ditemukan, hapus atau longgarkan filter. Stok yang tampil dapat berubah apabila ada transaksi atau pembaruan Admin.

### 3.5 Memilih Produk dan Mengisi Keranjang

**Tujuan:** Menyimpan pilihan produk sementara sebelum Checkout.

1. Dari katalog, buka detail produk yang diinginkan.
2. Tentukan jumlah menggunakan pengendali jumlah pada halaman detail.
3. Pastikan jumlah tidak melebihi stok tersedia, lalu pilih **Tambah**.
4. Pesan berhasil akan ditampilkan dan jumlah produk tersimpan di keranjang.
5. Buka ikon keranjang untuk melanjutkan pemeriksaan.

Jika stok tidak cukup atau produk tidak ditemukan, aplikasi menampilkan pesan dan tidak menambahkan jumlah yang tidak tersedia. Coba jumlah yang lebih kecil atau pilih produk lain.

### 3.6 Mengubah atau Menghapus Isi Keranjang

**Tujuan:** Menyesuaikan jumlah dan pilihan sebelum pesanan dibuat.

1. Pilih ikon keranjang. Halaman **Keranjang Belanja** menampilkan produk, jumlah, harga, dan ringkasan subtotal.
2. Untuk mengubah jumlah, pilih tombol **+** atau **−** pada baris produk. Perubahan dikirim saat tombol dipilih; tombol **−** tidak aktif jika jumlah sudah 1.
3. Untuk menghapus barang, pilih tombol **Hapus** pada baris produk.
4. Periksa kembali subtotal dan daftar produk.
5. Jika keranjang sudah benar, pilih **Checkout**. Checkout hanya dapat dilanjutkan oleh pelanggan yang sudah login; jika diarahkan ke Login, masuk terlebih dahulu lalu buka keranjang kembali.

Jumlah harus berupa bilangan bulat minimal 1 dan tidak melebihi stok. Jika keranjang kosong, tambahkan produk dari katalog terlebih dahulu. Keranjang disimpan pada sesi browser; apabila sesi berakhir, pelanggan mungkin perlu mengisi ulang keranjang.

### 3.7 Checkout dan Data Pengiriman

**Tujuan:** Membuat pesanan dan mengirimkan rincian penerima kepada Admin/Kurir.

1. Pada halaman Checkout, pastikan produk dan jumlah pada **Ringkasan Pesanan** sudah benar.
2. Isi **Nama Penerima**.
3. Isi **Alamat Lengkap (cantumkan kecamatan/kota)** dengan alamat yang dapat digunakan Kurir.
4. Isi **Nomor HP** penerima yang aktif.
5. Isi **Catatan (Opsional)** bila ada petunjuk tambahan.
6. Periksa wilayah tujuan, ongkir, dan total pada ringkasan. Wilayah ditentukan dari teks alamat dan tarif wilayah yang dikonfigurasi Admin.
7. Pilih **Buat Pesanan & Lanjut Bayar**.

Jika alamat tidak dapat dipasangkan dengan wilayah ongkir yang aktif, atau daftar tarif belum tersedia, pesanan tidak dapat dilanjutkan sampai masalah wilayah/tarif diselesaikan. Pastikan alamat mencantumkan kecamatan/kota secara jelas. Bila tetap gagal, hubungi pengelola/Admin; jangan memperkirakan ongkir sendiri.

### 3.8 Memahami Rincian Pembayaran

Total pembayaran terdiri dari **Subtotal Produk + Ongkir**. Wilayah tujuan dan ongkir ditampilkan pada Checkout dan halaman pembayaran. Ongkir mengikuti tarif wilayah yang disimpan di aplikasi; jumlah akhir dapat ditinjau Admin sebelum pembayaran dan tidak dapat diubah setelah bukti pembayaran diunggah atau pembayaran diverifikasi.

Setelah Checkout berhasil, aplikasi membuat nomor pesanan dan membuka halaman **Pembayaran Transfer**. Jika ongkir masih menunggu penetapan Admin, ikuti informasi pada halaman tersebut dan tunggu sampai nominal tersedia sebelum membayar.

### 3.9 Memilih Bank dan Melakukan Pembayaran

**Tujuan:** Mencatat bank pengirim dan membayar sesuai rincian pesanan.

1. Pada bagian **Bank Pengirim**, pilih bank yang digunakan: **BCA**, **BRI**, **BNI**, atau **Mandiri**.
2. Pilih **Simpan Bank Pengirim**.
3. Baca nomor rekening tujuan dan nama pemilik yang ditampilkan langsung pada halaman aplikasi. Pilihan bank adalah bank pengirim; transfer tetap ditujukan ke rekening BCA yang ditampilkan oleh aplikasi.
4. Transfer sebesar **Total pembayaran** yang tercantum, termasuk ongkir.
5. Simpan bukti transaksi.

Jangan menggunakan nomor rekening dari sumber lain atau mengubah nominal berdasarkan perkiraan. Jika bank belum dapat disimpan, periksa bahwa ongkir sudah tersedia dan bukti pembayaran belum terunggah. Bank pengirim tidak dapat diubah setelah bukti pembayaran dikirim.

### 3.10 Mengunggah Bukti Pembayaran

**Tujuan:** Mengirim bukti transfer untuk diperiksa Admin.

1. Pada halaman **Pembayaran Transfer**, pilih **Upload Bukti Pembayaran**. Jika bukti pernah dikirim dan pembayaran belum diverifikasi, pilih **Lihat / Unggah Ulang Bukti**.
2. Pilih file bukti dari perangkat. Format yang diterima JPG, JPEG, PNG, atau WEBP, maksimal 5 MB.
3. Pilih **Kirim**.
4. Setelah berhasil, status pembayaran menunggu verifikasi Admin. Jika mengganti bukti sebelum pembayaran diverifikasi, bukti sebelumnya akan diganti.

Bukti tidak dapat diunggah ulang setelah pembayaran diverifikasi. Bila format, ukuran, atau isi file tidak sesuai, aplikasi menampilkan kesalahan; pilih gambar yang benar dan coba kembali.

### 3.11 Melihat Status dan Riwayat Pesanan

**Tujuan:** Memantau pembayaran, pemrosesan, dan pengiriman.

1. Pilih menu **Pesanan**.
2. Daftar riwayat menampilkan pesanan terbaru beserta status ringkas.
3. Pilih salah satu pesanan untuk membuka detail, rincian produk, total, informasi pembayaran, dan informasi pengiriman yang tersedia.
4. Kembali ke detail pesanan setelah Admin atau Kurir memperbarui proses untuk melihat status terbaru.

Status ringkas yang ditampilkan dapat berupa:

| Status pada tampilan pelanggan | Arti umum |
|---|---|
| **Menunggu Pembayaran** | Pembayaran belum diverifikasi. |
| **Pembayaran Ditolak** | Admin menolak bukti; periksa kembali halaman pembayaran dan unggah bukti yang benar jika tersedia. |
| **Pembayaran Dikonfirmasi** | Pembayaran diverifikasi. |
| **Diproses** | Pesanan sedang disiapkan atau menunggu tahap pengiriman. |
| **Dalam Pengiriman** | Pengiriman telah berjalan. |
| **Selesai** | Pengiriman diterima atau pesanan ditandai selesai. |
| **Dibatalkan** | Pesanan berstatus dibatalkan. Hubungi Admin jika status ini tidak diharapkan. |

Nilai status transaksi di penyimpanan aplikasi dapat ditampilkan dalam bentuk label yang lebih mudah dibaca. Untuk rincian, perhatikan status pembayaran dan status pengiriman pada halaman detail.

### 3.12 Mengelola Profil dan Email

**Tujuan:** Memperbarui informasi akun.

1. Pilih menu **Profil**.
2. Tinjau bagian **Pesanan Saya**, **Alamat Saya**, dan **Metode Pembayaran** untuk melihat informasi ringkas yang tersedia.
3. Buka **Pengaturan Akun**, lalu ubah nama, username, atau email jika diperlukan.
4. Untuk mengganti password, isi password saat ini, password baru, dan konfirmasi password baru.
5. Pilih **Simpan Pengaturan Akun**.

Password baru minimal 8 karakter. Jika mengganti email, alamat tersebut perlu diverifikasi kembali. Ikuti tautan yang dikirim ke email; bila pesan tidak datang, periksa folder spam dan gunakan pengiriman ulang verifikasi dari Profil. Pengiriman email bergantung pada konfigurasi layanan email aplikasi.

### 3.13 Kode Pemulihan dan Lupa Password

**Tujuan:** Menyiapkan cara pemulihan akun apabila pelanggan lupa password.

**Menerbitkan kode saat masih bisa login:**

1. Buka **Profil**, lalu pilih **Terbitkan Kode Pemulihan**.
2. Pilih tombol **Terbitkan Kode Pemulihan** pada halaman tersebut.
3. Simpan kode yang ditampilkan di tempat aman. Kode berlaku selama 30 hari, hanya ditampilkan satu kali, dan penerbitan kode baru akan membatalkan kode sebelumnya.
4. Pilih **Selesai, lanjut ke Lupa Password** untuk keluar dari sesi dan membuka alur pemulihan.

**Mengatur password ketika tidak dapat login:**

1. Pada halaman Login, buka tautan **Lupa Password**.
2. Masukkan username pelanggan dan kode pemulihan yang masih berlaku.
3. Jika verifikasi berhasil, buat password baru minimal 8 karakter dan konfirmasikan.
4. Login kembali menggunakan password baru.

Kode yang salah, kedaluwarsa, telah digunakan, atau sementara terkunci tidak dapat digunakan. Kode hanya dapat dipakai satu kali. Jika kode tidak tersimpan atau tidak berlaku, pengguna perlu mengakses fitur penerbitan kode saat masih login; jika tidak dapat login, hubungi pengelola untuk bantuan di luar aplikasi. Fitur pemulihan pelanggan ini tidak berlaku untuk akun Admin atau Kurir.

### 3.14 Keluar dari Akun

Pada **Profil**, buka bagian **Keluar**, lalu pilih **Keluar dari akun**. Pastikan keluar setelah menggunakan perangkat bersama. Setelah logout, aplikasi mengakhiri sesi dan mengarahkan kembali ke Login.

### 3.15 Return Barang

Fitur Return Barang tersedia untuk pesanan yang sudah diterima/selesai dan pembayarannya telah diverifikasi, selama pesanan tidak dibatalkan.

1. Buka **Pesanan**, kemudian pilih detail pesanan yang memenuhi syarat.
2. Ajukan return dari detail pesanan, pilih rincian produk/jumlah sesuai form, dan unggah foto bukti yang diminta.
3. Kirim pengajuan dan pantau statusnya pada detail pesanan.
4. Pengajuan menunggu pemeriksaan Admin. Ikuti keputusan atau catatan Admin yang ditampilkan.

Pengajuan ditolak apabila syarat kelayakan tidak terpenuhi atau jumlah yang diajukan melebihi jumlah produk yang masih dapat direturn. Keputusan dan proses pengembalian dana ditangani Admin.

---

## 4. Bab II — Panduan Admin

### 4.1 Membuka Aplikasi dan Login Admin

1. Buka domain aplikasi melalui browser.
2. Pilih **Login** atau akses tautan admin yang disediakan pengelola. Rute khusus `/admin-login` mengarahkan ke halaman Login yang sama.
3. Masukkan username dan password akun Admin.
4. Pilih **Login**. Jika berhasil, dashboard Admin terbuka.

Akun Admin dibuat/dikelola oleh petugas berwenang, bukan melalui pendaftaran pelanggan. Akun petugas harus aktif. Jika login gagal, pastikan Anda menggunakan kredensial Admin dan minta bantuan Admin lain/pengelola untuk pemulihan.

### 4.2 Dashboard Admin

Menu **Dashboard** menampilkan ringkasan jumlah produk, jumlah pesanan, jumlah pesanan berstatus menunggu, nilai penjualan dari pembayaran yang diverifikasi, daftar pesanan terbaru, serta grafik penjualan harian selama tujuh hari terakhir. Angka penjualan hanya menghitung pembayaran yang berstatus diverifikasi. Dashboard membantu menentukan pekerjaan yang perlu ditindaklanjuti, tetapi perubahan data dilakukan melalui menu terkait.

### 4.3 Mengelola Produk

**Menu:** **Produk**.

1. Buka **Produk** untuk melihat daftar produk. Gunakan pencarian nama produk bila diperlukan.
2. Untuk menambah, pilih ikon **＋** (Tambah produk), isi nama, deskripsi, harga, stok, berat gram bila diketahui, dan foto bila tersedia.
3. Foto yang dapat diunggah berupa JPG, JPEG, PNG, atau WEBP, maksimal 5 MB.
4. Simpan formulir. Produk baru akan muncul pada daftar dan katalog pelanggan.
5. Untuk mengubah, pilih ikon edit pada baris produk, perbarui informasi yang diperlukan, lalu simpan formulir.
6. Untuk menghapus, gunakan tindakan hapus pada produk yang tidak memiliki catatan di riwayat pesanan.

Nama produk, harga, dan stok wajib diisi; deskripsi, berat, dan foto bersifat opsional. Produk yang telah tercatat dalam detail pesanan tidak dapat dihapus. Pesan keberhasilan atau kesalahan ditampilkan setelah tindakan.

### 4.4 Harga dan Stok

**Menu:** **Harga & Stok**.

1. Buka **Harga & Stok**.
2. Temukan produk pada tabel (halaman dapat terdiri dari beberapa halaman).
3. Ubah nilai harga dan/atau stok pada baris produk.
4. Pilih ikon edit/simpan pada baris tersebut.
5. Periksa pemberitahuan keberhasilan dan nilai terbaru.

Harga harus angka nonnegatif dan stok harus bilangan bulat nonnegatif. Pastikan pembaruan stok mencerminkan persediaan aktual, karena pelanggan tidak dapat menambahkan jumlah melebihi stok.

### 4.5 Melihat dan Memproses Pesanan

**Menu:** **Pesanan**.

1. Buka **Pesanan** untuk melihat daftar pesanan terbaru, pelanggan, tanggal, wilayah, total, dan status.
2. Pilih satu baris untuk melihat **Detail Pesanan (Admin)**.
3. Periksa nama pelanggan, alamat, produk, jumlah, subtotal, wilayah, ongkir, dan total pembayaran.
4. Jika ongkir belum ditetapkan, isi **Tetapkan ongkir (Rp)** lalu pilih **Simpan Ongkir**. Ongkir dibatasi paling tinggi sebesar subtotal produk.
5. Setelah bukti pembayaran tersedia, pilih **Verifikasi Pembayaran** untuk membuka halaman pemeriksaan.
6. Setelah pembayaran diterima, aplikasi mengubah status pesanan yang masih **menunggu** menjadi **diproses**. Status pesanan menjadi **selesai** setelah pengiriman ditandai **diterima**. Status yang tersedia pada data pesanan juga mencakup **dibatalkan**; tindak lanjuti pembatalan sesuai prosedur operasional pengelola.

Ongkir tidak dapat diubah setelah bukti pembayaran diunggah atau pembayaran diverifikasi. Apabila halaman menampilkan bahwa ongkir masih menunggu Admin, selesaikan penetapan ongkir sebelum memverifikasi pembayaran.

### 4.6 Tarif Ongkir

**Menu:** **Tarif Ongkir**.

1. Buka **Tarif Ongkir** untuk melihat wilayah dan tarif yang dikonfigurasi.
2. Ubah tarif pada wilayah yang sesuai.
3. Simpan perubahan dan pastikan pemberitahuan berhasil muncul.

Alamat Checkout dipetakan ke wilayah berdasarkan teks alamat, termasuk wilayah Ciwidey & Rancabali, Pasirjambu & Soreang, Bandung Raya, atau Luar Bandung Raya. Periksa penamaan wilayah yang ditampilkan pada aplikasi dan jangan menonaktifkan/mengubah wilayah tanpa memastikan proses checkout tetap memiliki tarif yang aktif.

### 4.7 Memeriksa dan Memverifikasi Pembayaran

**Menu:** **Pesanan** → pilih pesanan → **Verifikasi Pembayaran**.

1. Pastikan ongkir telah ditetapkan.
2. Periksa subtotal produk, wilayah, ongkir, total, bank pengirim, nama pelanggan, dan status.
3. Buka/lihat gambar pada bagian **Bukti Transfer** dan cocokkan dengan nominal serta informasi transaksi.
4. Jika sesuai, pilih **Terima**. Status pembayaran berubah menjadi **diverifikasi**. Jika status pesanan masih **menunggu**, aplikasi otomatis mengubahnya menjadi **diproses** dan membuat nomor struk.
5. Jika tidak sesuai, pilih **Tolak**. Pelanggan akan melihat status **Pembayaran Ditolak** dan dapat memperbaiki/mengunggah ulang bukti selama pembayaran belum diverifikasi.
6. Setelah pembayaran diverifikasi, tombol **Cetak Struk** tersedia.

Tindakan Terima/Tolak hanya tersedia untuk pembayaran berstatus menunggu yang sudah memiliki bukti. Jika bukti belum diunggah, ongkir belum ditentukan, atau pembayaran sudah diproses, aplikasi menolak perubahan dan menampilkan keterangan.

### 4.8 Menentukan Kurir dan Memperbarui Pengiriman

**Menu:** **Pengiriman**.

1. Buka **Pengiriman** untuk melihat pesanan dengan pembayaran terverifikasi.
2. Temukan penerima dan alamat pesanan. Jika belum memiliki tugas, label **BELUM DITUGASKAN** akan ditampilkan.
3. Pilih Kurir pada daftar **Pilih kurir**, kemudian pilih **Tugaskan**.
4. Pastikan nama Kurir dan status pengiriman terlihat pada baris pesanan.
5. Admin dapat memperbarui status pengiriman dari pilihan **Menunggu**, **Diproses**, **Dikirim**, atau **Diterima**, lalu memilih **Simpan**.

Pilih akun Kurir yang telah disetujui dan aktif. Hanya pesanan dengan pembayaran **diverifikasi** yang ditampilkan untuk ditugaskan. Perubahan ke **Diterima** juga menandai pesanan sebagai **selesai**. Sebaiknya status diubah mengikuti perkembangan aktual dan urutan kerja.

### 4.9 Laporan dan Ekspor

**Menu:** **Laporan**.

1. Buka **Laporan**.
2. Masukkan tanggal **Dari** dan **Sampai** sesuai periode yang ingin diperiksa. Jika tidak diisi, aplikasi menggunakan awal bulan hingga tanggal hari ini.
3. Terapkan filter periode untuk melihat jumlah pesanan, total penjualan terverifikasi, pesanan yang belum dibayar/ belum diverifikasi, daftar transaksi, serta grafik bulanan.
4. Pilih **Unduh Excel (.xlsx)** untuk mengunduh laporan dalam format Excel.

Tanggal akhir tidak boleh lebih awal daripada tanggal awal. Total penjualan hanya menghitung pembayaran berstatus diverifikasi; periksa rentang tanggal sebelum membandingkan hasil laporan.

### 4.10 Petugas dan Pemulihan Akun

**Menu:** **Petugas**.

Menu ini digunakan untuk menambah petugas Admin/Kurir, menyetujui pendaftaran Kurir, serta menangani permintaan pemulihan password.

- **Mendaftarkan petugas:** isi nama, username, password beserta konfirmasi, data kontak/alamat bila diperlukan, dan peran Admin atau Kurir; simpan. Password minimal 8 karakter.
- **Menyetujui Kurir:** tinjau petugas berstatus menunggu persetujuan, lalu gunakan **Setujui Akun** setelah memastikan identitas/kelayakannya. Setelah disetujui, akun menjadi aktif.
- **Pemulihan password Admin:** Admin yang lupa password mengajukan permintaan melalui halaman pemulihan Admin. Admin lain memeriksa permintaan pada bagian **Permintaan Pemulihan Password Admin** di menu Petugas, kemudian memilih **Setujui Pemulihan** atau **Tolak**. Admin pemohon melanjutkan pembuatan password setelah disetujui, menggunakan browser/sesi yang mengajukan permintaan.
- **Pemulihan password Kurir:** permintaan Kurir tampil pada data petugas. Admin dapat memilih **Verifikasi & Setujui Reset** atau **Tolak**. Jika disetujui, aplikasi menampilkan password sementara satu kali pada hasil tindakan. Serahkan langsung kepada Kurir melalui saluran yang aman dan jangan menyimpannya atau membagikannya secara terbuka.

Kurir yang masuk dengan password sementara wajib menggantinya sebelum menggunakan dashboard. Perlakukan password sementara dan permintaan pemulihan sebagai informasi rahasia.

### 4.11 Profil Admin dan Logout

1. Pilih **Profil**.
2. Ubah nama, username, nomor telepon, alamat, dan/atau password baru sesuai kebutuhan.
3. Jika mengganti password, isi password baru dan konfirmasi. Password baru minimal 8 karakter.
4. Pilih **Simpan Profil** dan periksa pemberitahuan.
5. Untuk mengakhiri sesi, gunakan kontrol keluar pada sidebar/header.

Jika password baru dibiarkan kosong, password lama tidak diubah. Username tidak dapat sama dengan username petugas lain.

### 4.12 Return Barang

**Menu:** **Return Barang**.

1. Buka **Return Barang** untuk melihat pengajuan, data pelanggan/produk, foto bukti, dan status return.
2. Pada status **Diajukan**, periksa data/foto, masukkan catatan bila diperlukan, lalu pilih **Setujui** atau **Tolak**.
3. Untuk pengajuan yang disetujui, catat kedatangan barang melalui **Catat Barang Diterima**.
4. Setelah barang diterima, periksa dan pilih **Layak Dijual** atau **Tidak Layak**. Isi catatan pemeriksaan jika diperlukan. Jika layak dijual, stok produk ditambahkan kembali oleh aplikasi.
5. Setelah pemeriksaan selesai, pengembalian dana dapat ditandai **Diproses** melalui **Mulai Transfer**.
6. Setelah transfer benar-benar dilakukan, masukkan referensi transfer jika tersedia lalu pilih **Tandai Transfer Selesai**.

Status return yang digunakan adalah **Diajukan**, **Disetujui**, **Ditolak**, **Barang Diterima**, **Layak Dijual**, dan **Tidak Layak**. Status pengembalian dana bergerak dari **Menunggu**, **Diproses**, menjadi **Selesai**. Ikuti urutan ini; aplikasi tidak mengizinkan langkah berikutnya sebelum prasyaratnya terpenuhi.

---

## 5. Bab III — Panduan Kurir

### 5.1 Pendaftaran dan Login Kurir

Jika belum memiliki akun, buka halaman Login lalu pilih tautan pendaftaran Kurir (atau rute pendaftaran Kurir yang disediakan aplikasi).

1. Isi nama dan username yang belum digunakan.
2. Buat password minimal 8 karakter, lalu isi konfirmasinya.
3. Nomor telepon dan alamat dapat diisi sebagai informasi tambahan.
4. Kirim permohonan. Akun berstatus menunggu persetujuan Admin.
5. Setelah Admin menyetujui akun, buka halaman Login dan masuk menggunakan username serta password.

Akun Kurir yang masih menunggu persetujuan tidak dapat masuk. Jika sudah memiliki akun, halaman `/kurir-login` mengarahkan ke Login bersama. Setelah berhasil, Kurir masuk ke **Dashboard**. Jika menggunakan password sementara hasil reset, aplikasi terlebih dahulu meminta penggantian password.

### 5.2 Dashboard Kurir

Menu **Dashboard** menampilkan ringkasan jumlah pengiriman hari ini, jumlah berstatus **dikirim**, jumlah **diterima**, dan beberapa tujuan dari tugas yang belum diterima. Ringkasan hanya berkaitan dengan pengiriman yang ditugaskan kepada akun Kurir yang sedang masuk.

Jika belum ada tugas, daftar tujuan dapat kosong. Buka **Pengiriman** untuk melihat daftar aktual. Bila belum ada tugas di sana juga, hubungi Admin untuk memastikan pembayaran pesanan sudah diverifikasi dan penugasan telah dibuat.

### 5.3 Melihat Daftar dan Detail Pengiriman

**Menu:** **Pengiriman**.

1. Buka **Pengiriman** untuk melihat tugas yang ditugaskan kepada Anda.
2. Periksa nomor pesanan, nama penerima, alamat, dan status pada daftar.
3. Pilih salah satu tugas untuk membuka **Detail Pengiriman (Kurir)**.
4. Periksa nama penerima, nomor telepon, alamat lengkap, catatan pelanggan bila tersedia, dan nomor pesanan.
5. Hubungi penerima menggunakan informasi yang tampil apabila diperlukan untuk pelaksanaan pengiriman.

Informasi pengiriman merupakan data pesanan yang dimasukkan pelanggan. Jika alamat/nomor tidak lengkap atau tidak sesuai, koordinasikan dengan Admin sebelum menandai pengiriman selesai.

### 5.4 Memperbarui Status Pengiriman

Status Kurir bergerak satu tahap dalam satu waktu:

| Status saat ini | Tindakan berikutnya pada detail |
|---|---|
| **menunggu** | Pilih **Update Status** untuk menjadi **diproses** (tugas diambil/disiapkan). |
| **diproses** | Pilih **Update Status** untuk menjadi **dikirim**. |
| **dikirim** | Setelah barang diterima pelanggan, pilih **Konfirmasi Diterima**. |
| **diterima** | Pengiriman selesai; pilih **Lihat Konfirmasi** untuk melihat konfirmasi. |

1. Buka detail pengiriman.
2. Pastikan tindakan berikutnya sesuai dengan kondisi nyata.
3. Tekan **Update Status** atau **Konfirmasi Diterima**.
4. Periksa notifikasi. Setelah diterima, aplikasi menampilkan halaman konfirmasi dan pesanan pelanggan menjadi selesai.

Kurir tidak dapat melewati tahapan atau mengubah status dari **diterima** kembali. Jika aplikasi menolak perubahan, muat ulang detail untuk memeriksa status terbaru; bila masih tidak sesuai, hubungi Admin. Jangan mengonfirmasi penerimaan sebelum barang benar-benar sampai.

### 5.5 Profil Kurir

**Menu:** **Profil**.

1. Periksa profil tersimpan.
2. Ubah nama, username, nomor telepon, atau alamat bila diperlukan.
3. Untuk mengganti password, isi password baru dan konfirmasi; kosongkan kedua kolom password jika tidak ingin mengganti.
4. Pilih **Simpan Profil**.

Password baru minimal 8 karakter. Username harus unik. Pemberitahuan akan menunjukkan apakah perubahan tersimpan.

### 5.6 Pemulihan Password Kurir

1. Pada halaman Login, buka tautan pemulihan password Kurir.
2. Masukkan username dan kirim permintaan.
3. Jika akun cocok, permintaan diteruskan kepada Admin untuk verifikasi.
4. Admin menyetujui atau menolak dari menu **Petugas**. Jika disetujui, Admin memberikan password sementara secara langsung.
5. Login dengan password sementara. Aplikasi mewajibkan Anda membuat password baru sebelum membuka dashboard.

Untuk keamanan, jangan meminta atau mengirim password melalui kolom catatan pesanan. Jika permintaan tidak ditindaklanjuti, hubungi Admin secara langsung.

### 5.7 Logout

Pilih **Keluar dari Kurir** pada sidebar atau kontrol keluar di bagian atas. Aplikasi mengakhiri sesi dan mengarahkan ke halaman Login. Selalu logout setelah selesai, khususnya pada perangkat bersama.

---

## 6. Alur Kerja Antaraktor

### 6.1 Urutan Pemesanan hingga Pengiriman

1. **Pelanggan memilih produk.** Pelanggan membuka katalog, memeriksa harga/stok, menentukan jumlah, dan mengisi keranjang.
2. **Pelanggan membuat pesanan.** Pada Checkout, pelanggan mengisi nama penerima, alamat, nomor HP, dan catatan opsional. Aplikasi menyimpan rincian produk serta menghitung subtotal dan ongkir berdasarkan wilayah yang dikenali dan tarif aktif.
3. **Pelanggan membayar.** Pelanggan memilih bank pengirim, melakukan transfer ke rekening yang ditampilkan aplikasi sebesar total pembayaran, lalu mengunggah bukti.
4. **Admin memeriksa pembayaran.** Admin memastikan ongkir sudah ditetapkan, memeriksa bukti dan jumlah pembayaran, lalu menerima atau menolak bukti.
5. **Pesanan diproses.** Pembayaran yang diterima menjadi **diverifikasi**. Pesanan yang masih **menunggu** menjadi **diproses** dan Admin dapat mencetak struk.
6. **Admin menugaskan Kurir.** Pada **Pengiriman**, Admin memilih Kurir untuk pesanan yang telah diverifikasi.
7. **Kurir menerima tugas.** Tugas muncul di menu **Pengiriman** pada akun Kurir yang ditunjuk. Kurir membaca kontak penerima, alamat, catatan, dan detail pesanan.
8. **Kurir mengirimkan pesanan.** Kurir memperbarui status sesuai tahap nyata: **menunggu** → **diproses** → **dikirim** → **diterima**.
9. **Pelanggan memantau hasil.** Pelanggan melihat status pembayaran/pesanan/pengiriman melalui **Pesanan**. Status **diterima** mengubah pesanan menjadi **selesai**.

### 6.2 Keterkaitan Status

Status pesanan, pembayaran, dan pengiriman merupakan informasi yang berkaitan, tetapi bukan satu status yang sama. Pembayaran **diverifikasi** tidak otomatis berarti barang sudah dikirim; Admin masih perlu memproses dan menugaskan Kurir. Sebaliknya, status pengiriman **diterima** menandai pesanan sebagai **selesai**. Jika suatu status tidak berubah, periksa menu yang sesuai dengan proses tersebut: pembayaran oleh Admin, penugasan oleh Admin, atau tahapan kirim oleh Kurir.

---

## 7. Penanganan Kendala dan Kesalahan

| Kendala | Penyebab yang mungkin | Langkah penanganan |
|---|---|---|
| Tidak dapat login | Username/email atau password salah; akun Kurir belum disetujui; peran akun berbeda. | Periksa ejaan dan kredensial. Pelanggan gunakan pemulihan kode. Admin/Kurir ikuti prosedur pemulihan masing-masing. Kurir menunggu persetujuan Admin. |
| Registrasi gagal | Username/email sudah digunakan, format email tidak valid, password kurang dari batas, atau konfirmasi tidak sama. | Periksa pesan validasi, gunakan username/email lain, isi ulang data, dan samakan konfirmasi password. |
| Produk tidak tersedia atau stok habis | Produk dihapus/tidak ditemukan atau stok berubah. | Muat ulang katalog/detail, pilih produk lain atau jumlah lebih kecil. Admin memeriksa stok melalui **Harga & Stok**. |
| Jumlah produk melebihi stok | Jumlah keranjang lebih besar daripada stok terbaru. | Kurangi jumlah pada keranjang dan simpan perubahan. Jangan lanjutkan Checkout sampai pesan stok tidak muncul. |
| Checkout gagal | Keranjang kosong/produk tidak tersedia; data wajib belum lengkap; alamat tidak dikenali; tarif ongkir belum aktif. | Periksa keranjang, isi semua data penerima dan alamat dengan kecamatan/kota, lalu coba kembali. Hubungi Admin bila tarif wilayah belum tersedia. |
| Ongkir belum muncul/ditetapkan | Wilayah belum dapat dipetakan atau perlu ditetapkan Admin. | Jangan membayar berdasarkan perkiraan. Tunggu Admin menetapkan ongkir pada detail pesanan, lalu buka kembali halaman pembayaran. |
| Gagal mengunggah bukti pembayaran | Format bukan JPG/JPEG/PNG/WEBP, file lebih dari 5 MB, koneksi terputus, atau pembayaran sudah diverifikasi. | Pilih gambar valid berukuran maksimal 5 MB dan coba dengan koneksi stabil. Bukti tidak dapat diganti setelah diverifikasi. |
| Pembayaran belum diverifikasi | Admin belum memeriksa bukti atau status masih menunggu. | Buka **Pesanan** untuk memeriksa status. Pastikan bukti sudah berhasil diunggah. Jika perlu tindak lanjut, hubungi Admin dan sampaikan nomor pesanan. |
| Pembayaran ditolak | Bukti tidak sesuai atau transaksi belum dapat dipastikan. | Periksa nominal dan bukti transfer; unggah ulang bukti yang benar jika tombol **Lihat / Unggah Ulang Bukti** tersedia. Hubungi Admin bila tidak jelas. |
| Belum ada pengiriman untuk Kurir | Belum ada pesanan terverifikasi yang ditugaskan kepada akun tersebut. | Pastikan akun sudah aktif, buka **Pengiriman** kembali, lalu hubungi Admin untuk memeriksa status pembayaran dan penugasan. |
| Gagal memperbarui status pengiriman | Tahapan tidak berurutan, status sudah berubah, atau tugas bukan milik akun Kurir tersebut. | Muat ulang detail dan pilih tindakan untuk tahap berikutnya saja. Jika masih gagal, sampaikan nomor pesanan kepada Admin. |
| Lupa password pelanggan | Kode pemulihan belum diterbitkan, salah, kedaluwarsa, sudah dipakai, atau terkunci sementara. | Gunakan kode pemulihan yang disimpan. Jika masih bisa login, terbitkan kode baru di Profil. Jika tidak bisa dan tidak memiliki kode, hubungi pengelola. |
| Lupa password Admin | Permintaan pemulihan belum disetujui Admin lain atau persetujuan kedaluwarsa. | Ajukan permintaan pemulihan Admin, minta Admin lain meninjau di **Petugas**, lalu lanjutkan di browser yang digunakan untuk mengajukan permintaan. |
| Lupa password Kurir | Permintaan reset belum diverifikasi Admin. | Ajukan reset menggunakan username, hubungi Admin, dan gunakan password sementara hanya setelah Admin menyetujuinya. Ganti password saat login pertama setelah reset. |
| Email verifikasi tidak diterima | Alamat keliru, pesan masuk spam, atau layanan email belum dikonfigurasi. | Periksa alamat dan folder spam, gunakan kirim ulang verifikasi dari Profil. Jika tetap gagal, minta pengelola memeriksa konfigurasi email. |
| Halaman menampilkan kesalahan atau data tidak berubah | Koneksi terputus, sesi kedaluwarsa, atau data telah berubah oleh pengguna lain. | Muat ulang halaman, login kembali jika diminta, dan periksa status terbaru sebelum mengulang tindakan. Hindari mengirim tindakan berkali-kali tanpa memeriksa hasil sebelumnya. |

Saat meminta bantuan, sampaikan peran akun, nomor pesanan (jika ada), waktu kejadian, dan pesan kesalahan yang tampil. Jangan mengirim password, kode pemulihan, atau informasi rahasia ke pihak yang tidak berwenang.

---

## 8. Penutup

Samudera Fresh Strawberry membantu menghubungkan proses penjualan produk, pencatatan pesanan dan pembayaran, pengelolaan operasional Admin, serta pelaksanaan pengiriman oleh Kurir. Pemisahan hak akses membantu setiap aktor menjalankan fungsi sesuai tanggung jawabnya, sementara pelanggan dapat memantau status pesanan melalui akun.

Manual Book ini diharapkan membantu pengguna memahami aplikasi secara tertib dan mengurangi kesalahan saat mengisi data maupun memperbarui status. Pengguna dianjurkan untuk selalu memeriksa rincian transaksi dan mengikuti status yang tampil pada aplikasi.

Demikian panduan penggunaan Aplikasi Samudera Fresh Strawberry disusun. Terima kasih atas perhatian dan kerja sama seluruh pengguna. Semoga aplikasi ini mendukung pelayanan dan perkembangan UMKM Samudera Fresh Strawberry.

**— Selesai —**
