<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    public function show($id)
    {
        $pesanan = $this->findCustomerOrder($id);

        if (! $pesanan) {
            abort(404);
        }

        $pesanan->subtotal_produk = (float) $pesanan->total_harga - (float) ($pesanan->ongkir ?? 0);
        $pembayaran = $this->findOrderPayment($pesanan->id_pesanan);

        return view('pembayaran', compact('pesanan', 'pembayaran'));
    }

    public function receipt($id)
    {
        $isAdmin = session('role') === 'Admin';
        $pesanan = $isAdmin
            ? DB::table('pesanan')->where('id_pesanan', $id)->first()
            : $this->findCustomerOrder($id);
        abort_if(! $pesanan, 404);

        $pembayaran = $this->findOrderPayment($pesanan->id_pesanan);
        abort_if(
            $pembayaran->status_pembayaran !== 'diverifikasi'
                || ! $pembayaran->nomor_struk
                || ! $pembayaran->tanggal_verifikasi,
            404
        );

        $pelanggan = DB::table('pelanggan')
            ->where('id_pelanggan', $pesanan->id_pelanggan)
            ->first();
        $alamatPengiriman = filled($pesanan->alamat_pengiriman ?? null)
            ? $pesanan->alamat_pengiriman
            : ($pelanggan->alamat ?? null);

        $detailPesanan = DB::table('detail_pesanan as dp')
            ->leftJoin('produk as pr', 'pr.id_produk', '=', 'dp.id_produk')
            ->where('dp.id_pesanan', $pesanan->id_pesanan)
            ->select('dp.jumlah', 'dp.harga', 'dp.subtotal', 'pr.nama_produk')
            ->get();
        $pesanan->subtotal_produk = (float) $detailPesanan->sum('subtotal');

        $backUrl = $isAdmin
            ? route('admin.payments.show', $pesanan->id_pesanan)
            : route('pesanan.show', $pesanan->id_pesanan);

        return view('pembayaran-struk', compact(
            'pesanan',
            'pembayaran',
            'pelanggan',
            'alamatPengiriman',
            'detailPesanan',
            'backUrl'
        ));
    }

    public function uploadForm($id)
    {
        $pesanan = $this->findCustomerOrder($id);

        if (! $pesanan) {
            abort(404);
        }

        if ($pesanan->ongkir === null) {
            return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
                ->with('error', 'Ongkir masih menunggu penetapan Admin. Pembayaran belum dapat diproses.');
        }

        $pembayaran = $this->findOrderPayment($pesanan->id_pesanan);

        return view('pembayaran-upload', compact('pesanan', 'pembayaran'));
    }

    public function pilihMetode(Request $request, $id)
    {
        $data = $request->validate([
            'metode_pembayaran' => 'required|in:BCA,BRI,BNI,Mandiri',
        ]);

        $pesanan = $this->findCustomerOrder($id);

        if (! $pesanan) {
            abort(404);
        }

        if ($pesanan->ongkir === null) {
            return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
                ->with('error', 'Ongkir masih menunggu penetapan Admin. Pembayaran belum dapat diproses.');
        }

        $pembayaran = $this->findOrderPayment($pesanan->id_pesanan);

        if ($pembayaran->bukti_pembayaran || $pembayaran->status_pembayaran !== 'menunggu') {
            return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
                ->with('error', 'Bank pengirim tidak dapat diubah setelah bukti pembayaran diunggah.');
        }

        DB::table('pembayaran')
            ->where('id_pembayaran', $pembayaran->id_pembayaran)
            ->update(['metode_pembayaran' => $data['metode_pembayaran']]);

        return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
            ->with('success', 'Bank pengirim berhasil disimpan.');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $pesanan = $this->findCustomerOrder($id);

        if (! $pesanan) {
            abort(404);
        }

        if ($pesanan->ongkir === null) {
            return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
                ->with('error', 'Ongkir masih menunggu penetapan Admin. Pembayaran belum dapat diproses.');
        }

        $pembayaran = $this->findOrderPayment($pesanan->id_pesanan);

        if ($pembayaran->status_pembayaran === 'diverifikasi') {
            return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
                ->with('error', 'Bukti tidak dapat diunggah ulang setelah pembayaran diverifikasi.');
        }

        $path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'local');

        if (! $path) {
            throw ValidationException::withMessages([
                'bukti_pembayaran' => 'Bukti pembayaran gagal disimpan. Silakan coba lagi.',
            ]);
        }

        try {
            DB::table('pembayaran')
                ->where('id_pembayaran', $pembayaran->id_pembayaran)
                ->update([
                    'tanggal_pembayaran' => now(),
                    'bukti_pembayaran' => $path,
                    'status_pembayaran' => 'menunggu',
                ]);
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($pembayaran->bukti_pembayaran) {
            Storage::disk('local')->delete($pembayaran->bukti_pembayaran);
        }

        return redirect()->route('pembayaran.show', $pesanan->id_pesanan)
            ->with('success', 'Bukti transfer berhasil diunggah dan menunggu verifikasi.');
    }

    private function findCustomerOrder($id): ?object
    {
        return DB::table('pesanan')
            ->where('id_pesanan', $id)
            ->where('id_pelanggan', session('id_pelanggan'))
            ->first();
    }

    private function findOrderPayment($idPesanan): object
    {
        $pembayaran = DB::table('pembayaran')
            ->where('id_pesanan', $idPesanan)
            ->first();

        if (! $pembayaran) {
            abort(404);
        }

        return $pembayaran;
    }
}
