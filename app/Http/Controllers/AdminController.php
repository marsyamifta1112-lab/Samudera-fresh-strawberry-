<?php

namespace App\Http\Controllers;

use App\Support\ShippingZoneResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

    public function bestSellers()
    {
        $produkTerlaris = DB::table('detail_pesanan as dp')
            ->join('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->select(
                'pr.id_produk',
                'pr.nama_produk',
                'pr.foto_produk',
                DB::raw('SUM(dp.jumlah) as total_terjual'),
                DB::raw('SUM(dp.subtotal) as total_penjualan')
            )
            ->groupBy('pr.id_produk', 'pr.nama_produk', 'pr.foto_produk')
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

        DB::table('produk')->insert($data);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct($id)
    {
        $produk = DB::table('produk')->where('id_produk', $id)->first();
        abort_if(! $produk, 404);

        return view('admin.products.form', compact('produk'));
    }

    public function updateProduct(Request $request, $id)
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

        if ($fotoBaru) {
            $this->deleteProductImage($produk->foto_produk);
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function deleteProduct($id)
    {
        $produk = DB::table('produk')->where('id_produk', $id)->first();
        abort_if(! $produk, 404);

        if (DB::table('detail_pesanan')->where('id_produk', $id)->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena tercatat pada riwayat pesanan.');
        }

        DB::table('produk')->where('id_produk', $id)->delete();
        $this->deleteProductImage($produk->foto_produk);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function inventory()
    {
        $produk = DB::table('produk')->orderBy('nama_produk')->paginate(12);

        return view('admin.inventory', compact('produk'));
    }

    public function updateInventory(Request $request, $id)
    {
        $data = $request->validate([
            'harga_produk' => 'required|numeric|min:0|max:9999999999.99',
            'stok_produk' => 'required|integer|min:0|max:2147483647',
        ]);

        $updated = DB::table('produk')->where('id_produk', $id)->update($data);
        abort_if(! $updated && ! DB::table('produk')->where('id_produk', $id)->exists(), 404);

        return back()->with('success', 'Harga dan stok berhasil diperbarui.');
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

    public function orderDetail($id)
    {
        $pesanan = $this->orderQuery()
            ->where('p.id_pesanan', $id)
            ->first();
        abort_if(! $pesanan, 404);

        $detailPesanan = DB::table('detail_pesanan as dp')
            ->leftJoin('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->where('dp.id_pesanan', $id)
            ->select('dp.jumlah', 'dp.harga', 'dp.subtotal', 'pr.nama_produk', 'pr.foto_produk')
            ->get();

        return view('admin.order-detail', compact('pesanan', 'detailPesanan'));
    }

    public function paymentReviewPage($id)
    {
        $pesanan = $this->orderQuery()->where('p.id_pesanan', $id)->first();
        abort_if(! $pesanan || ! $pesanan->status_pembayaran, 404);

        return view('admin.payment-review', compact('pesanan'));
    }

    public function updateOrder(Request $request, $id)
    {
        $data = $request->validate([
            'status_pesanan' => ['required', Rule::in(['menunggu', 'diproses', 'selesai', 'dibatalkan'])],
        ]);
        abort_if(! DB::table('pesanan')->where('id_pesanan', $id)->exists(), 404);
        DB::table('pesanan')->where('id_pesanan', $id)->update($data);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    public function updateShippingCost(Request $request, $id)
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

    public function reviewPayment(Request $request, $id)
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

    public function paymentProof($id)
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

    public function assignCourier(Request $request, $id)
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

    public function updateShipment(Request $request, $id)
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
        $filters = $request->validate([
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);
        $from = $filters['dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['sampai'] ?? now()->toDateString();
        $query = $this->reportQuery($from, $to);

        $ringkasan = [
            'jumlah_pesanan' => (clone $query)->count('p.id_pesanan'),
            'total_penjualan' => (clone $query)->where('pb.status_pembayaran', 'diverifikasi')->sum('pb.jumlah_bayar'),
            'belum_dibayar' => (clone $query)->where(function ($builder) {
                $builder->whereNull('pb.status_pembayaran')
                    ->orWhere('pb.status_pembayaran', '!=', 'diverifikasi');
            })->count('p.id_pesanan'),
        ];

        $laporan = (clone $query)
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
            ->orderByDesc('p.tanggal_pemesanan')
            ->paginate(15)
            ->withQueryString();

        $grafikBulanan = collect(range(4, 0))->map(function (int $monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $total = DB::table('pesanan as p')
                ->join('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
                ->where('pb.status_pembayaran', 'diverifikasi')
                ->whereBetween('p.tanggal_pemesanan', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('pb.jumlah_bayar');

            return (object) [
                'label' => $date->translatedFormat('M'),
                'total' => (float) $total,
            ];
        });

        return view('admin.reports', compact('ringkasan', 'laporan', 'from', 'to', 'grafikBulanan'));
    }

    public function exportReports(Request $request)
    {
        $filters = $request->validate([
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);
        $from = $filters['dari'] ?? now()->startOfMonth()->toDateString();
        $to = $filters['sampai'] ?? now()->toDateString();
        $rows = $this->reportQuery($from, $to)
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

        $directory = storage_path('framework/cache');
        File::ensureDirectoryExists($directory);
        $filePath = tempnam($directory, 'sales-report-');

        if ($filePath === false) {
            throw new \RuntimeException('Tidak dapat membuat file sementara untuk laporan Excel.');
        }

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

    public function approveCourier($id)
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

    private function orderQuery()
    {
        return DB::table('pesanan as p')
            ->join('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->leftJoin('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->select(
                'p.id_pesanan',
                'p.nama_penerima',
                'p.alamat_pengiriman',
                'p.tanggal_pemesanan',
                'p.total_harga',
                'p.ongkir',
                'p.wilayah_ongkir',
                DB::raw('CASE WHEN p.ongkir IS NULL THEN p.total_harga ELSE p.total_harga - p.ongkir END as subtotal_produk'),
                'p.status_pesanan',
                'pl.nama_pelanggan',
                'pb.bukti_pembayaran',
                'pb.metode_pembayaran',
                'pb.status_pembayaran',
                'pb.nomor_struk'
            );
    }

    private function reportQuery(string $from, string $to)
    {
        return DB::table('pesanan as p')
            ->leftJoin('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->whereDate('p.tanggal_pemesanan', '>=', $from)
            ->whereDate('p.tanggal_pemesanan', '<=', $to);
    }

    
private function writeSalesWorkbook(string $filePath, iterable $rows, string $from, string $to): void
{
    $headers = [
        'ID Pesanan',
        'Tanggal',
        'Pelanggan',
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

    $password = 'SFS2026';
    $passwordHash = 0;

    for ($i = strlen($password) - 1; $i >= 0; $i--) {
        $passwordHash = (($passwordHash >> 14) & 0x01)
            | (($passwordHash << 1) & 0x7fff);
        $passwordHash ^= ord($password[$i]);
    }

    $passwordHash = (($passwordHash >> 14) & 0x01)
        | (($passwordHash << 1) & 0x7fff);
    $passwordHash ^= strlen($password);
    $passwordHash ^= 0xCE4B;

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
        .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        .'<sheetViews><sheetView workbookViewId="0">'
        .'<pane ySplit="7" topLeftCell="A8" activePane="bottomLeft" state="frozen"/>'
        .'</sheetView></sheetViews>'
        .'<cols>'
        .'<col min="1" max="1" width="14" customWidth="1"/>'
        .'<col min="2" max="2" width="20" customWidth="1"/>'
        .'<col min="3" max="3" width="28" customWidth="1"/>'
        .'<col min="4" max="4" width="22" customWidth="1"/>'
        .'<col min="5" max="7" width="22" customWidth="1"/>'
        .'</cols>'
        .'<sheetData>'.implode('', $worksheetRows).'</sheetData>'
        .'<sheetProtection sheet="1" objects="1" scenarios="1"'
        .' password="'.strtoupper(str_pad(dechex($passwordHash), 4, '0', STR_PAD_LEFT)).'"'
        .' formatCells="1" formatColumns="1" formatRows="1"'
        .' insertColumns="1" insertRows="1" insertHyperlinks="1"'
        .' deleteColumns="1" deleteRows="1" sort="0" autoFilter="0"/>'
        .'<autoFilter ref="A7:G'.$lastRow.'"/>'
        .'<mergeCells count="5">'
        .'<mergeCell ref="B1:G1"/>'
        .'<mergeCell ref="B2:G2"/>'
        .'<mergeCell ref="B3:G3"/>'
        .'<mergeCell ref="A5:G5"/>'
        .'<mergeCell ref="A6:G6"/>'
        .'</mergeCells>'
        .($hasLogo ? '<drawing r:id="rId1"/>' : '')
        .'</worksheet>';

    $files = [
        '[Content_Types].xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .($hasLogo
                ? '<Default Extension="jpg" ContentType="image/jpeg"/>'
                    .'<Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>'
                : '')
            .'</Types>',

        '_rels/.rels' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>',

        'xl/workbook.xml' =>
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
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
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode=\'&quot;Rp&quot; #,##0.00\'/></numFmts>'
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
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1"/>'
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
}

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
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