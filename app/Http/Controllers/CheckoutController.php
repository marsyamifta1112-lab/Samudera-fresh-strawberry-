<?php

namespace App\Http\Controllers;

use App\Support\DeliveryEstimate;
use App\Support\OperationalSchedule;
use App\Support\ProductCatalog;
use App\Support\ShippingZoneResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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

        $idProduk = collect($keranjang)->pluck('id_produk')->filter()->unique()->values();
        $produkTersedia = DB::table('produk')
            ->whereIn('id_produk', $idProduk)
            ->get()
            ->keyBy('id_produk');
        $grades = $idProduk->isEmpty()
            ? collect()
            : DB::table('produk_grade')
                ->whereIn('id_produk', $idProduk)
                ->get()
                ->keyBy(fn (object $grade): string => $grade->id_produk.'-'.$grade->grade);
        $subtotalProduk = 0;

        foreach ($keranjang as $cartKey => &$item) {
            $produk = $produkTersedia->get($item['id_produk'] ?? $cartKey);

            if (! $produk) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Ada produk yang sudah tidak tersedia. Periksa kembali keranjang.');
            }

            if (! (bool) ($produk->is_available ?? true)) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Maaf, produk sedang habis dan tidak dapat dipesan.');
            }

            if (ProductCatalog::isHematPackage($produk->nama_produk)) {
                return redirect()->route('keranjang.index')
                    ->with('error', 'Paket Hemat sudah tidak tersedia. Hapus dari keranjang untuk melanjutkan.');
            }

            if (! empty($item['grade'])) {
                $grade = $grades->get($produk->id_produk.'-'.$item['grade']);
                if (! $grade || (int) $item['jumlah'] > (float) $grade->stok_kg) {
                    return redirect()->route('keranjang.index')
                        ->with('error', 'Stok grade yang dipilih berubah. Periksa kembali keranjang.');
                }

                $item['harga_produk'] = $grade->harga_per_kg;
                $item['foto_produk'] = ProductCatalog::gradeImage($item['grade']);
                $item['satuan'] = 'kg';
            } else {
                if (ProductCatalog::isFreshStrawberry($produk->nama_produk)) {
                    return redirect()->route('produk.show', $produk->id_produk)
                        ->with('error', 'Pilih grade stroberi segar sebelum melanjutkan.');
                }
                if ((int) $produk->stok_produk < 1 || (int) $item['jumlah'] > (int) $produk->stok_produk) {
                    return redirect()->route('keranjang.index')
                        ->with('error', 'Stok produk tidak mencukupi. Periksa kembali keranjang.');
                }

                $item['harga_produk'] = $produk->harga_produk;
                $item['foto_produk'] = $produk->foto_produk;
                $item['satuan'] = $produk->berat_gram ? 'paket' : 'pcs';
                $item['berat_gram'] = $produk->berat_gram;
            }
            $item['id_produk'] = $produk->id_produk;
            $item['nama_produk'] = $produk->nama_produk;
            $subtotalProduk += $item['harga_produk'] * $item['jumlah'];
        }
        unset($item);
        session(['keranjang' => $keranjang]);

        $aturanWilayahOngkir = ShippingZoneResolver::rules();
        $tarifWilayahOngkir = $wilayahOngkir
            ->pluck('tarif', 'nama_wilayah')
            ->map(fn ($tarif) => (int) $tarif);
        $settings = OperationalSchedule::settings();
        $orderedAt = Carbon::now('UTC');
        $operationalSchedule = OperationalSchedule::calculate($settings, $orderedAt);
        $arrivalEstimates = [];

        foreach ($wilayahOngkir as $zone) {
            $arrivalEstimates[$zone->nama_wilayah] = DeliveryEstimate::forOrder(
                $settings,
                $orderedAt,
                $zone->nama_wilayah,
                $operationalSchedule['tanggal_pengiriman']
            );
        }

        return view('checkout', compact(
            'keranjang',
            'subtotalProduk',
            'wilayahOngkir',
            'aturanWilayahOngkir',
            'tarifWilayahOngkir',
            'operationalSchedule',
            'arrivalEstimates'
        ));
    }

    // Memproses pesanan dari checkout
    public function store(Request $request)
    {
        $akses = $this->cekPelanggan();

        if ($akses) {
            return $akses;
        }

        $validated = $request->validate([
            'nama_penerima' => 'required|string|max:100',
            'alamat_pengiriman' => 'required|string|max:1000',
            'no_hp' => 'required|string|max:20',
            'catatan' => 'nullable|string|max:1000',
            'metode_pembayaran' => 'required|in:Transfer,QRIS',
        ]);

        $keranjang = session('keranjang', []);

        if (empty($keranjang)) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Keranjang belanja masih kosong.');
        }

        $idPelanggan = session('id_pelanggan');

        $order = DB::transaction(function () use (
            $keranjang,
            $idPelanggan,
            $validated
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

                if (! (bool) ($produk->is_available ?? true)) {
                    throw ValidationException::withMessages([
                        'keranjang' => 'Maaf, produk sedang habis dan tidak dapat dipesan.',
                    ]);
                }

                if (ProductCatalog::isHematPackage($produk->nama_produk)) {
                    throw ValidationException::withMessages([
                        'keranjang' => 'Paket Hemat sudah tidak tersedia. Hapus dari keranjang.',
                    ]);
                }

                if (! empty($item['grade'])) {
                    if (! ProductCatalog::isFreshStrawberry($produk->nama_produk)) {
                        throw ValidationException::withMessages([
                            'keranjang' => 'Pilihan grade hanya berlaku untuk stroberi segar.',
                        ]);
                    }

                    $varian = DB::table('produk_grade')
                        ->where('id_produk', $produk->id_produk)
                        ->where('grade', $item['grade'])
                        ->lockForUpdate()
                        ->first();

                    if (! $varian || $jumlah > (float) $varian->stok_kg) {
                        throw ValidationException::withMessages([
                            'keranjang' => "Stok Grade {$item['grade']} tidak mencukupi.",
                        ]);
                    }

                    $harga = (float) $varian->harga_per_kg;
                    $subtotal = $harga * $jumlah;
                    $subtotalProduk += $subtotal;
                    $detailPesanan[] = [
                        'id_produk' => $produk->id_produk,
                        'jumlah' => $jumlah,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                        'grade' => $item['grade'],
                        'satuan' => 'kg',
                        'berat_gram' => null,
                    ];

                    DB::table('produk_grade')
                        ->where('id_produk_grade', $varian->id_produk_grade)
                        ->decrement('stok_kg', $jumlah);

                    continue;
                }

                if (ProductCatalog::isFreshStrawberry($produk->nama_produk)) {
                    throw ValidationException::withMessages([
                        'keranjang' => 'Pilih grade stroberi segar sebelum checkout.',
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

                $harga = (float) $produk->harga_produk;
                $subtotal = $harga * $jumlah;
                $subtotalProduk += $subtotal;

                $detailPesanan[] = [
                    'id_produk' => $produk->id_produk,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'grade' => null,
                    'satuan' => $produk->berat_gram ? 'paket' : 'pcs',
                    'berat_gram' => $produk->berat_gram,
                ];

                // Kurangi stok dalam transaksi yang sama
                DB::table('produk')
                    ->where('id_produk', $produk->id_produk)
                    ->decrement('stok_produk', $jumlah);
            }

            $wilayahOngkir = ShippingZoneResolver::resolve(
                $wilayahOngkir,
                $validated['alamat_pengiriman']
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

            $orderedAtUtc = Carbon::now('UTC');
            $operationalSettings = OperationalSchedule::settings();
            $shippingSchedule = OperationalSchedule::calculate(
                $operationalSettings,
                $orderedAtUtc->copy()->setTimezone(OperationalSchedule::TIMEZONE)
            );
            $arrivalEstimate = DeliveryEstimate::forOrder(
                $operationalSettings,
                $orderedAtUtc,
                $wilayahOngkir->nama_wilayah,
                $shippingSchedule['tanggal_pengiriman']
            );

            // Simpan data pesanan
            $idPesanan = DB::table('pesanan')->insertGetId([
                'id_pelanggan' => $idPelanggan,
                'tanggal_pemesanan' => $orderedAtUtc->toDateTimeString(),
                'jadwal_pengiriman' => $shippingSchedule['tanggal_pengiriman'],
                'total_harga' => $subtotalProduk + $ongkir,
                'ongkir' => $ongkir,
                'wilayah_ongkir' => $wilayahOngkir->nama_wilayah,
                'status_pesanan' => 'menunggu',
                'nama_penerima' => $validated['nama_penerima'],
                'alamat_pengiriman' => $validated['alamat_pengiriman'],
                'no_hp' => $validated['no_hp'],
                'catatan' => $validated['catatan'] ?? null,
            ], 'id_pesanan');

            // Simpan rincian setiap produk yang dipesan
            foreach ($detailPesanan as $detail) {
                $detail['id_pesanan'] = $idPesanan;

                DB::table('detail_pesanan')->insert($detail);
            }

            // Buat catatan pembayaran
            DB::table('pembayaran')->insert([
                'id_pesanan' => $idPesanan,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'jumlah_bayar' => $subtotalProduk + $ongkir,
                'status_pembayaran' => 'menunggu',
            ]);

            return [
                'id_pesanan' => $idPesanan,
                'jadwal' => $shippingSchedule,
                'estimasi_tiba' => $arrivalEstimate,
            ];
        });

        // Kosongkan keranjang setelah transaksi berhasil
        session()->forget('keranjang');

        return redirect()->route('pembayaran.show', $order['id_pesanan'])
            ->with(
                'success',
                'Pesanan berhasil dibuat. Perkiraan tiba di rumah: '.$order['estimasi_tiba']['label'].'. Perkiraan berdasarkan wilayah tujuan dan dapat berubah mengikuti kurir; silakan lanjutkan pembayaran.'
            );
    }
}