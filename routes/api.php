<?php

use App\Support\OrderMetadata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/produk', function () {
    $produk = DB::table('produk')
        ->select(
            'id_produk',
            'nama_produk',
            'deskripsi',
            'harga_produk',
            'stok_produk',
            'foto_produk'
        )
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Data produk berhasil diambil.',
        'data' => $produk,
    ]);
});

Route::get('/produk/{id}', function ($id) {
    $produk = DB::table('produk')
        ->where('id_produk', $id)
        ->first();

    if (! $produk) {
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan.',
            'data' => null,
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Detail produk berhasil diambil.',
        'data' => $produk,
    ]);
});

// API daftar pesanan
Route::get('/pesanan', function (Request $request) {
    $query = DB::table('pesanan')
        ->select(
            'id_pesanan',
            'id_pelanggan',
            'tanggal_pemesanan',
            'total_harga',
            'status_pesanan',
            'nama_penerima',
            'alamat_pengiriman',
            'no_hp',
            'catatan'
        );

    if ($request->filled('id_pelanggan')) {
        $query->where('id_pelanggan', $request->id_pelanggan);
    }

    return response()->json([
        'success' => true,
        'message' => 'Data pesanan berhasil diambil.',
        'data' => $query->orderByDesc('id_pesanan')->get()->map(function (object $order): object {
            $order->catatan = OrderMetadata::customerNote($order->catatan);

            return $order;
        }),
    ]);
});

// API detail pesanan
Route::get('/pesanan/{id}', function ($id) {
    $pesanan = DB::table('pesanan')
        ->where('id_pesanan', $id)
        ->first();

    if (! $pesanan) {
        return response()->json([
            'success' => false,
            'message' => 'Pesanan tidak ditemukan.',
            'data' => null,
        ], 404);
    }

    $pesanan->catatan = OrderMetadata::customerNote($pesanan->catatan);

    $detail = DB::table('detail_pesanan')
        ->join(
            'produk',
            'detail_pesanan.id_produk',
            '=',
            'produk.id_produk'
        )
        ->where('detail_pesanan.id_pesanan', $id)
        ->select(
            'detail_pesanan.id_detail',
            'detail_pesanan.id_produk',
            'produk.nama_produk',
            'detail_pesanan.jumlah',
            'detail_pesanan.harga',
            'detail_pesanan.subtotal'
        )
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Detail pesanan berhasil diambil.',
        'data' => [
            'pesanan' => $pesanan,
            'detail' => $detail,
        ],
    ]);
});

// API daftar pembayaran
Route::get('/pembayaran', function () {
    $pembayaran = DB::table('pembayaran')
        ->select(
            'id_pembayaran',
            'id_pesanan',
            'tanggal_pembayaran',
            'metode_pembayaran',
            'jumlah_bayar',
            'bukti_pembayaran',
            'status_pembayaran'
        )
        ->orderByDesc('id_pembayaran')
        ->get();

    return response()->json([
        'success' => true,
        'message' => 'Data pembayaran berhasil diambil.',
        'data' => $pembayaran,
    ]);
});

// API detail pembayaran
Route::get('/pembayaran/{id}', function ($id) {
    $pembayaran = DB::table('pembayaran')
        ->where('id_pembayaran', $id)
        ->first();

    if (! $pembayaran) {
        return response()->json([
            'success' => false,
            'message' => 'Pembayaran tidak ditemukan.',
            'data' => null,
        ], 404);
    }

    return response()->json([
        'success' => true,
        'message' => 'Detail pembayaran berhasil diambil.',
        'data' => $pembayaran,
    ]);
});
