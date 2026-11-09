<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Support\ShippingZoneResolver;

class CheckoutController extends Controller
{
    // Memastikan pelanggan sudah login
    private function cekPelanggan()
    {
        if (
            !session('id_pelanggan') ||
            session('role') !== 'Pelanggan'
        ) {
            return redirect()->route('login')
                ->with(
                    'error',
                    'Silakan login sebagai pelanggan terlebih dahulu.'
                );
        }

        return null;
    }

    // Menampilkan halaman checkout
    public function index()
    {
        $akses = $this->cekPelanggan();

        if ($akses) {
            return $akses;
        }

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang belanja masih kosong.');
        }

        $wilayahOngkir = DB::table('ongkir_wilayah')
            ->where('aktif', true)
            ->orderBy('tarif')
            ->get();
        abort_if($wilayahOngkir->isEmpty(), 503, 'Tarif pengiriman belum tersedia. Silakan hubungi Admin.');

        $produkTersedia = DB::table('produk')
            ->whereIn('id_produk', array_keys($keranjang))
            ->get()
            ->keyBy('id_produk');
        $subtotalProduk = 0;

        foreach ($keranjang as $idProduk => $item) {
            $produk = $produkTersedia->get($idProduk);

            if (! $produk) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Ada produk yang sudah tidak tersedia. Periksa kembali keranjang.');
            }

            $keranjang[$idProduk]['nama_produk'] = $produk->nama_produk;
            $keranjang[$idProduk]['harga_produk'] = $produk->harga_produk;
            $keranjang[$idProduk]['foto_produk'] = $produk->foto_produk;
            $subtotalProduk += $produk->harga_produk * $item['jumlah'];
        }

        $aturanWilayahOngkir = ShippingZoneResolver::rules();
        $tarifWilayahOngkir = $wilayahOngkir
            ->pluck('tarif', 'nama_wilayah')
            ->map(fn ($tarif) => (int) $tarif);

        return view('checkout', compact(
            'keranjang',
            'subtotalProduk',
            'wilayahOngkir',
            'aturanWilayahOngkir',
            'tarifWilayahOngkir'
        ));
    }

    // Memproses pesanan dari checkout
    public function store(Request $request)
    {
        $akses = $this->cekPelanggan();

        if ($akses) {
            return $akses;
        }

        $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'alamat_pengiriman' => 'required|string|max:1000',
            'no_hp' => 'required|string|max:20',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang belanja masih kosong.');
        }

        $idPelanggan = session('id_pelanggan');

        $idPesanan = DB::transaction(function () use (
            $keranjang,
            $idPelanggan,
            $request
        ) {
            $wilayahOngkir = DB::table('ongkir_wilayah')
                ->where('aktif', true)
                ->lockForUpdate()
                ->get();

            $subtotalProduk = 0;
            $detailPesanan = [];

            foreach ($keranjang as $item) {
                // Validasi struktur dan jumlah item
                if (
                    !isset($item['id_produk'], $item['jumlah']) ||
                    !is_numeric($item['jumlah']) ||
                    (int) $item['jumlah'] < 1 ||
                    (float) $item['jumlah'] !== (float) (int) $item['jumlah']
                ) {
                    throw ValidationException::withMessages([
                        'keranjang' =>
                            'Jumlah produk tidak valid. Silakan periksa keranjang.',
                    ]);
                }

                $jumlah = (int) $item['jumlah'];

                // Ambil data produk terbaru dan kunci barisnya
                $produk = DB::table('produk')
                    ->where('id_produk', $item['id_produk'])
                    ->lockForUpdate()
                    ->first();

                if (!$produk) {
                    throw ValidationException::withMessages([
                        'keranjang' =>
                            'Produk tidak ditemukan. Silakan periksa keranjang.',
                    ]);
                }

                if (
                    $produk->stok_produk === null ||
                    $jumlah > (int) $produk->stok_produk
                ) {
                    throw ValidationException::withMessages([
                        'keranjang' =>
                            'Stok produk tidak mencukupi. Silakan periksa keranjang.',
                    ]);
                }

                $subtotal = $produk->harga_produk * $jumlah;
                $subtotalProduk += $subtotal;

                $detailPesanan[] = [
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $jumlah,
                    'harga' => $produk->harga_produk,
                    'subtotal' => $subtotal,
                ];

                // Kurangi stok dalam transaksi yang sama
                DB::table('produk')
                    ->where('id_produk', $produk->id_produk)
                    ->decrement('stok_produk', $jumlah);
            }

            $wilayahOngkir = ShippingZoneResolver::resolve(
                $wilayahOngkir,
                $request->alamat_pengiriman
            );

            if (! $wilayahOngkir) {
                throw ValidationException::withMessages([
                    'alamat_pengiriman' => 'Tarif untuk wilayah alamat ini belum tersedia. Silakan hubungi Admin.',
                ]);
            }

            $ongkir = ShippingZoneResolver::cost(
                (int) $wilayahOngkir->tarif,
                $subtotalProduk
            );

            // Simpan data pesanan
            $idPesanan = DB::table('pesanan')->insertGetId([
                'id_pelanggan' => $idPelanggan,
                'tanggal_pemesanan' => now(),
                'total_harga' => $subtotalProduk + $ongkir,
                'ongkir' => $ongkir,
                'wilayah_ongkir' => $wilayahOngkir->nama_wilayah,
                'status_pesanan' => 'menunggu',
                'nama_penerima' => $request->nama_penerima,
                'alamat_pengiriman' => $request->alamat_pengiriman,
                'no_hp' => $request->no_hp,
                'catatan' => $request->catatan,
            ], 'id_pesanan');

            // Simpan rincian setiap produk yang dipesan
            foreach ($detailPesanan as $detail) {
                $detail['id_pesanan'] = $idPesanan;

                DB::table('detail_pesanan')->insert($detail);
            }

            // Buat catatan pembayaran
            DB::table('pembayaran')->insert([
                'id_pesanan' => $idPesanan,
                'metode_pembayaran' => 'BCA',
                'jumlah_bayar' => $subtotalProduk + $ongkir,
                'status_pembayaran' => 'menunggu',
            ]);

            return $idPesanan;
        });

        // Kosongkan keranjang setelah transaksi berhasil
        session()->forget('keranjang');

        return redirect()->route('pembayaran.show', $idPesanan)
            ->with(
                'success',
                'Pesanan berhasil dibuat. Ongkir telah dihitung berdasarkan alamat dan subtotal produk, silakan lanjutkan pembayaran.'
            );
    }
}