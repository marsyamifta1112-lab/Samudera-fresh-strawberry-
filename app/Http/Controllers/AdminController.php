<?php

namespace App\Http\Controllers;

use App\Support\DeliveryEstimate;
use App\Support\OperationalSchedule;
use App\Support\ProductCatalog;
use App\Support\ShippingZoneResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard()
    {
        $ringkasan = [
            'produk' => DB::table('produk')->count(),
            'pesanan' => DB::table('pesanan')->count(),
            'menunggu' => DB::table('pesanan')->where('status_pesanan', 'menunggu')->count(),
            'penjualan' => DB::table('pembayaran')->where('status_pembayaran', 'diverifikasi')->sum('jumlah_bayar'),
        ];

        $pesananTerbaru = $this->orderQuery()
            ->orderByDesc('p.tanggal_pemesanan')
            ->orderByDesc('p.id_pesanan')
            ->limit(6)
            ->get();

        $tanggalMulai = now()->subDays(6)->startOfDay();
        $penjualanHarian = DB::table('pesanan as p')
            ->join('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->where('pb.status_pembayaran', 'diverifikasi')
            ->whereBetween('p.tanggal_pemesanan', [$tanggalMulai, now()->endOfDay()])
            ->selectRaw('DATE(p.tanggal_pemesanan) as tanggal, SUM(pb.jumlah_bayar) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $grafik = collect(range(6, 0))->map(function (int $daysAgo) use ($penjualanHarian) {
            $date = now()->subDays($daysAgo)->toDateString();

            return (object) [
                'label' => now()->subDays($daysAgo)->format('d/m'),
                'total' => (float) ($penjualanHarian[$date] ?? 0),
            ];
        });

        return view('admin.dashboard', compact('ringkasan', 'pesananTerbaru', 'grafik'));
    }

    public function products(Request $request)
    {
        $query = DB::table('produk')->orderBy('nama_produk');

        if ($request->filled('q')) {
            $query->where('nama_produk', 'like', '%'.$request->string('q')->toString().'%');
        }

        $produk = $query->paginate(10)->withQueryString();

        return view('admin.products.index', compact('produk'));
    }

    public function toggle(int $id)
    {
        $isAvailable = DB::transaction(function () use ($id): bool {
            $produk = DB::table('produk')
                ->where('id_produk', $id)
                ->lockForUpdate()
                ->first();
            abort_if(! $produk, 404);

            $isAvailable = ! (bool) $produk->is_available;
            DB::table('produk')
                ->where('id_produk', $id)
                ->update(['is_available' => $isAvailable]);

            return $isAvailable;
        });

        return redirect()->route('admin.products.index')
            ->with('success', $isAvailable
                ? 'Produk berhasil ditandai tersedia.'
                : 'Produk berhasil ditandai habis.');
    }

    public function bestSellers()
    {
        $produkTerlaris = DB::table('detail_pesanan as dp')
            ->join('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->select(
                'pr.id_produk',
                'pr.nama_produk',
                'pr.foto_produk',
                'pr.berat_gram',
                'dp.grade',
                DB::raw('SUM(dp.jumlah) as total_terjual'),
                DB::raw('SUM(dp.subtotal) as total_penjualan')
            )
            ->groupBy('pr.id_produk', 'pr.nama_produk', 'pr.foto_produk', 'pr.berat_gram', 'dp.grade')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        return view('admin.products.best-sellers', compact('produkTerlaris'));
    }

    public function createProduct()
    {
        return view('admin.products.form', ['produk' => null]);
    }

    public function storeProduct(Request $request)
    {
        $data = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:5000',
            'harga_produk' => 'required|numeric|min:0|max:9999999999.99',
            'stok_produk' => 'required|integer|min:0|max:2147483647',
            'berat_gram' => 'nullable|integer|min:0|max:2147483647',
            'foto_produk' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $foto = $this->storeProductImage($request);
        if ($foto) {
            $data['foto_produk'] = $foto;
        }

        $idProduk = DB::table('produk')->insertGetId($data, 'id_produk');
        if (ProductCatalog::isFreshStrawberry($data['nama_produk'])) {
            $this->ensureProductGrades($idProduk);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct(int $id)
    {
        $produk = DB::table('produk')->where('id_produk', $id)->first();
        abort_if(! $produk, 404);

        return view('admin.products.form', compact('produk'));
    }

    public function updateProduct(Request $request, int $id)
    {
        $produk = DB::table('produk')->where('id_produk', $id)->first();
        abort_if(! $produk, 404);

        $data = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:5000',
            'harga_produk' => 'required|numeric|min:0|max:9999999999.99',
            'stok_produk' => 'required|integer|min:0|max:2147483647',
            'berat_gram' => 'nullable|integer|min:0|max:2147483647',
            'foto_produk' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $fotoBaru = $this->storeProductImage($request);
        if ($fotoBaru) {
            $data['foto_produk'] = $fotoBaru;
        }

        DB::table('produk')->where('id_produk', $id)->update($data);
        if (ProductCatalog::isFreshStrawberry($data['nama_produk'])) {
            $this->ensureProductGrades((int) $id);
        }

        if ($fotoBaru) {
            $this->deleteProductImage($produk->foto_produk);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function deleteProduct(int $id)
    {
        $produk = DB::table('produk')->where('id_produk', $id)->first();
        abort_if(! $produk, 404);

        if (DB::table('detail_pesanan')->where('id_produk', $id)->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena tercatat pada riwayat pesanan.');
        }

        DB::transaction(function () use ($id): void {
            DB::table('produk_grade')->where('id_produk', $id)->delete();
            DB::table('produk')->where('id_produk', $id)->delete();
        });
        $this->deleteProductImage($produk->foto_produk);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function inventory()
    {
        $produk = DB::table('produk')->orderBy('nama_produk')->paginate(12);
        $grades = DB::table('produk_grade as pg')
            ->join('produk as p', 'p.id_produk', '=', 'pg.id_produk')
            ->select('pg.id_produk_grade', 'pg.id_produk', 'pg.grade', 'pg.harga_per_kg', 'pg.stok_kg', 'p.nama_produk')
            ->orderBy('p.nama_produk')
            ->orderBy('pg.grade')
            ->get();

        return view('admin.inventory', compact('produk', 'grades'));
    }

    public function updateInventory(Request $request, int $id)
    {
        $data = $request->validate([
            'harga_produk' => 'required|numeric|min:0|max:9999999999.99',
            'stok_produk' => 'required|integer|min:0|max:2147483647',
        ]);

        $updated = DB::table('produk')->where('id_produk', $id)->update($data);
        abort_if(! $updated && ! DB::table('produk')->where('id_produk', $id)->exists(), 404);

        return back()->with('success', 'Harga dan stok berhasil diperbarui.');
    }

    public function updateGradeInventory(Request $request, int $id)
    {
        $data = $request->validate([
            'harga_per_kg' => 'required|numeric|min:0|max:9999999999.99',
            'stok_kg' => ['required', 'numeric', 'min:0', 'max:999999999.999', 'regex:/^\d+(?:\.\d{1,3})?$/'],
        ]);

        $updated = DB::table('produk_grade')->where('id_produk_grade', $id)->update($data);
        abort_if(! $updated && ! DB::table('produk_grade')->where('id_produk_grade', $id)->exists(), 404);

        return back()->with('success', 'Harga dan stok grade berhasil diperbarui.');
    }

    private function ensureProductGrades(int $idProduk): void
    {
        foreach (ProductCatalog::GRADE_PRICES as $grade => $price) {
            DB::table('produk_grade')->insertOrIgnore([
                'id_produk' => $idProduk,
                'grade' => $grade,
                'harga_per_kg' => $price,
                'stok_kg' => 0,
            ]);
        }
    }

    public function orders()
    {
        $pesanan = $this->orderQuery()
            ->orderByDesc('p.tanggal_pemesanan')
            ->orderByDesc('p.id_pesanan')
            ->paginate(12);

        return view('admin.orders', compact('pesanan'));
    }

    public function shippingRates()
    {
        $wilayahOngkir = DB::table('ongkir_wilayah')->orderBy('tarif')->get();

        return view('admin.shipping-rates', compact('wilayahOngkir'));
    }

    public function updateShippingRates(Request $request)
    {
        $rates = $request->input('tarif');

        if (! is_array($rates)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tarif' => 'Data tarif ongkir tidak valid.',
            ]);
        }

        foreach ($rates as $id => $rate) {
            $rate = trim((string) $rate);
            if (preg_match('/^\d{1,3}(?:\.\d{3})+$/D', $rate)) {
                $rate = str_replace('.', '', $rate);
            }
            $rates[$id] = $rate;
        }

        $request->merge(['tarif' => $rates]);
        $data = $request->validate([
            'tarif' => ['required', 'array'],
            'tarif.*' => ['required', 'integer', 'min:0', 'max:999999999'],
        ]);

        $knownIds = DB::table('ongkir_wilayah')
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
        if (array_diff(array_map('strval', array_keys($data['tarif'])), $knownIds)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'tarif' => 'Ada wilayah ongkir yang tidak dikenal.',
            ]);
        }

        DB::transaction(function () use ($data): void {
            foreach ($data['tarif'] as $id => $tarif) {
                DB::table('ongkir_wilayah')->where('id', $id)->update([
                    'tarif' => (int) $tarif,
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Tarif ongkir wilayah berhasil diperbarui.');
    }

    public function operationalSettings()
    {
        $settings = OperationalSchedule::settings();
        $dayLabels = OperationalSchedule::DAY_NAMES;

        return view('admin.operational-settings', compact('settings', 'dayLabels'));
    }

    public function updateOperationalSettings(Request $request)
    {
        $data = $request->validate([
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i', 'after:jam_buka'],
            'hari_operasional' => ['required', 'array', 'min:1'],
            'hari_operasional.*' => ['required', 'integer', 'between:1,7', 'distinct'],
            'tanggal_libur' => ['nullable', 'array', 'max:100'],
            'tanggal_libur.*' => ['nullable', 'date_format:Y-m-d', 'distinct'],
        ], [
            'jam_tutup.after' => 'Jam tutup harus setelah jam buka pada hari yang sama.',
            'hari_operasional.min' => 'Pilih setidaknya satu hari operasional.',
            'tanggal_libur.*.date_format' => 'Tanggal libur harus menggunakan format tanggal yang valid.',
        ]);

        $holidays = array_values(array_filter(
            $data['tanggal_libur'] ?? [],
            static fn ($date): bool => is_string($date) && $date !== ''
        ));
        sort($holidays);

        DB::table('pengaturan_operasional')
            ->where('id', 1)
            ->update([
                'jam_buka' => $data['jam_buka'],
                'jam_tutup' => $data['jam_tutup'],
                'hari_operasional' => json_encode(
                    array_values(array_unique(array_map('intval', $data['hari_operasional']))),
                    JSON_THROW_ON_ERROR
                ),
                'tanggal_libur' => json_encode($holidays, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Jam dan hari operasional berhasil diperbarui.');
    }

    public function orderDetail(int $id)
    {
        $pesanan = $this->orderQuery()
            ->where('p.id_pesanan', $id)
            ->first();
        abort_if(! $pesanan, 404);
        $pesanan->estimasi_tiba = DeliveryEstimate::forStoredOrder($pesanan);

        $detailPesanan = DB::table('detail_pesanan as dp')
            ->leftJoin('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->where('dp.id_pesanan', $id)
            ->select('dp.jumlah', 'dp.harga', 'dp.subtotal', 'dp.grade', 'dp.satuan', 'dp.berat_gram', 'pr.nama_produk', 'pr.foto_produk')
            ->get();

        return view('admin.order-detail', compact('pesanan', 'detailPesanan'));
    }

    public function paymentReviewPage(int $id)
    {
        $pesanan = $this->orderQuery()
            ->addSelect('pb.nama_pemilik_rekening_pengirim', 'pb.nomor_rekening_pengirim')
            ->where('p.id_pesanan', $id)
            ->first();
        abort_if(! $pesanan || ! $pesanan->status_pembayaran, 404);

        return view('admin.payment-review', compact('pesanan'));
    }

    public function updateOrder(Request $request, int $id)
    {
        $data = $request->validate([
            'status_pesanan' => ['required', Rule::in(['menunggu', 'diproses', 'selesai', 'dibatalkan'])],
        ]);
        abort_if(! DB::table('pesanan')->where('id_pesanan', $id)->exists(), 404);
        DB::table('pesanan')->where('id_pesanan', $id)->update($data);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function updateShippingCost(Request $request, int $id)
    {
        $ongkir = trim((string) $request->input('ongkir', ''));

        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/D', $ongkir)) {
            $ongkir = str_replace('.', '', $ongkir);
        }

        $request->merge(['ongkir' => $ongkir]);

        $data = $request->validate([
            'ongkir' => ['required', 'integer', 'min:0', 'max:999999999'],
        ]);

        $result = DB::transaction(function () use ($id, $data): string {
            $pesanan = DB::table('pesanan')
                ->where('id_pesanan', $id)
                ->lockForUpdate()
                ->first();
            abort_if(! $pesanan, 404);

            $pembayaran = DB::table('pembayaran')
                ->where('id_pesanan', $id)
                ->lockForUpdate()
                ->first();

            if (
                ($pembayaran && $pembayaran->status_pembayaran === 'diverifikasi')
                || ($pembayaran && $pembayaran->bukti_pembayaran)
            ) {
                return 'Ongkir tidak dapat diubah setelah bukti pembayaran diunggah atau pembayaran diverifikasi.';
            }

            $subtotalProduk = (float) DB::table('detail_pesanan')
                ->where('id_pesanan', $id)
                ->sum('subtotal');
            $requestedOngkir = (int) $data['ongkir'];
            $ongkir = ShippingZoneResolver::cost($requestedOngkir, $subtotalProduk);
            $total = round($subtotalProduk + $ongkir, 2);

            DB::table('pesanan')
                ->where('id_pesanan', $id)
                ->update([
                    'ongkir' => $ongkir,
                    'total_harga' => $total,
                ]);

            if ($pembayaran) {
                DB::table('pembayaran')
                    ->where('id_pesanan', $id)
                    ->update(['jumlah_bayar' => $total]);
            }

            return $ongkir < $requestedOngkir ? 'capped' : '';
        });

        if ($result !== '' && $result !== 'capped') {
            return back()->with('error', $result);
        }

        return back()->with(
            'success',
            $result === 'capped'
                ? 'Ongkir melebihi subtotal produk, sehingga dibatasi sampai subtotal tersebut.'
                : 'Ongkir dan total pembayaran berhasil diperbarui.'
        );
    }

    public function reviewPayment(Request $request, int $id)
    {
        $data = $request->validate([
            'status_pembayaran' => ['required', Rule::in(['diverifikasi', 'ditolak'])],
        ]);

        $result = DB::transaction(function () use ($id, $data): string {
            $pesanan = DB::table('pesanan')
                ->where('id_pesanan', $id)
                ->lockForUpdate()
                ->first();
            abort_if(! $pesanan, 404);

            if ($pesanan->ongkir === null) {
                return 'Ongkir belum ditetapkan Admin. Pembayaran belum dapat diverifikasi.';
            }

            $pembayaran = DB::table('pembayaran')
                ->where('id_pesanan', $id)
                ->lockForUpdate()
                ->first();
            abort_if(! $pembayaran, 404);

            if (! $pembayaran->bukti_pembayaran) {
                return 'Bukti pembayaran belum diunggah.';
            }

            if ($pembayaran->status_pembayaran !== 'menunggu') {
                return 'Pembayaran ini sudah diproses sebelumnya.';
            }

            $updates = ['status_pembayaran' => $data['status_pembayaran']];

            if ($data['status_pembayaran'] === 'diverifikasi') {
                $tanggalVerifikasi = now();
                $updates['nomor_struk'] = sprintf(
                    'SFS-%s-%06d',
                    $tanggalVerifikasi->format('Ymd'),
                    $pembayaran->id_pembayaran
                );
                $updates['tanggal_verifikasi'] = $tanggalVerifikasi;

                DB::table('pesanan')
                    ->where('id_pesanan', $id)
                    ->where('status_pesanan', 'menunggu')
                    ->update(['status_pesanan' => 'diproses']);
            }

            DB::table('pembayaran')
                ->where('id_pembayaran', $pembayaran->id_pembayaran)
                ->update($updates);

            return '';
        });

        if ($result !== '') {
            return back()->with('error', $result);
        }

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function paymentProof(int $id)
    {
        $payment = DB::table('pembayaran')->where('id_pesanan', $id)->first();
        abort_if(
            ! $payment
                || ! $payment->bukti_pembayaran
                || ! str_starts_with($payment->bukti_pembayaran, 'bukti-pembayaran/')
                || ! Storage::disk('local')->exists($payment->bukti_pembayaran),
            404
        );

        return response()->file(Storage::disk('local')->path($payment->bukti_pembayaran));
    }

    public function shipments()
    {
        $pengirimanTerakhir = DB::table('pengiriman')
            ->select('id_pesanan', DB::raw('MAX(id_pengiriman) as id_pengiriman'))
            ->groupBy('id_pesanan');

        $pesanan = DB::table('pesanan as p')
            ->join('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->leftJoinSub($pengirimanTerakhir, 'pt', function ($join) {
                $join->on('pt.id_pesanan', '=', 'p.id_pesanan');
            })
            ->leftJoin('pengiriman as pg', 'pg.id_pengiriman', '=', 'pt.id_pengiriman')
            ->leftJoin('petugas as kurir', 'kurir.id_petugas', '=', 'pg.id_petugas')
            ->where('pb.status_pembayaran', 'diverifikasi')
            ->select(
                'p.id_pesanan',
                'p.nama_penerima',
                'p.no_hp',
                'p.alamat_pengiriman',
                'p.status_pesanan',
                'p.tanggal_pemesanan',
                'pg.id_pengiriman',
                'pg.status_pengiriman',
                'pg.id_petugas',
                'kurir.nama_petugas as nama_kurir'
            )
            ->orderByDesc('p.tanggal_pemesanan')
            ->paginate(12);

        $kurir = DB::table('petugas')->where('role', 'Kurir')->orderBy('nama_petugas')->get();

        return view('admin.shipments', compact('pesanan', 'kurir'));
    }

    public function assignCourier(Request $request, int $id)
    {
        $data = $request->validate([
            'id_petugas' => ['required', Rule::exists('petugas', 'id_petugas')->where('role', 'Kurir')],
        ]);

        $order = DB::table('pesanan as p')
            ->join('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->where('p.id_pesanan', $id)
            ->where('pb.status_pembayaran', 'diverifikasi')
            ->select('p.*')
            ->first();
        abort_if(! $order, 404);

        DB::transaction(function () use ($order, $data) {
            $shipment = DB::table('pengiriman')
                ->where('id_pesanan', $order->id_pesanan)
                ->orderByDesc('id_pengiriman')
                ->first();

            $shipmentData = [
                'id_petugas' => $data['id_petugas'],
                'nama_penerima' => $order->nama_penerima,
                'no_telp_penerima' => $order->no_hp,
                'alamat_pengiriman' => $order->alamat_pengiriman,
                'status_pengiriman' => $shipment->status_pengiriman ?? 'menunggu',
            ];

            if ($shipment) {
                DB::table('pengiriman')->where('id_pengiriman', $shipment->id_pengiriman)->update($shipmentData);
            } else {
                $shipmentData['id_pesanan'] = $order->id_pesanan;
                DB::table('pengiriman')->insert($shipmentData);
            }
        });

        return back()->with('success', 'Kurir berhasil ditugaskan.');
    }

    public function updateShipment(Request $request, int $id)
    {
        $data = $request->validate([
            'status_pengiriman' => ['required', Rule::in(['menunggu', 'diproses', 'dikirim', 'diterima'])],
        ]);

        $shipment = DB::table('pengiriman')->where('id_pengiriman', $id)->first();
        abort_if(! $shipment, 404);

        DB::transaction(function () use ($shipment, $data) {
            DB::table('pengiriman')->where('id_pengiriman', $shipment->id_pengiriman)->update([
                'status_pengiriman' => $data['status_pengiriman'],
                'tanggal_pengiriman' => in_array($data['status_pengiriman'], ['dikirim', 'diterima'], true)
                    ? ($shipment->tanggal_pengiriman ?? now())
                    : $shipment->tanggal_pengiriman,
            ]);

            if ($data['status_pengiriman'] === 'diterima') {
                DB::table('pesanan')->where('id_pesanan', $shipment->id_pesanan)->update(['status_pesanan' => 'selesai']);
            } elseif (in_array($data['status_pengiriman'], ['diproses', 'dikirim'], true)) {
                DB::table('pesanan')->where('id_pesanan', $shipment->id_pesanan)
                    ->where('status_pesanan', 'menunggu')
                    ->update(['status_pesanan' => 'diproses']);
            }
        });

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }

    public function reports(Request $request)
    {
        return view('admin.reports', $this->reportViewData($request, true));
    }

    public function printReports(Request $request)
    {
        $filterOptions = $this->reportFilterOptions();
        $filters = $this->validateReportFilters($request, $filterOptions);
        $from = $filters['dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['sampai'] ?? now()->toDateString();
        $transaksi = $this->reportQuery($from, $to, $filters)
            ->leftJoin('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->select(
                'p.id_pesanan',
                'p.tanggal_pemesanan',
                'pl.nama_pelanggan',
                'p.total_harga',
                'p.ongkir',
                DB::raw('CASE WHEN p.ongkir IS NULL THEN p.total_harga ELSE p.total_harga - p.ongkir END as subtotal_produk'),
                'p.status_pesanan',
                'pb.metode_pembayaran',
                'pb.status_pembayaran'
            )
            ->orderBy(
                [
                    'tanggal' => 'p.tanggal_pemesanan',
                    'pesanan' => 'p.id_pesanan',
                    'total' => 'p.total_harga',
                ][$filters['urutkan'] ?? 'tanggal'],
                $filters['arah'] ?? 'desc'
            )
            ->orderByDesc('p.id_pesanan')
            ->get();
        $this->attachReportProductSummaries($transaksi);

        return view('admin.reports-print', compact('transaksi', 'from', 'to'));
    }

    public function exportReports(Request $request)
    {
        $filters = $this->validateReportFilters($request, $this->reportFilterOptions());
        $from = $filters['dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['sampai'] ?? now()->toDateString();
        $rows = $this->reportQuery($from, $to, $filters)
            ->leftJoin('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->select(
                'p.id_pesanan',
                'p.tanggal_pemesanan',
                'pl.nama_pelanggan',
                'p.total_harga',
                'p.ongkir',
                'p.wilayah_ongkir',
                DB::raw('CASE WHEN p.ongkir IS NULL THEN p.total_harga ELSE p.total_harga - p.ongkir END as subtotal_produk'),
                'p.status_pesanan',
                'pb.metode_pembayaran',
                'pb.status_pembayaran'
            )
            ->orderBy('p.tanggal_pemesanan')
            ->get();
        $this->attachReportProductSummaries($rows);

        $directory = storage_path('framework/cache');
        File::ensureDirectoryExists($directory);
        $filePath = tempnam($directory, 'sales-report-');

        if ($filePath === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara untuk laporan Excel.');
        }

        if (! unlink($filePath)) {
            throw new \RuntimeException('Tidak dapat menyiapkan file sementara untuk laporan Excel.');
        }

        $filePath .= '.zip';
        $this->writeSalesWorkbook($filePath, $rows, $from, $to);

        return response()->download(
            $filePath,
            'laporan-penjualan-'.$from.'-'.$to.'.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    public function staff()
    {
        $petugas = DB::table('petugas')
            ->select(
                'id_petugas',
                'nama_petugas',
                'username',
                'no_telp',
                'alamat',
                'role',
                'status_akun',
                'password_reset_requested_at',
                'password_reset_status'
            )
            ->orderBy('role')
            ->orderByRaw("CASE WHEN status_akun = 'Menunggu Persetujuan' THEN 0 ELSE 1 END")
            ->orderBy('nama_petugas')
            ->paginate(15);

        $adminRecoveryRequests = DB::table('permintaan_reset_password_admin as recovery')
            ->join('petugas as requester', 'requester.id_petugas', '=', 'recovery.admin_id')
            ->where('recovery.status', 'Menunggu Verifikasi')
            ->where('recovery.expires_at', '>', now())
            ->where('recovery.admin_id', '<>', (int) session('id_petugas'))
            ->select(
                'recovery.id',
                'recovery.requested_at',
                'requester.nama_petugas',
                'requester.username'
            )
            ->orderBy('recovery.requested_at')
            ->get();

        return view('admin.staff', compact('petugas', 'adminRecoveryRequests'));
    }

    public function approveCourierPasswordReset(int $id)
    {
        $temporaryPassword = Str::random(16);
        $approved = DB::transaction(function () use ($id, $temporaryPassword): bool {
            $courier = DB::table('petugas')
                ->where('id_petugas', $id)
                ->where('role', 'Kurir')
                ->lockForUpdate()
                ->first();

            if (! $courier || $courier->password_reset_status !== 'Menunggu Verifikasi') {
                return false;
            }

            DB::table('petugas')
                ->where('id_petugas', $id)
                ->update([
                    'password' => Hash::make($temporaryPassword),
                    'must_change_password' => true,
                    'password_reset_status' => 'Disetujui',
                ]);

            return true;
        });

        abort_if(! $approved, 404);

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Permintaan reset disetujui. Berikan password sementara ini langsung kepada kurir.')
            ->with('courier_temporary_password', $temporaryPassword);
    }

    public function rejectCourierPasswordReset(int $id)
    {
        $rejected = DB::table('petugas')
            ->where('id_petugas', $id)
            ->where('role', 'Kurir')
            ->where('password_reset_status', 'Menunggu Verifikasi')
            ->update(['password_reset_status' => 'Ditolak']);

        abort_if(! $rejected, 404);

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Permintaan reset password kurir ditolak.');
    }

    public function storeStaff(Request $request)
    {
        $data = $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:petugas,username',
            'password' => 'required|string|min:8|confirmed',
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:1000',
            'role' => ['required', Rule::in(['Admin', 'Kurir'])],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status_akun'] = 'Aktif';
        DB::table('petugas')->insert($data);

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Petugas baru berhasil ditambahkan.');
    }

    public function approveCourier(int $id)
    {
        $updated = DB::table('petugas')
            ->where('id_petugas', $id)
            ->where('role', 'Kurir')
            ->where('status_akun', 'Menunggu Persetujuan')
            ->update(['status_akun' => 'Aktif']);

        abort_if(! $updated, 404);

        return redirect()
            ->route('admin.staff')
            ->with('success', 'Akun Kurir berhasil disetujui.');
    }

    public function profile()
    {
        $petugas = $this->currentAdmin();

        return view('admin.profile', compact('petugas'));
    }

    public function updateProfile(Request $request)
    {
        $petugas = $this->currentAdmin();
        $data = $request->validate([
            'nama_petugas' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('petugas', 'username')->ignore($petugas->id_petugas, 'id_petugas'),
            ],
            'no_telp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::table('petugas')->where('id_petugas', $petugas->id_petugas)->update($data);
        session(['nama_petugas' => $data['nama_petugas']]);

        return redirect()
            ->route('admin.profile')
            ->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function logout(Request $request)
    {
        $cookie = $request->cookie('admin_password_recovery_v2');
        $recoveryRequest = null;

        if (
            is_string($cookie)
            && preg_match('/^([1-9][0-9]*):([a-f0-9]{64})$/D', $cookie, $matches)
        ) {
            $recoveryRequest = DB::table('permintaan_reset_password_admin')
                ->where('id', (int) $matches[1])
                ->where('status', 'Disetujui')
                ->where('expires_at', '>', now())
                ->where('reviewer_id', (int) $request->session()->get('id_petugas'))
                ->first();

            if (
                $recoveryRequest
                && (
                    (int) $recoveryRequest->admin_id === (int) $request->session()->get('id_petugas')
                    || ! hash_equals(
                        (string) $recoveryRequest->token_hash,
                        hash('sha256', $matches[2])
                    )
                    || ! DB::table('petugas')
                        ->where('id_petugas', $recoveryRequest->admin_id)
                        ->where('role', 'Admin')
                        ->where('status_akun', 'Aktif')
                        ->exists()
                )
            ) {
                $recoveryRequest = null;
            }
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($recoveryRequest) {
            return redirect()
                ->route('admin.password.reset')
                ->with('success', 'Permintaan disetujui. Silakan buat password baru untuk akun Admin pemohon.');
        }

        return redirect()->route('login')->with('success', 'Kamu berhasil keluar dari akun.');
    }

    private function currentAdmin(): object
    {
        $petugas = DB::table('petugas')
            ->where('id_petugas', session('id_petugas'))
            ->where('role', 'Admin')
            ->first();
        abort_if(! $petugas, 404);

        return $petugas;
    }

    private function orderQuery(): \Illuminate\Database\Query\Builder
    {
        $query = DB::table('pesanan as p');
        $query->join('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan');
        $query->leftJoin('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan');
        $query->select([
            'p.id_pesanan',
            'p.nama_penerima',
            'p.alamat_pengiriman',
            'p.tanggal_pemesanan',
            'p.jadwal_pengiriman',
            'p.total_harga',
            'p.ongkir',
            'p.wilayah_ongkir',
            DB::raw('CASE WHEN p.ongkir IS NULL THEN p.total_harga ELSE p.total_harga - p.ongkir END as subtotal_produk'),
            'p.status_pesanan',
            'pl.nama_pelanggan',
            'pb.bukti_pembayaran',
            'pb.metode_pembayaran',
            'pb.status_pembayaran',
            'pb.nomor_struk',
        ]);

        return $query;
    }

    private function reportQuery(string $from, string $to, array $filters = [])
    {
        return DB::table('pesanan as p')
            ->leftJoin('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->whereDate('p.tanggal_pemesanan', '>=', $from)
            ->whereDate('p.tanggal_pemesanan', '<=', $to)
            ->when(
                $filters['metode_pembayaran'] ?? null,
                fn ($query, $method) => $query->where('pb.metode_pembayaran', $method)
            )
            ->when(
                $filters['status_pesanan'] ?? null,
                fn ($query, $status) => $query->where('p.status_pesanan', $status)
            )
            ->when(
                isset($filters['nominal_min']),
                fn ($query) => $query->where('p.total_harga', '>=', $filters['nominal_min'])
            )
            ->when(
                isset($filters['nominal_max']),
                fn ($query) => $query->where('p.total_harga', '<=', $filters['nominal_max'])
            )
            ->when($filters['q'] ?? null, function ($query, string $term): void {
                $query->where(function ($builder) use ($term): void {
                    $builder->whereExists(function ($customerQuery) use ($term): void {
                        $customerQuery->selectRaw('1')
                            ->from('pelanggan as search_pl')
                            ->whereColumn('search_pl.id_pelanggan', 'p.id_pelanggan')
                            ->where('search_pl.nama_pelanggan', 'like', '%'.$term.'%');
                    })->orWhere('pb.nomor_struk', 'like', '%'.$term.'%');

                    if (ctype_digit($term)) {
                        $builder->orWhere('p.id_pesanan', (int) $term);
                    }
                });
            });
    }

    private function reportViewData(Request $request, bool $paginate): array
    {
        $filterOptions = $this->reportFilterOptions();
        $filters = $this->validateReportFilters($request, $filterOptions);
        $from = $filters['dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['sampai'] ?? now()->toDateString();
        $period = $filters['periode'] ?? $this->defaultReportPeriod($from, $to);
        $query = $this->reportQuery($from, $to, $filters);
        $paidQuery = (clone $query)
            ->where('pb.status_pembayaran', 'diverifikasi')
            ->where(function ($builder): void {
                $builder->whereNull('p.status_pesanan')
                    ->orWhere('p.status_pesanan', '!=', 'dibatalkan');
            });

        $ringkasan = [
            'total_transaksi' => (clone $paidQuery)->distinct()->count('p.id_pesanan'),
            'total_pendapatan' => (float) (clone $paidQuery)->sum('pb.jumlah_bayar'),
            'jumlah_pesanan' => (clone $query)->distinct()->count('p.id_pesanan'),
            'produk_terjual' => (int) (clone $paidQuery)
                ->join('detail_pesanan as sold_details', 'sold_details.id_pesanan', '=', 'p.id_pesanan')
                ->sum('sold_details.jumlah'),
        ];

        $produkTerlaris = (clone $paidQuery)
            ->join('detail_pesanan as dp', 'dp.id_pesanan', '=', 'p.id_pesanan')
            ->join('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->select('pr.id_produk', 'pr.nama_produk')
            ->selectRaw('SUM(dp.jumlah) as jumlah_terjual')
            ->groupBy('pr.id_produk', 'pr.nama_produk')
            ->orderByDesc('jumlah_terjual')
            ->limit(5)
            ->get();

        $periodExpression = $this->reportPeriodExpression($period);
        $periodRows = (clone $query)
            ->selectRaw($periodExpression.' as bucket')
            ->selectRaw('COUNT(DISTINCT p.id_pesanan) as jumlah_pesanan')
            ->selectRaw(
                "SUM(CASE WHEN pb.status_pembayaran = 'diverifikasi' "
                ."AND (p.status_pesanan IS NULL OR p.status_pesanan != 'dibatalkan') "
                .'THEN COALESCE(pb.jumlah_bayar, 0) ELSE 0 END) as total_pendapatan'
            )
            ->groupByRaw($periodExpression)
            ->orderBy('bucket')
            ->get();
        $grafikPeriode = $this->fillReportPeriods($from, $to, $period, $periodRows);

        $rowsQuery = (clone $query)
            ->leftJoin('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->select(
                'p.id_pesanan',
                'p.tanggal_pemesanan',
                'pl.nama_pelanggan',
                'p.total_harga',
                'p.ongkir',
                'p.wilayah_ongkir',
                DB::raw('CASE WHEN p.ongkir IS NULL THEN p.total_harga ELSE p.total_harga - p.ongkir END as subtotal_produk'),
                DB::raw('(SELECT COALESCE(SUM(dp.jumlah), 0) FROM detail_pesanan dp WHERE dp.id_pesanan = p.id_pesanan) as jumlah_produk'),
                'p.status_pesanan',
                'pb.metode_pembayaran',
                'pb.status_pembayaran',
                'pb.nomor_struk'
            );
        $sortColumns = [
            'tanggal' => 'p.tanggal_pemesanan',
            'pesanan' => 'p.id_pesanan',
            'total' => 'p.total_harga',
        ];
        $rowsQuery->orderBy($sortColumns[$filters['urutkan'] ?? 'tanggal'], $filters['arah'] ?? 'desc')
            ->orderByDesc('p.id_pesanan');
        $laporan = $paginate
            ? $rowsQuery->paginate(15)->withQueryString()
            : $rowsQuery->get();

        $now = now();
        $quickRanges = [
            'harian' => ['label' => 'Hari ini', 'from' => $now->toDateString(), 'to' => $now->toDateString()],
            'mingguan' => ['label' => 'Minggu ini', 'from' => $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString(), 'to' => $now->toDateString()],
            'bulanan' => ['label' => 'Bulan ini', 'from' => $now->copy()->startOfMonth()->toDateString(), 'to' => $now->toDateString()],
            'tahunan' => ['label' => 'Tahun ini', 'from' => $now->copy()->startOfYear()->toDateString(), 'to' => $now->toDateString()],
        ];

        return compact(
            'ringkasan',
            'laporan',
            'from',
            'to',
            'period',
            'grafikPeriode',
            'produkTerlaris',
            'filterOptions',
            'filters',
            'quickRanges'
        );
    }

    private function defaultReportPeriod(string $from, string $to): string
    {
        $days = Carbon::parse($from)->diffInDays(Carbon::parse($to));

        return match (true) {
            $days <= 31 => 'harian',
            $days <= 120 => 'mingguan',
            $days <= 730 => 'bulanan',
            default => 'tahunan',
        };
    }

    private function reportPeriodExpression(string $period): string
    {
        $sqlite = DB::connection()->getDriverName() === 'sqlite';

        return match ($period) {
            'harian' => 'DATE(p.tanggal_pemesanan)',
            'mingguan' => $sqlite
                ? "date(p.tanggal_pemesanan, '-' || ((CAST(strftime('%w', p.tanggal_pemesanan) AS INTEGER) + 6) % 7) || ' days')"
                : 'DATE_SUB(DATE(p.tanggal_pemesanan), INTERVAL WEEKDAY(p.tanggal_pemesanan) DAY)',
            'bulanan' => $sqlite
                ? "strftime('%Y-%m', p.tanggal_pemesanan)"
                : "DATE_FORMAT(p.tanggal_pemesanan, '%Y-%m')",
            'tahunan' => $sqlite
                ? "strftime('%Y', p.tanggal_pemesanan)"
                : 'YEAR(p.tanggal_pemesanan)',
        };
    }

    private function fillReportPeriods(
        string $from,
        string $to,
        string $period,
        \Illuminate\Support\Collection $periodRows
    )
    {
        $totals = $periodRows->keyBy('bucket');
        $current = Carbon::parse($from);
        $end = Carbon::parse($to);

        switch ($period) {
            case 'mingguan':
                $current->startOfWeek(Carbon::MONDAY);
                break;
            case 'bulanan':
                $current->startOfMonth();
                break;
            case 'tahunan':
                $current->startOfYear();
                break;
            default:
                $current->startOfDay();
        }

        $points = collect();
        while ($current <= $end) {
            $key = match ($period) {
                'harian' => $current->format('Y-m-d'),
                'mingguan' => $current->format('Y-m-d'),
                'bulanan' => $current->format('Y-m'),
                'tahunan' => $current->format('Y'),
            };
            $row = $totals->get($key);
            $label = match ($period) {
                'harian' => $current->format('d/m'),
                'mingguan' => $current->format('d M'),
                'bulanan' => $current->translatedFormat('M Y'),
                'tahunan' => $current->format('Y'),
            };

            $points->push((object) [
                'label' => $label,
                'pendapatan' => (float) ($row->total_pendapatan ?? 0),
                'pesanan' => (int) ($row->jumlah_pesanan ?? 0),
            ]);

            match ($period) {
                'harian' => $current->addDay(),
                'mingguan' => $current->addWeek(),
                'bulanan' => $current->addMonth(),
                'tahunan' => $current->addYear(),
            };
        }

        return $points;
    }

    private function reportFilterOptions(): array
    {
        return [
            'metode_pembayaran' => DB::table('pembayaran')
                ->whereNotNull('metode_pembayaran')
                ->distinct()
                ->orderBy('metode_pembayaran')
                ->pluck('metode_pembayaran')
                ->all(),
            'status_pesanan' => DB::table('pesanan')
                ->whereNotNull('status_pesanan')
                ->distinct()
                ->orderBy('status_pesanan')
                ->pluck('status_pesanan')
                ->all(),
        ];
    }

    private function validateReportFilters(Request $request, array $filterOptions): array
    {
        $filters = $request->validate([
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
            'metode_pembayaran' => ['nullable', Rule::in($filterOptions['metode_pembayaran'])],
            'status_pesanan' => ['nullable', Rule::in($filterOptions['status_pesanan'])],
            'nominal_min' => ['nullable', 'numeric', 'min:0'],
            'nominal_max' => ['nullable', 'numeric', 'min:0'],
            'periode' => ['nullable', Rule::in(['harian', 'mingguan', 'bulanan', 'tahunan'])],
            'q' => ['nullable', 'string', 'max:100'],
            'urutkan' => ['nullable', Rule::in(['tanggal', 'pesanan', 'total'])],
            'arah' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        if (
            isset($filters['nominal_min'], $filters['nominal_max'])
            && (float) $filters['nominal_max'] < (float) $filters['nominal_min']
        ) {
            throw ValidationException::withMessages([
                'nominal_max' => 'Nominal maksimum harus lebih besar atau sama dengan nominal minimum.',
            ]);
        }

        return $filters;
    }

    private function attachReportProductSummaries(iterable $orders): void
    {
        $orderIds = collect($orders)->pluck('id_pesanan');
        $detailsByOrder = DB::table('detail_pesanan as dp')
            ->leftJoin('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->whereIn('dp.id_pesanan', $orderIds)
            ->select(
                'dp.id_pesanan',
                'dp.jumlah',
                'dp.grade',
                'dp.harga',
                'dp.berat_gram',
                'pr.nama_produk'
            )
            ->get()
            ->groupBy('id_pesanan');

        foreach ($orders as $order) {
            $order->ringkasan_produk = ($detailsByOrder->get($order->id_pesanan) ?? collect())
                ->map(function (object $detail): string {
                    $name = $detail->nama_produk ?: 'Produk tidak tersedia';
                    if ($detail->grade) {
                        return $name.' Grade '.$detail->grade.' · '.$detail->jumlah.' kg × Rp'.number_format((float) $detail->harga, 0, ',', '.').'/kg';
                    }

                    $weight = $detail->berat_gram
                        ? ' · '.$detail->berat_gram.' gram/kemasan'
                        : '';

                    $unit = $detail->berat_gram ? 'kemasan' : 'pcs';

                    return $name.' · '.$detail->jumlah.' '.$unit.$weight.' × Rp'.number_format((float) $detail->harga, 0, ',', '.').'/'.$unit;
                })
                ->implode('; ');
        }
    }

private function writeSalesWorkbook(string $filePath, iterable $rows, string $from, string $to): void
{
    $headers = [
        'ID Pesanan',
        'Tanggal',
        'Pelanggan',
        'Rincian Produk',
        'Subtotal Produk (Rp)',
        'Ongkir (Rp)',
        'Total Pembayaran (Rp)',
        'Status Pesanan',
        'Metode Pembayaran',
        'Status Pembayaran',
    ];

    $tableRows = [];

    foreach ($rows as $row) {
        $tableRows[] = [
            ['value' => (int) $row->id_pesanan, 'type' => 'number'],
            [
                'value' => $row->tanggal_pemesanan
                    ? date('d/m/Y H:i', strtotime($row->tanggal_pemesanan))
                    : '',
                'type' => 'string',
            ],
            ['value' => $row->nama_pelanggan ?? '', 'type' => 'string'],
            ['value' => $row->ringkasan_produk ?? '', 'type' => 'string'],
            [
                'value' => (float) ($row->subtotal_produk ?? $row->total_harga ?? 0),
                'type' => 'number',
                'style' => 2,
            ],
            [
                'value' => $row->ongkir === null ? 'Menunggu Admin' : (float) $row->ongkir,
                'type' => $row->ongkir === null ? 'string' : 'number',
                'style' => 2,
            ],
            [
                'value' => (float) ($row->total_harga ?? 0),
                'type' => 'number',
                'style' => 2,
            ],
            ['value' => $row->status_pesanan ?? '', 'type' => 'string'],
            ['value' => $row->metode_pembayaran ?? '', 'type' => 'string'],
            ['value' => $row->status_pembayaran ?? '', 'type' => 'string'],
        ];
    }

    $logoPath = public_path('images/logo samudera.jpeg');
    $hasLogo = is_file($logoPath) && is_readable($logoPath);

    $worksheetRows = [
        '<row r="1" ht="45" customHeight="1">'
            . '<c r="B1" t="inlineStr" s="3"><is><t>Samudera Fresh Strawberry</t></is></c>'
            . '</row>',
        '<row r="2" ht="20" customHeight="1">'
            . '<c r="B2" t="inlineStr" s="4"><is><t>Telepon: 085678990652</t></is></c>'
            . '</row>',
        '<row r="3" ht="30" customHeight="1">'
            . '<c r="B3" t="inlineStr" s="4"><is><t>'
            . $this->escapeXml('Alamat: Kp. Mekar Baru RW 03 RT 02, Alamendah, Rancabali, Kabupaten Bandung')
            . '</t></is></c>'
            . '</row>',
        '<row r="4" ht="10" customHeight="1"></row>',
        '<row r="5" ht="28" customHeight="1">'
            . '<c r="A5" t="inlineStr" s="3"><is><t>LAPORAN PENJUALAN</t></is></c>'
            . '</row>',
        '<row r="6" ht="22" customHeight="1">'
            . '<c r="A6" t="inlineStr" s="4"><is><t>'
            . $this->escapeXml('Periode: '.$from.' sampai '.$to)
            . '</t></is></c>'
            . '</row>',
    ];

    $headerCells = '';

    foreach ($headers as $index => $header) {
        $reference = chr(65 + $index).'7';

        $headerCells .= '<c r="'.$reference.'" t="inlineStr" s="1">'
            . '<is><t>'.$this->escapeXml($header).'</t></is></c>';
    }

    $worksheetRows[] = '<row r="7" ht="30" customHeight="1">'
        .$headerCells.'</row>';

    foreach ($tableRows as $rowIndex => $values) {
        $excelRow = $rowIndex + 8;
        $cells = '';

        foreach ($values as $columnIndex => $cell) {
            $reference = chr(65 + $columnIndex).$excelRow;
            $style = isset($cell['style'])
                ? ' s="'.$cell['style'].'"'
                : '';

            if ($cell['type'] === 'number') {
                $cells .= '<c r="'.$reference.'"'.$style.'>'
                    .'<v>'.$cell['value'].'</v></c>';
            } else {
                $cells .= '<c r="'.$reference.'" t="inlineStr"'.$style.'>'
                    .'<is><t>'.$this->escapeXml((string) $cell['value'])
                    .'</t></is></c>';
            }
        }

        $worksheetRows[] = '<row r="'.$excelRow.'">'.$cells.'</row>';
    }

    $lastRow = max(7, count($tableRows) + 7);

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
        .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        .'<dimension ref="A1:J'.$lastRow.'"/>'
        .'<sheetViews><sheetView workbookViewId="0">'
        .'<pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/>'
        .'<selection pane="bottomLeft" activeCell="A8" sqref="A8"/>'
        .'</sheetView></sheetViews>'
        .'<sheetFormatPr defaultRowHeight="15"/>'
        .'<cols>'
        .'<col min="1" max="1" width="14" customWidth="1"/>'
        .'<col min="2" max="2" width="20" customWidth="1"/>'
        .'<col min="3" max="3" width="28" customWidth="1"/>'
        .'<col min="4" max="4" width="55" customWidth="1"/>'
        .'<col min="5" max="9" width="22" customWidth="1"/>'
        .'<col min="10" max="10" width="22" customWidth="1"/>'
        .'</cols>'
        .'<sheetData>'.implode('', $worksheetRows).'</sheetData>'
        .'<autoFilter ref="A7:J'.$lastRow.'"/>'
        .'<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
        .($hasLogo ? '<drawing r:id="rId1"/>' : '')
        .'</worksheet>';

    $files = [
        '[Content_Types].xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .($hasLogo ? '<Default Extension="jpg" ContentType="image/jpeg"/>' : '')
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .($hasLogo ? '<Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>' : '')
            .'</Types>',

        '_rels/.rels' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>',

        'xl/workbook.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<bookViews><workbookView/></bookViews>'
            .'<sheets><sheet name="Laporan Penjualan" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>',

        'xl/_rels/workbook.xml.rels' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>',

        'xl/styles.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="4">'
            .'<font><name val="Arial"/><sz val="11"/></font>'
            .'<font><name val="Arial"/><b/><color rgb="FFFFFFFF"/><sz val="11"/></font>'
            .'<font><name val="Arial"/><b/><color rgb="FF174C32"/><sz val="15"/></font>'
            .'<font><name val="Arial"/><color rgb="FF444444"/><sz val="10"/></font>'
            .'</fonts>'
            .'<fills count="3">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF174C32"/><bgColor indexed="64"/></patternFill></fill>'
            .'</fills>'
            .'<borders count="2">'
            .'<border><left/><right/><top/><bottom/><diagonal/></border>'
            .'<border><left style="thin"><color rgb="FFEEE9DE"/></left><right style="thin"><color rgb="FFEEE9DE"/></right><top style="thin"><color rgb="FFEEE9DE"/></top><bottom style="thin"><color rgb="FFEEE9DE"/></bottom><diagonal/></border>'
            .'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="5">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyAlignment="1"><alignment vertical="center" horizontal="center" wrapText="1"/></xf>'
            .'<xf numFmtId="3" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>',

        'xl/worksheets/sheet1.xml' => $sheetXml,
    ];

    if ($hasLogo) {
        $files['xl/worksheets/_rels/sheet1.xml.rels'] =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/>'
            .'</Relationships>';

        $files['xl/drawings/drawing1.xml'] =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing"'
            .' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
            .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<xdr:oneCellAnchor>'
            .'<xdr:from><xdr:col>0</xdr:col><xdr:colOff>50000</xdr:colOff><xdr:row>0</xdr:row><xdr:rowOff>50000</xdr:rowOff></xdr:from>'
            .'<xdr:ext cx="450000" cy="450000"/>'
            .'<xdr:pic><xdr:nvPicPr><xdr:cNvPr id="1" name="Logo Samudera Fresh Strawberry"/><xdr:cNvPicPr/></xdr:nvPicPr>'
            .'<xdr:blipFill><a:blip r:embed="rId1"/><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'
            .'<xdr:spPr><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></xdr:spPr>'
            .'</xdr:pic><xdr:clientData/></xdr:oneCellAnchor>'
            .'</xdr:wsDr>';

        $files['xl/drawings/_rels/drawing1.xml.rels'] =
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/logo-samudera.jpg"/>'
            .'</Relationships>';

        $files['xl/media/logo-samudera.jpg'] = file_get_contents($logoPath);
    }

    if (class_exists(\ZipArchive::class)) {
        $zip = new \ZipArchive();
        $result = $zip->open($filePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        if ($result !== true) {
            @unlink($filePath);
            throw new \RuntimeException('Tidak dapat membangun file Excel laporan.');
        }

        foreach ($files as $name => $contents) {
            if (! $zip->addFromString($name, $contents)) {
                $zip->close();
                @unlink($filePath);
                throw new \RuntimeException('Gagal menulis bagian '.$name.' pada file Excel.');
            }
        }

        if (! $zip->close()) {
            @unlink($filePath);
            throw new \RuntimeException('Gagal menyelesaikan file Excel laporan.');
        }

        return;
    }

    if (! class_exists(\PharData::class)) {
        @unlink($filePath);
        throw new \RuntimeException('Ekspor Excel memerlukan ekstensi PHP zip atau phar.');
    }

    try {
        $archive = new \PharData($filePath, 0, null, \Phar::ZIP);

        foreach ($files as $name => $contents) {
            $archive->addFromString($name, $contents);
        }

        unset($archive);
    } catch (\Throwable $exception) {
        unset($archive);
        @unlink($filePath);
        throw $exception;
    }
}

    private function escapeXml(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $value) ?? '';

        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT | ENT_SUBSTITUTE, 'UTF-8');
    }

    private function storeProductImage(Request $request): ?string
    {
        if (! $request->hasFile('foto_produk')) {
            return null;
        }

        $image = $request->file('foto_produk');
        $directory = public_path('images/products');
        File::ensureDirectoryExists($directory);
        $fileName = bin2hex(random_bytes(16)).'.'.$image->extension();
        $image->move($directory, $fileName);

        return 'products/'.$fileName;
    }

    private function deleteProductImage(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'products/')) {
            return;
        }

        $directory = realpath(public_path('images/products'));
        $file = realpath(public_path('images/'.$path));

        if ($directory && $file && str_starts_with($file, $directory.DIRECTORY_SEPARATOR) && is_file($file)) {
            unlink($file);
        }
    }
}