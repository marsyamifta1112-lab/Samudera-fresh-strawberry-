<?php

namespace App\Http\Controllers;

use App\Support\OrderMetadata;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index()
    {
        $pengirimanTerakhir = DB::table('pengiriman')
            ->select('id_pesanan', DB::raw('MAX(id_pengiriman) as id_pengiriman'))
            ->groupBy('id_pesanan');

        $pesanan = DB::table('pesanan as p')
            ->leftJoin('pembayaran as pb', 'pb.id_pesanan', '=', 'p.id_pesanan')
            ->leftJoinSub($pengirimanTerakhir, 'pengiriman_terakhir', function ($join) {
                $join->on('pengiriman_terakhir.id_pesanan', '=', 'p.id_pesanan');
            })
            ->leftJoin('pengiriman as pg', 'pg.id_pengiriman', '=', 'pengiriman_terakhir.id_pengiriman')
            ->where('p.id_pelanggan', session('id_pelanggan'))
            ->select(
                'p.id_pesanan',
                'p.tanggal_pemesanan',
                'p.total_harga',
                'p.ongkir',
                'p.wilayah_ongkir',
                'p.status_pesanan',
                'pb.status_pembayaran',
                'pg.status_pengiriman'
            )
            ->orderByDesc('p.tanggal_pemesanan')
            ->orderByDesc('p.id_pesanan')
            ->get()
            ->map(function (object $order): object {
                $order->nomor_pesanan = $this->formatOrderNumber($order->id_pesanan);
                $order->status_label = $this->getStatusLabel($order);

                return $order;
            });

        return view('pesanan', compact('pesanan'));
    }

    public function show($id)
    {
        $pesanan = $this->findCustomerOrder($id);

        if (! $pesanan) {
            abort(404);
        }

        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $pesanan->id_pesanan)
            ->first();

        $pengiriman = DB::table('pengiriman')
            ->where('id_pesanan', $pesanan->id_pesanan)
            ->orderByDesc('id_pengiriman')
            ->first();

        $detailPesanan = DB::table('detail_pesanan as dp')
            ->leftJoin('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->where('dp.id_pesanan', $pesanan->id_pesanan)
            ->select(
                'dp.id_detail',
                'dp.id_produk',
                'dp.jumlah',
                'dp.harga',
                'dp.subtotal',
                'pr.nama_produk',
                'pr.foto_produk'
            )
            ->get();

        $metadata = OrderMetadata::decode($pesanan->catatan);
        $pesanan->subtotal_produk = (float) $detailPesanan->sum('subtotal');
        $pesanan->catatan = $metadata['customer_note'];
        $returnsByDetail = collect($metadata['returns'])
            ->map(static fn (array $return): object => (object) $return)
            ->sortByDesc('created_at')
            ->groupBy('id_detail');

        $nomorPesanan = $this->formatOrderNumber($pesanan->id_pesanan);
        $statusPesanan = $this->getStatusLabel((object) [
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pembayaran' => $pembayaran->status_pembayaran ?? null,
            'status_pengiriman' => $pengiriman->status_pengiriman ?? null,
        ]);
        $langkahAktif = $this->getActiveStep(
            $pesanan->status_pesanan,
            $pembayaran->status_pembayaran ?? null,
            $pengiriman->status_pengiriman ?? null
        );
        $dibatalkan = $pesanan->status_pesanan === 'dibatalkan';
        $bolehReturn = ! $dibatalkan
            && ($pesanan->status_pesanan === 'selesai' || ($pengiriman->status_pengiriman ?? null) === 'diterima')
            && ($pembayaran->status_pembayaran ?? null) === 'diverifikasi';

        return view('pesanan-detail', compact(
            'pesanan',
            'pembayaran',
            'pengiriman',
            'detailPesanan',
            'nomorPesanan',
            'statusPesanan',
            'langkahAktif',
            'dibatalkan',
            'returnsByDetail',
            'bolehReturn'
        ));
    }

    private function findCustomerOrder($id): ?object
    {
        return DB::table('pesanan')
            ->where('id_pesanan', $id)
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();
    }

    private function formatOrderNumber(int $id): string
    {
        return 'SP'.str_pad((string) $id, 6, '0', STR_PAD_LEFT);
    }

    private function getStatusLabel(object $order): string
    {
        if ($order->status_pesanan === 'dibatalkan') {
            return 'Dibatalkan';
        }

        if (($order->status_pengiriman ?? null) === 'diterima' || $order->status_pesanan === 'selesai') {
            return 'Selesai';
        }

        if (in_array($order->status_pengiriman ?? null, ['dikirim', 'diterima'], true)) {
            return 'Dalam Pengiriman';
        }

        if (
            $order->status_pesanan === 'diproses'
            || in_array($order->status_pengiriman ?? null, ['diproses', 'dikirim', 'diterima'], true)
        ) {
            return 'Diproses';
        }

        if (($order->status_pembayaran ?? null) === 'diverifikasi') {
            return 'Pembayaran Dikonfirmasi';
        }

        if (($order->status_pembayaran ?? null) === 'ditolak') {
            return 'Pembayaran Ditolak';
        }

        return 'Menunggu Pembayaran';
    }

    private function getActiveStep(?string $statusPesanan, ?string $statusPembayaran, ?string $statusPengiriman): int
    {
        $step = 0;

        if ($statusPembayaran === 'diverifikasi') {
            $step = 1;
        }

        if (
            $statusPesanan === 'diproses'
            || in_array($statusPengiriman, ['diproses', 'dikirim', 'diterima'], true)
        ) {
            $step = 2;
        }

        if (in_array($statusPengiriman, ['dikirim', 'diterima'], true)) {
            $step = 3;
        }

        if ($statusPesanan === 'selesai' || $statusPengiriman === 'diterima') {
            $step = 4;
        }

        return $step;
    }
}
