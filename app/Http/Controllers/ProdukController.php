<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;

class ProdukController extends Controller
{
    // Menampilkan katalog produk
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => 'nullable|string|max:100',
            'kategori' => 'nullable|in:segar,keripik,selai',
            'harga_min' => 'nullable|numeric|min:0',
            'harga_max' => 'nullable|numeric|min:0|gte:harga_min',
            'berat_min' => 'nullable|integer|min:0',
            'berat_max' => 'nullable|integer|min:0|gte:berat_min',
        ]);

        $query = Produk::query();

        if (!empty($filters['q'])) {
            $kataKunci = trim($filters['q']);
            $query->where(function ($query) use ($kataKunci) {
                $query->where('nama_produk', 'like', '%' . $kataKunci . '%')
                    ->orWhere('deskripsi', 'like', '%' . $kataKunci . '%');
            });
        }

        if (!empty($filters['kategori'])) {
            $kataKategori = match ($filters['kategori']) {
                'segar' => ['segar', 'fresh'],
                'keripik' => ['keripik'],
                'selai' => ['selai'],
            };

            $query->where(function ($query) use ($kataKategori) {
                foreach ($kataKategori as $kata) {
                    $query->orWhere('nama_produk', 'like', '%' . $kata . '%')
                        ->orWhere('deskripsi', 'like', '%' . $kata . '%');
                }
            });
        }

        if (isset($filters['harga_min'])) {
            $query->where('harga_produk', '>=', $filters['harga_min']);
        }

        if (isset($filters['harga_max'])) {
            $query->where('harga_produk', '<=', $filters['harga_max']);
        }

        if (isset($filters['berat_min'])) {
            $query->where('berat_gram', '>=', $filters['berat_min']);
        }

        if (isset($filters['berat_max'])) {
            $query->where('berat_gram', '<=', $filters['berat_max']);
        }

        $produk = $query->orderBy('id_produk')->get();

        return view('produk', compact('produk', 'filters'));
    }

    public function show($id)
    {
        $produk = Produk::whereKey($id)->firstOrFail();
        $keranjang = session('keranjang', []);
        $jumlahDiKeranjang = $keranjang[$produk->id_produk]['jumlah'] ?? 0;
        $stokTersedia = max(0, (int) $produk->stok_produk - (int) $jumlahDiKeranjang);

        return view('produk-detail', compact('produk', 'stokTersedia'));
    }

    // Menambahkan produk ke keranjang
    public function tambahKeranjang(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'sometimes|required|integer|min:1|max:1000',
        ]);

        $jumlahDitambahkan = (int) $request->input('jumlah', 1);

        $produk = DB::table('produk')
            ->where('id_produk', $id)
            ->first();

        if (!$produk) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        $keranjang = session('keranjang', []);
        $jumlahSekarang = $keranjang[$id]['jumlah'] ?? 0;

        if ($jumlahDitambahkan + $jumlahSekarang > $produk->stok_produk) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        if (isset($keranjang[$id])) {
            $keranjang[$id]['jumlah'] += $jumlahDitambahkan;
            $keranjang[$id]['harga_produk'] = $produk->harga_produk;
            $keranjang[$id]['nama_produk'] = $produk->nama_produk;
            $keranjang[$id]['foto_produk'] = $produk->foto_produk;
        } else {
            $keranjang[$id] = [
                'id_produk' => $produk->id_produk,
                'nama_produk' => $produk->nama_produk,
                'harga_produk' => $produk->harga_produk,
                'foto_produk' => $produk->foto_produk,
                'jumlah' => $jumlahDitambahkan,
            ];
        }

        session(['keranjang' => $keranjang]);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    // Menampilkan halaman keranjang
    public function keranjang()
    {
        if (!session('id_pelanggan') || session('role') !== 'Pelanggan') {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai pelanggan terlebih dahulu.');
        }

        $keranjang = session('keranjang', []);
        $produk = DB::table('produk')
            ->whereIn('id_produk', array_keys($keranjang))
            ->get()
            ->keyBy('id_produk');
        $total = 0;

        foreach ($keranjang as $id => &$item) {
            $dataProduk = $produk->get($id);

            if (!$dataProduk) {
                unset($keranjang[$id]);
                continue;
            }

            $item['id_produk'] = $dataProduk->id_produk;
            $item['nama_produk'] = $dataProduk->nama_produk;
            $item['harga_produk'] = $dataProduk->harga_produk;
            $item['foto_produk'] = $dataProduk->foto_produk;
            $total += $dataProduk->harga_produk * $item['jumlah'];
        }
        unset($item);

        session(['keranjang' => $keranjang]);

        return view('keranjang', compact('keranjang', 'total'));
    }

    // Mengubah jumlah produk di keranjang
    public function updateKeranjang(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjang = session('keranjang', []);

        if (!isset($keranjang[$id])) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Produk tidak ada di keranjang.');
        }

        $produk = DB::table('produk')
            ->where('id_produk', $id)
            ->first();

        if (!$produk) {
            unset($keranjang[$id]);
            session(['keranjang' => $keranjang]);

            return redirect()->route('keranjang.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        if ($request->jumlah > $produk->stok_produk) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $keranjang[$id]['jumlah'] = (int) $request->jumlah;
        session(['keranjang' => $keranjang]);

        return redirect()->route('keranjang.index')
            ->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    // Menghapus produk dari keranjang
    public function hapusKeranjang($id)
    {
        $keranjang = session('keranjang', []);
        unset($keranjang[$id]);
        session(['keranjang' => $keranjang]);

        return redirect()->route('keranjang.index')
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    // Route pesan lama tetap dipertahankan
    public function pesan(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        if (!session('id_pelanggan') || session('role') !== 'Pelanggan') {
            return redirect()->route('login')
                ->with('error', 'Silakan login sebagai pelanggan terlebih dahulu.');
        }

        $produk = DB::table('produk')
            ->where('id_produk', $id)
            ->first();

        if (!$produk) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        if ($request->jumlah > $produk->stok_produk) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $subtotal = $produk->harga_produk * (int) $request->jumlah;

        DB::transaction(function () use ($produk, $request, $subtotal) {
            $idPesanan = DB::table('pesanan')->insertGetId([
                'id_pelanggan' => session('id_pelanggan'),
                'tanggal_pemesanan' => now(),
                'total_harga' => $subtotal,
                'status_pesanan' => 'menunggu',
            ]);

            DB::table('detail_pesanan')->insert([
                'id_pesanan' => $idPesanan,
                'id_produk' => $produk->id_produk,
                'jumlah' => (int) $request->jumlah,
                'harga' => $produk->harga_produk,
                'subtotal' => $subtotal,
            ]);
        });

        return redirect('/pesanan')
            ->with('success', 'Pesanan berhasil dibuat.');
    }
}