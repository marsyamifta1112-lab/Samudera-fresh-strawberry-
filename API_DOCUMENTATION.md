# API Documentation

## 1. Deskripsi Umum

Samudera Fresh Strawberry adalah aplikasi berbasis web untuk sistem informasi penjualan produk UMKM stroberi Ciwidey. Aplikasi ini dikembangkan oleh Kelompok Twinbyte dari SMK Budi Bakti Ciwidey dan dibangun menggunakan Laravel, PHP, dan MySQL.

Dokumentasi ini dibuat berdasarkan hasil analisis langsung terhadap source code proyek, terutama file `routes/api.php`, `routes/web.php`, controller, middleware, dan konfigurasi autentikasi Laravel.

## 2. Tujuan Dokumentasi API

Dokumentasi ini bertujuan untuk:

- menjelaskan endpoint yang benar-benar ada di proyek
- membedakan antara route web dan route API
- menjelaskan metode HTTP, parameter, response, dan error yang ada
- memberikan gambaran yang jujur tentang status API saat ini
- menjadi dokumen pendukung untuk penilaian akhir, pengujian, dan pengembangan lebih lanjut

## 3. Status Sebenarnya API Proyek

Berdasarkan hasil inspeksi source code, aplikasi ini pada dasarnya adalah aplikasi web berbasis session dan form-based UI, bukan REST API yang lengkap untuk seluruh modul bisnis.

Hal yang ditemukan:

- `routes/web.php` berisi sebagian besar fitur utama aplikasi, seperti login, register, checkout, dashboard admin, dashboard kurir, profil pelanggan, pengelolaan produk, pembelian, dan pengiriman.
- `routes/api.php` hanya berisi endpoint JSON read-only untuk data produk, pesanan, dan pembayaran.
- Tidak ditemukan implementasi autentikasi token berbasis API seperti Laravel Sanctum atau Passport.
- Autentikasi berjalan melalui session web (`web` guard) dan middleware custom `RoleMiddleware`.

Kesimpulan:

- Proyek ini tidak memiliki API REST formal yang lengkap untuk semua fitur bisnis.
- Endpoint JSON yang ada terbatas pada data read-only yang dapat diakses melalui `routes/api.php`.
- Fitur utama aplikasi diimplementasikan melalui halaman web dan session-based routing di `routes/web.php`.

## 4. Teknologi yang Digunakan

- PHP 8.2
- Laravel Framework 12
- MySQL / MariaDB
- Blade Template Engine
- Vite
- Tailwind CSS
- Composer
- PHPUnit
- Session-based authentication dan custom role middleware

## 5. Base URL API

Karena route API tidak memakai prefix `/api`, maka endpoint yang tersedia bersifat relatif terhadap base URL aplikasi.

Contoh:

- Local development: `http://localhost:8000/produk`
- Production: `https://domain-anda.com/produk`

Catatan:

- Endpoint API terdapat langsung di root aplikasi, bukan di `/api/...`.
- `routes/api.php` tidak menambahkan prefix otomatis jika tidak dikonfigurasi secara khusus.

## 6. Metode HTTP yang Digunakan

Endpoint API yang benar-benar tersedia pada source code menggunakan metode HTTP berikut:

- `GET`

Tidak ada endpoint `POST`, `PUT`, `PATCH`, atau `DELETE` yang ditemukan pada `routes/api.php`.

## 7. Endpoint API yang Benar-benar Tersedia

### 7.1 Endpoint Produk

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/produk` | Mengambil daftar produk |
| GET | `/produk/{id}` | Mengambil detail produk berdasarkan ID |

### 7.2 Endpoint Pesanan

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/pesanan` | Mengambil daftar pesanan |
| GET | `/pesanan/{id}` | Mengambil detail pesanan berdasarkan ID |

### 7.3 Endpoint Pembayaran

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/pembayaran` | Mengambil daftar pembayaran |
| GET | `/pembayaran/{id}` | Mengambil detail pembayaran berdasarkan ID |

## 8. Detail Endpoint API

### 8.1 GET /produk

#### Deskripsi
Mengambil daftar seluruh produk yang tersimpan di tabel `produk`.

#### Method
`GET`

#### URL
`/produk`

#### Parameter
Tidak ada parameter wajib.

Parameter opsional:

- Tidak ada pada implementasi saat ini.

#### Header
- `Accept: application/json` (disarankan)

#### Response Sukses
```json
{
  "success": true,
  "message": "Data produk berhasil diambil.",
  "data": [
    {
      "id_produk": 1,
      "nama_produk": "Stroberi Ciwidey",
      "deskripsi": "Produk berkualitas tinggi",
      "harga_produk": 25000,
      "stok_produk": 50,
      "foto_produk": "produk-1.jpg"
    }
  ]
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Produk tidak ditemukan.",
  "data": null
}
```

#### HTTP Status Code
- `200 OK` untuk sukses
- `404 Not Found` jika data tidak ada (hanya pada endpoint detail produk)

---

### 8.2 GET /produk/{id}

#### Deskripsi
Mengambil detail produk berdasarkan `id_produk`.

#### Method
`GET`

#### URL
`/produk/{id}`

#### Parameter Path
| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `id` | integer | Ya | ID produk |

#### Response Sukses
```json
{
  "success": true,
  "message": "Detail produk berhasil diambil.",
  "data": {
    "id_produk": 1,
    "nama_produk": "Stroberi Ciwidey",
    "deskripsi": "Produk berkualitas tinggi",
    "harga_produk": 25000,
    "stok_produk": 50,
    "foto_produk": "produk-1.jpg"
  }
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Produk tidak ditemukan.",
  "data": null
}
```

#### HTTP Status Code
- `200 OK`
- `404 Not Found`

---

### 8.3 GET /pesanan

#### Deskripsi
Mengambil daftar pesanan. Endpoint ini menyediakan filter berdasarkan `id_pelanggan` jika parameter yang diminta ada.

#### Method
`GET`

#### URL
`/pesanan`

#### Parameter Query
| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `id_pelanggan` | integer | Tidak | Filter pesanan berdasarkan pelanggan |

#### Contoh Request
```http
GET /pesanan?id_pelanggan=12
```

#### Response Sukses
```json
{
  "success": true,
  "message": "Data pesanan berhasil diambil.",
  "data": [
    {
      "id_pesanan": 101,
      "id_pelanggan": 12,
      "tanggal_pemesanan": "2026-11-10",
      "total_harga": 500000,
      "status_pesanan": "Diproses",
      "nama_penerima": "Budi",
      "alamat_pengiriman": "Jl. Merdeka No. 10",
      "no_hp": "081234567890",
      "catatan": "Sedang diproses"
    }
  ]
}
```

#### Response Error
Karena endpoint ini tidak melakukan validasi error spesifik pada query, maka respons error umumnya didapatkan jika query database gagal atau data tidak tersedia.

#### HTTP Status Code
- `200 OK`

---

### 8.4 GET /pesanan/{id}

#### Deskripsi
Mengambil detail lengkap sebuah pesanan, termasuk metadata pesanan dan daftar item dalam transaksi tersebut.

#### Method
`GET`

#### URL
`/pesanan/{id}`

#### Parameter Path
| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `id` | integer | Ya | ID pesanan |

#### Response Sukses
```json
{
  "success": true,
  "message": "Detail pesanan berhasil diambil.",
  "data": {
    "pesanan": {
      "id_pesanan": 101,
      "id_pelanggan": 12,
      "tanggal_pemesanan": "2026-11-10",
      "total_harga": 500000,
      "status_pesanan": "Diproses",
      "nama_penerima": "Budi",
      "alamat_pengiriman": "Jl. Merdeka No. 10",
      "no_hp": "081234567890",
      "catatan": "Sedang diproses"
    },
    "detail": [
      {
        "id_detail": 1,
        "id_produk": 5,
        "nama_produk": "Stroberi Premium",
        "jumlah": 2,
        "harga": 180000,
        "subtotal": 360000
      }
    ]
  }
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Pesanan tidak ditemukan.",
  "data": null
}
```

#### HTTP Status Code
- `200 OK`
- `404 Not Found`

---

### 8.5 GET /pembayaran

#### Deskripsi
Mengambil daftar data pembayaran yang tersimpan di tabel `pembayaran`.

#### Method
`GET`

#### URL
`/pembayaran`

#### Parameter
Tidak ada parameter wajib.

#### Response Sukses
```json
{
  "success": true,
  "message": "Data pembayaran berhasil diambil.",
  "data": [
    {
      "id_pembayaran": 1,
      "id_pesanan": 101,
      "tanggal_pembayaran": "2026-11-10",
      "metode_pembayaran": "Transfer Bank",
      "jumlah_bayar": 500000,
      "bukti_pembayaran": "proof_101.jpg",
      "status_pembayaran": "Menunggu Verifikasi"
    }
  ]
}
```

#### HTTP Status Code
- `200 OK`

---

### 8.6 GET /pembayaran/{id}

#### Deskripsi
Mengambil detail data pembayaran berdasarkan `id_pembayaran`.

#### Method
`GET`

#### URL
`/pembayaran/{id}`

#### Parameter Path
| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `id` | integer | Ya | ID pembayaran |

#### Response Sukses
```json
{
  "success": true,
  "message": "Detail pembayaran berhasil diambil.",
  "data": {
    "id_pembayaran": 1,
    "id_pesanan": 101,
    "tanggal_pembayaran": "2026-11-10",
    "metode_pembayaran": "Transfer Bank",
    "jumlah_bayar": 500000,
    "bukti_pembayaran": "proof_101.jpg",
    "status_pembayaran": "Menunggu Verifikasi"
  }
}
```

#### Response Error
```json
{
  "success": false,
  "message": "Pembayaran tidak ditemukan.",
  "data": null
}
```

#### HTTP Status Code
- `200 OK`
- `404 Not Found`

## 9. Endpoint Web yang Bukan REST API

Dalam aplikasi ini, fitur utama dijalankan melalui halaman web di `routes/web.php`, bukan melalui endpoint API formal. Beberapa route penting yang ditemukan adalah:

### 9.1 Autentikasi dan Akun

- `GET /login`
- `POST /login`
- `GET /register`
- `POST /register`
- `GET /kurir/register`
- `POST /kurir/register`
- `GET /password/lupa`
- `POST /password/lupa`
- `GET /password/baru`
- `POST /password/baru`

### 9.2 Produk dan Keranjang

- `GET /produk`
- `GET /produk/{id}`
- `GET /keranjang`
- `POST /keranjang/tambah/{id}`
- `PUT /keranjang/{id}`
- `DELETE /keranjang/{id}`
- `GET /checkout`
- `POST /checkout`

### 9.3 Pesanan dan Pembayaran

- `GET /pesanan/{id}`
- `GET /pesanan/{id}/pembayaran`
- `POST /pesanan/{id}/pembayaran/metode`
- `POST /pesanan/{id}/pembayaran/unggah`
- `POST /pesanan/{id}/return`

### 9.4 Admin

- `GET /admin`
- `GET /admin/products`
- `POST /admin/products`
- `PUT /admin/products/{id}`
- `DELETE /admin/products/{id}`
- `GET /admin/orders`
- `GET /admin/reports`
- `GET /admin/reports/export`
- `GET /admin/shipments`
- `PUT /admin/payments/{id}`

### 9.5 Kurir

- `GET /kurir/dashboard`
- `GET /kurir/shipments`
- `GET /kurir/shipments/{id}`
- `PUT /kurir/shipments/{id}`

> Perlu dipahami bahwa route web di atas mengirim HTML dan memanfaatkan session Laravel, bukan payload JSON seperti API REST. Mereka tidak bisa diperlakukan sebagai endpoint API formal tanpa penyesuaian implementasi.

## 10. Autentikasi dan Otorisasi

### 10.1 Sistem Autentikasi

Berdasarkan `config/auth.php`, konfigurasi autentikasi aplikasi menggunakan guard `web` dengan provider `users`.

Artinya:

- autentikasi berbasis session
- user login menggunakan form web
- tidak ada token, bearer token, atau JWT yang didefinisikan

### 10.2 Otorisasi

Otorisasi dilakukan secara custom melalui `RoleMiddleware`.

File `app/Http/Middleware/RoleMiddleware.php` memeriksa session:

- `role`
- `id_pelanggan`
- `id_petugas`
- `must_change_password`

Middleware mengecek role berikut:

- `Pelanggan`
- `Admin`
- `Kurir`

Contoh logika:

- pelanggan hanya bisa mengakses route yang bersifat pelanggan
- admin hanya bisa mengakses route admin
- kurir hanya bisa mengakses route kurir
- kurir yang harus mengganti password akan diarahkan ke halaman perubahan password sementara

### 10.3 Kondisi Autentikasi yang Ditemukan

- login pelanggan dan admin/kurir diatur di `AuthController` dengan validasi username/email dan password
- password disimpan menggunakan `Hash::make()`
- session dipakai untuk menyimpan identitas login
- redirect dilakukan ke halaman dashboard atau onboarding sesuai peran

## 11. Validasi Data dan Aturan Akses

Beberapa validasi yang ditemukan pada source code antara lain:

### 11.1 Register Pelanggan

Pada `AuthController::register()`:

- `nama_pelanggan` wajib diisi
- `email` valid jika diisi
- `username` wajib diisi
- `password` minimal 6 karakter
- username dan email tidak boleh duplikat

### 11.2 Login

Pada `AuthController::login()`:

- `username` wajib diisi
- `password` wajib diisi
- hanya satu akun yang valid yang diizinkan
- akun petugas dengan `status_akun !== 'Aktif'` ditolak

### 11.3 Register Kurir

Pada `AuthController::registerKurir()`:

- `nama_petugas` wajib diisi
- `username` wajib unik
- `password` minimal 8 karakter dan `confirmed`
- `no_telp` dan `alamat` bersifat opsional

### 11.4 Akses Peran

- `role:Pelanggan`
- `role:Admin`
- `role:Kurir`

Akses dibatasi dalam `routes/web.php` dan `RoleMiddleware`.

## 12. Hubungan API dengan Database

API yang ada menarik data langsung dari tabel database Laravel menggunakan query builder:

- `DB::table('produk')`
- `DB::table('pesanan')`
- `DB::table('detail_pesanan')`
- `DB::table('pembayaran')`

Contoh relasi yang digunakan:

- `detail_pesanan` berelasi dengan `produk` melalui `id_produk`
- `pesanan` memiliki data pelanggan melalui `id_pelanggan`
- `pembayaran` terkait dengan `pesanan` melalui `id_pesanan`

Data yang dikembalikan dibentuk dalam format JSON dengan properti:

- `success`
- `message`
- `data`

## 13. Response Error yang Ditemukan

Endpoint JSON yang ada menghadirkan response error dengan pola berikut:

```json
{
  "success": false,
  "message": "Produk tidak ditemukan.",
  "data": null
}
```

Pada route-detail, error 404 digunakan jika record tidak ditemukan.

## 14. HTTP Status Code yang Digunakan

| Kode | Arti | Keterangan |
|---|---|---|
| 200 | OK | Request berhasil diproses |
| 404 | Not Found | Data tidak ditemukan |
| 302 | Redirect | Digunakan pada route web, bukan API |
| 500 | Internal Server Error | Terjadi error server runtime |

## 15. Kesimpulan

Berdasarkan hasil analisis source code, dapat disimpulkan bahwa:

1. Proyek Samudera Fresh Strawberry tidak memiliki REST API formal yang lengkap untuk seluruh fungsi bisnis.
2. `routes/api.php` menyediakan endpoint JSON yang terbatas untuk data publik/read-only, yaitu produk, pesanan, dan pembayaran.
3. Fitur utama aplikasi berjalan melalui `routes/web.php` dan session-based authentication.
4. Autentikasi dan otorisasi tidak menggunakan token API seperti JWT, Sanctum, atau Passport.
5. Endpoint JSON yang tersedia benar-benar ada dan sesuai dengan implementasi source code.

## 16. Rekomendasi Pengembangan API ke Depan

Untuk pengembangan API yang lebih profesional dan siap digunakan pada aplikasi mobile atau frontend modern, rekomendasi yang dapat dilakukan adalah:

- membangun route API terpisah dengan prefix `/api`
- menggunakan Laravel Sanctum atau Passport untuk auth token
- membuat CRUD API untuk produk, pesanan, pembayaran, pengiriman, dan laporan
- menggunakan resource controller dan form request validation
- menyediakan response API yang konsisten dengan format JSON standar
- membuat endpoint autentikasi seperti `/api/login`, `/api/logout`, `/api/me`

## 17. Catatan Akhir

Dokumentasi ini dibuat berdasarkan hasil pemeriksaan langsung terhadap file proyek yang tersedia. Semua endpoint yang dijelaskan adalah endpoint yang benar-benar ada pada source code. Tidak ada endpoint fiktif atau dibuat-buat yang tidak sesuai dengan implementasi yang ada.
