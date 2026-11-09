<?php

namespace App\Http\Controllers;

use App\Support\OrderMetadata;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReturnController extends Controller
{
    public function store(Request $request, int $orderId)
    {
        $data = $request->validate([
            'id_detail' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'alasan' => 'required|string|min:10|max:2000',
            'foto_bukti' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'bank_pengembalian' => 'required|in:BCA,BRI,BNI,Mandiri',
            'nama_pemilik_rekening' => 'required|string|max:100',
            'nomor_rekening' => ['required', 'string', 'max:34', 'regex:/^[0-9]+$/'],
        ]);

        $order = $this->customerOrder($orderId);
        abort_if(! $order, 404);

        $payment = DB::table('pembayaran')->where('id_pesanan', $orderId)->first();
        $shipment = DB::table('pengiriman')
            ->where('id_pesanan', $orderId)
            ->where('status_pengiriman', 'diterima')
            ->exists();

        if (
            $order->status_pesanan === 'dibatalkan'
            || ! $payment
            || $payment->status_pembayaran !== 'diverifikasi'
            || ($order->status_pesanan !== 'selesai' && ! $shipment)
        ) {
            return back()->with('error', 'Return hanya dapat diajukan untuk pesanan lunas yang sudah diterima.');
        }

        $proofPath = $request->file('foto_bukti')->store('return-bukti', 'local');
        if (! $proofPath) {
            throw ValidationException::withMessages([
                'foto_bukti' => 'Foto bukti return gagal disimpan. Silakan coba lagi.',
            ]);
        }

        try {
            DB::transaction(function () use ($data, $orderId, $proofPath) {
                $lockedOrder = DB::table('pesanan')
                    ->where('id_pesanan', $orderId)
                    ->where('id_pelanggan', session('id_pelanggan'))
                    ->lockForUpdate()
                    ->first();
                abort_if(! $lockedOrder, 404);

                $detail = DB::table('detail_pesanan')
                    ->where('id_detail', $data['id_detail'])
                    ->where('id_pesanan', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (! $detail) {
                    throw ValidationException::withMessages([
                        'id_detail' => 'Produk tersebut bukan bagian dari pesanan ini.',
                    ]);
                }

                $metadata = OrderMetadata::decode($lockedOrder->catatan);
                $jumlahAktif = collect($metadata['returns'])
                    ->filter(fn (array $return): bool => (int) $return['id_detail'] === (int) $detail->id_detail
                        && $return['status_return'] !== 'Ditolak')
                    ->sum('jumlah');
                $jumlahTersisa = (int) $detail->jumlah - (int) $jumlahAktif;

                if ($data['jumlah'] > $jumlahTersisa) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Jumlah return melebihi produk yang masih dapat diajukan.',
                    ]);
                }

                $nextId = collect($metadata['returns'])->max('id_return') + 1;
                $now = now()->toDateTimeString();
                $metadata['returns'][] = [
                    'id_return' => $nextId,
                    'id_detail' => $detail->id_detail,
                    'id_pesanan' => $orderId,
                    'id_pelanggan' => $lockedOrder->id_pelanggan,
                    'id_produk' => $detail->id_produk,
                    'jumlah' => $data['jumlah'],
                    'jumlah_pengembalian' => number_format((float) $detail->harga * $data['jumlah'], 2, '.', ''),
                    'alasan' => $data['alasan'],
                    'foto_bukti' => $proofPath,
                    'bank_pengembalian' => $data['bank_pengembalian'],
                    'nama_pemilik_rekening' => $data['nama_pemilik_rekening'],
                    'nomor_rekening' => $data['nomor_rekening'],
                    'status_return' => 'Diajukan',
                    'catatan_admin' => null,
                    'status_pengembalian_dana' => 'Menunggu',
                    'referensi_transfer' => null,
                    'stok_dikembalikan_at' => null,
                    'dana_ditransfer_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                DB::table('pesanan')->where('id_pesanan', $orderId)->update([
                    'catatan' => OrderMetadata::encode($metadata['customer_note'], $metadata['returns']),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($proofPath);
            throw $exception;
        }

        return redirect()
            ->route('pesanan.show', $orderId)
            ->with('success', 'Pengajuan return berhasil dikirim dan menunggu pemeriksaan Admin.');
    }

    public function adminIndex()
    {
        $items = DB::table('pesanan as p')
            ->join('pelanggan as pl', 'pl.id_pelanggan', '=', 'p.id_pelanggan')
            ->select('p.id_pesanan', 'p.catatan', 'pl.nama_pelanggan')
            ->whereNotNull('p.catatan')
            ->orderByDesc('p.id_pesanan')
            ->get()
            ->flatMap(function (object $order): array {
                return collect(OrderMetadata::decode($order->catatan)['returns'])
                    ->map(function (array $return) use ($order): object {
                        $return['nama_pelanggan'] = $order->nama_pelanggan;
                        $return['nama_produk'] = DB::table('produk')
                            ->where('id_produk', $return['id_produk'])
                            ->value('nama_produk');

                        return (object) $return;
                    })
                    ->all();
            })
            ->sort(function (object $left, object $right): int {
                $pendingOrder = ($left->status_return === 'Diajukan' ? 0 : 1)
                    <=> ($right->status_return === 'Diajukan' ? 0 : 1);

                return $pendingOrder ?: strcmp($right->created_at ?? '', $left->created_at ?? '');
            })
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $returns = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.returns', compact('returns'));
    }

    public function review(Request $request, int $orderId, int $returnId)
    {
        $data = $request->validate([
            'keputusan' => 'required|in:setujui,tolak',
            'catatan_admin' => 'nullable|string|max:2000',
        ]);

        $this->updateOrderReturn($orderId, $returnId, function (array $return) use ($data): array {
            abort_if($return['status_return'] !== 'Diajukan', 409, 'Pengajuan return ini sudah diproses.');
            $return['status_return'] = $data['keputusan'] === 'setujui' ? 'Disetujui' : 'Ditolak';
            $return['catatan_admin'] = $data['catatan_admin'] ?? null;

            return $this->touch($return);
        });

        return redirect()->route('admin.returns')->with('success', 'Keputusan return berhasil disimpan.');
    }

    public function updateStatus(Request $request, int $orderId, int $returnId)
    {
        $data = $request->validate([
            'status_return' => 'required|in:Barang Diterima,Layak Dijual,Tidak Layak',
            'catatan_admin' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($orderId, $returnId, $data) {
            $order = DB::table('pesanan')->where('id_pesanan', $orderId)->lockForUpdate()->first();
            abort_if(! $order, 404);
            $return = OrderMetadata::findReturn($order->catatan, $returnId);
            abort_if(! $return, 404);

            if ($data['status_return'] === 'Barang Diterima') {
                abort_if($return['status_return'] !== 'Disetujui', 409, 'Return harus disetujui sebelum barang diterima.');
            } else {
                abort_if($return['status_return'] !== 'Barang Diterima', 409, 'Barang harus dicatat diterima sebelum diperiksa.');
            }

            if ($data['status_return'] === 'Layak Dijual') {
                abort_if($return['stok_dikembalikan_at'] !== null, 409, 'Stok untuk return ini sudah dikembalikan.');
                $product = DB::table('produk')->where('id_produk', $return['id_produk'])->lockForUpdate()->first();
                abort_if(! $product || $product->stok_produk === null, 409, 'Stok produk tidak tersedia untuk diperbarui.');

                if ((int) $product->stok_produk + (int) $return['jumlah'] > 2147483647) {
                    throw ValidationException::withMessages([
                        'status_return' => 'Jumlah stok setelah return melebihi batas database.',
                    ]);
                }

                DB::table('produk')
                    ->where('id_produk', $return['id_produk'])
                    ->increment('stok_produk', (int) $return['jumlah']);
                $return['stok_dikembalikan_at'] = now()->toDateTimeString();
            }

            $return['status_return'] = $data['status_return'];
            $return['catatan_admin'] = $data['catatan_admin'] ?? $return['catatan_admin'];
            $return = $this->touch($return);
            $this->saveOrderReturn($order, $return);
        });

        return redirect()->route('admin.returns')->with('success', 'Status return berhasil diperbarui.');
    }

    public function updateRefund(Request $request, int $orderId, int $returnId)
    {
        $data = $request->validate([
            'aksi' => 'required|in:mulai_transfer,selesai_transfer',
            'referensi_transfer' => 'nullable|string|max:100',
        ]);

        $this->updateOrderReturn($orderId, $returnId, function (array $return) use ($data): array {
            abort_if(! in_array($return['status_return'], ['Layak Dijual', 'Tidak Layak'], true), 409, 'Return belum selesai diperiksa.');

            if ($data['aksi'] === 'mulai_transfer') {
                abort_if($return['status_pengembalian_dana'] !== 'Menunggu', 409, 'Pengembalian dana ini sudah diproses.');
                $return['status_pengembalian_dana'] = 'Diproses';

                return $this->touch($return);
            }

            abort_if(
                $return['status_pengembalian_dana'] !== 'Diproses' || $return['dana_ditransfer_at'] !== null,
                409,
                'Transfer harus diproses dan belum pernah diselesaikan.'
            );
            $return['status_pengembalian_dana'] = 'Selesai';
            $return['referensi_transfer'] = $data['referensi_transfer'] ?? null;
            $return['dana_ditransfer_at'] = now()->toDateTimeString();

            return $this->touch($return);
        });

        return redirect()->route('admin.returns')->with('success', 'Status pengembalian dana berhasil diperbarui.');
    }

    public function proof(int $orderId, int $returnId)
    {
        $order = $this->findOrder($orderId);
        abort_if(! $order, 404);

        if (session('role') === 'Pelanggan' && (int) $order->id_pelanggan !== (int) session('id_pelanggan')) {
            abort(404);
        }

        $return = OrderMetadata::findReturn($order->catatan, $returnId);
        abort_if(! $return, 404);

        $path = $return['foto_bukti'];
        abort_unless(str_starts_with($path, 'return-bukti/') && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    private function customerOrder(int $id): ?object
    {
        return DB::table('pesanan')
            ->where('id_pesanan', $id)
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();
    }

    private function findOrder(int $id): ?object
    {
        return DB::table('pesanan')->where('id_pesanan', $id)->first();
    }

    private function updateOrderReturn(int $orderId, int $returnId, callable $callback): void
    {
        DB::transaction(function () use ($orderId, $returnId, $callback) {
            $order = DB::table('pesanan')->where('id_pesanan', $orderId)->lockForUpdate()->first();
            abort_if(! $order, 404);

            $updatedNote = OrderMetadata::updateReturn($order->catatan, $returnId, $callback);
            abort_if($updatedNote === null, 404);

            DB::table('pesanan')->where('id_pesanan', $orderId)->update(['catatan' => $updatedNote]);
        });
    }

    private function saveOrderReturn(object $order, array $return): void
    {
        $updatedNote = OrderMetadata::updateReturn(
            $order->catatan,
            (int) $return['id_return'],
            static fn (): array => $return
        );
        abort_if($updatedNote === null, 404);

        DB::table('pesanan')->where('id_pesanan', $order->id_pesanan)->update(['catatan' => $updatedNote]);
    }

    private function touch(array $return): array
    {
        $return['updated_at'] = now()->toDateTimeString();

        return $return;
    }
}
