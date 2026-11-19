<?php

namespace App\Http\Controllers;

use App\Support\OperationalSchedule;
use App\Support\ProductCatalog;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

        $query = Produk::query()
            ->whereRaw('LOWER(nama_produk) NOT LIKE ?', ['%paket hemat%']);

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
                    $query->orWhere('nama_produk', 'like', '%' . $kata . '%');
                }
            });
        }

        if (isset($filters['harga_min']) || isset($filters['harga_max'])) {
            $query->where(function ($query) use ($filters) {
                $query->where(function ($query) use ($filters) {
                    $query->whereRaw(
                        "NOT ((LOWER(nama_produk) LIKE ? OR LOWER(nama_produk) LIKE ?) AND (LOWER(nama_produk) LIKE ? OR LOWER(nama_produk) LIKE ?))",
                        ['%strawberry%', '%stroberi%', '%segar%', '%fresh%']
                    );
                    if (isset($filters['harga_min'])) {
                        $query->where('harga_produk', '>=', $filters['harga_min']);
                    }
                    if (isset($filters['harga_max'])) {
                        $query->where('harga_produk', '<=', $filters['harga_max']);
                    }
                })->orWhereExists(function ($query) use ($filters) {
                    $query->selectRaw('1')
                        ->from('produk_grade')
                        ->whereColumn('produk_grade.id_produk', 'produk.id_produk');
                    if (isset($filters['harga_min'])) {
                        $query->where('produk_grade.harga_per_kg', '>=', $filters['harga_min']);
                    }
                    if (isset($filters['harga_max'])) {
                        $query->where('produk_grade.harga_per_kg', '<=', $filters['harga_max']);
                    }
                });
            });
        }

        if (isset($filters['berat_min']) || isset($filters['berat_max'])) {
            $query->whereRaw(
                "NOT ((LOWER(nama_produk) LIKE ? OR LOWER(nama_produk) LIKE ?) AND (LOWER(nama_produk) LIKE ? OR LOWER(nama_produk) LIKE ?))",
                ['%strawberry%', '%stroberi%', '%segar%', '%fresh%']
            );
            if (isset($filters['berat_min'])) {
                $query->where('berat_gram', '>=', $filters['berat_min']);
            }
            if (isset($filters['berat_max'])) {
                $query->where('berat_gram', '<=', $filters['berat_max']);
            }
        }

        $produk = $query->orderBy('id_produk')->get();
        $operationalSchedule = OperationalSchedule::current();
        $freshProductIds = $produk
            ->filter(fn (Produk $item): bool => ProductCatalog::isFreshStrawberry($item->nama_produk))
            ->pluck('id_produk');
        $gradesByProduct = $freshProductIds->isEmpty()
            ? collect()
            : DB::table('produk_grade')->whereIn('id_produk', $freshProductIds)->get()->groupBy('id_produk');

        $produk->each(function (Produk $item) use ($gradesByProduct): void {
            $item->is_strawberry_fresh = ProductCatalog::isFreshStrawberry($item->nama_produk);
            $item->grades = $gradesByProduct->get($item->id_produk, collect());
        });

        return view('produk', compact('produk', 'filters', 'operationalSchedule'));
    }

    public function show(int $id)
    {
        $produk = Produk::whereKey($id)->firstOrFail();
        abort_if(ProductCatalog::isHematPackage($produk->nama_produk), 404);

        $isStrawberryFresh = ProductCatalog::isFreshStrawberry($produk->nama_produk);
        $grades = $isStrawberryFresh
            ? DB::table('produk_grade')->where('id_produk', $produk->id_produk)->orderBy('grade')->get()
            : collect();
        $keranjang = session('keranjang', []);
        $jumlahDiKeranjang = $keranjang[$produk->id_produk]['jumlah'] ?? 0;
        $stokTersedia = max(0, (int) $produk->stok_produk - (int) $jumlahDiKeranjang);
        $hasStock = $isStrawberryFresh
            ? $grades->contains(fn (object $grade): bool => (float) $grade->stok_kg > 0)
            : (int) $produk->stok_produk > 0;
        $dapatDipesan = (bool) ($produk->is_available ?? true) && $hasStock;

        return view('produk-detail', compact('produk', 'stokTersedia', 'dapatDipesan', 'isStrawberryFresh', 'grades'));
    }

    // Menambahkan produk ke keranjang
    public function tambahKeranjang(Request $request, int $id)
    {
        $request->validate([
            'jumlah' => 'sometimes|required|integer|min:1|max:1000',
            'grade' => 'nullable|in:A,B,C',
        ]);

        $jumlahDitambahkan = (int) $request->input('jumlah', 1);

        $produk = DB::table('produk')
            ->where('id_produk', $id)
            ->first();

        if (!$produk) {
            return redirect()->route('produk.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        if (! (bool) ($produk->is_available ?? true)) {
            return back()->with('error', 'Maaf, produk sedang habis dan tidak dapat dipesan.');
        }

        if (ProductCatalog::isHematPackage($produk->nama_produk)) {
            return redirect()->route('produk.index')
                ->with('error', 'Paket Hemat sudah tidak tersedia.');
        }

        if (ProductCatalog::isFreshStrawberry($produk->nama_produk)) {
            $grade = $request->input('grade');
            if (! $grade) {
                throw ValidationException::withMessages([
                    'grade' => 'Pilih Grade A, B, atau C untuk stroberi segar.',
                ]);
            }

            $varian = DB::table('produk_grade')
                ->where('id_produk', $produk->id_produk)
                ->where('grade', $grade)
                ->first();
            abort_if(! $varian, 404);

            $keranjang = session('keranjang', []);
            $cartKey = 'grade-'.$produk->id_produk.'-'.$grade;
            $jumlahSekarang = (int) ($keranjang[$cartKey]['jumlah'] ?? 0);

            if ($jumlahDitambahkan + $jumlahSekarang > (float) $varian->stok_kg) {
                return back()->with('error', "Stok Grade {$grade} tidak mencukupi.");
            }

            $keranjang[$cartKey] = [
                'id_produk' => $produk->id_produk,
                'nama_produk' => $produk->nama_produk,
                'harga_produk' => $varian->harga_per_kg,
                'foto_produk' => ProductCatalog::gradeImage($grade),
                'jumlah' => $jumlahSekarang + $jumlahDitambahkan,
                'grade' => $grade,
                'satuan' => 'kg',
            ];
            session(['keranjang' => $keranjang]);

            return back()->with('success', "Stroberi Grade {$grade} berhasil ditambahkan ke keranjang.");
        }

        if ((int) $produk->stok_produk < 1) {
            return back()->with('error', 'Maaf, produk sedang habis dan tidak dapat dipesan.');
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
        $idProduk = collect($keranjang)->pluck('id_produk')->filter()->unique()->values();
        $produk = DB::table('produk')
            ->whereIn('id_produk', $idProduk)
            ->get()
            ->keyBy('id_produk');
        $grades = $idProduk->isEmpty()
            ? collect()
            : DB::table('produk_grade')
                ->whereIn('id_produk', $idProduk)
                ->get()
                ->keyBy(fn (object $grade): string => $grade->id_produk.'-'.$grade->grade);
        $total = 0;

        foreach ($keranjang as $id => &$item) {
            $dataProduk = $produk->get($item['id_produk'] ?? $id);

            if (! $dataProduk || ProductCatalog::isHematPackage($dataProduk->nama_produk)) {
                unset($keranjang[$id]);
                continue;
            }

            $item['id_produk'] = $dataProduk->id_produk;
            $item['nama_produk'] = $dataProduk->nama_produk;
            if (! empty($item['grade'])) {
                $grade = $grades->get($dataProduk->id_produk.'-'.$item['grade']);
                if (! $grade) {
                    unset($keranjang[$id]);
                    continue;
                }

                $item['harga_produk'] = $grade->harga_per_kg;
                $item['foto_produk'] = ProductCatalog::gradeImage($item['grade']);
                $item['satuan'] = 'kg';
            } else {
                $item['harga_produk'] = $dataProduk->harga_produk;
                $item['foto_produk'] = $dataProduk->foto_produk;
                $item['satuan'] = $dataProduk->berat_gram ? 'paket' : 'pcs';
                $item['berat_gram'] = $dataProduk->berat_gram;
            }
            $item['is_available'] = (bool) ($dataProduk->is_available ?? true)
                && (! empty($item['grade']) || (int) $dataProduk->stok_produk > 0);
            $total += $item['harga_produk'] * $item['jumlah'];
        }
        unset($item);

        $adaProdukHabis = collect($keranjang)->contains(
            fn (array $item): bool => ! ($item['is_available'] ?? true)
        );

        session(['keranjang' => $keranjang]);

        return view('keranjang', compact('keranjang', 'total', 'adaProdukHabis'));
    }

    // Mengubah jumlah produk di keranjang
    public function updateKeranjang(Request $request, int $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $keranjang = session('keranjang', []);

        if (!isset($keranjang[$id])) {
            return redirect()->route('keranjang.index')
                ->with('error', 'Produk tidak ada di keranjang.');
        }

        $item = $keranjang[$id];
        $produk = DB::table('produk')
            ->where('id_produk', $item['id_produk'] ?? $id)
            ->first();

        if (!$produk) {
            unset($keranjang[$id]);
            session(['keranjang' => $keranjang]);

            return redirect()->route('keranjang.index')
                ->with('error', 'Produk tidak ditemukan.');
        }

        if (! (bool) ($produk->is_available ?? true)) {
            return back()->with('error', 'Maaf, produk sedang habis dan tidak dapat dipesan.');
        }

        if (ProductCatalog::isHematPackage($produk->nama_produk)) {
            return redirect()->route('produk.index')
                ->with('error', 'Paket Hemat sudah tidak tersedia.');
        }

        if (! empty($item['grade'])) {
            $varian = DB::table('produk_grade')
                ->where('id_produk', $produk->id_produk)
                ->where('grade', $item['grade'])
                ->first();
            if (! $varian) {
                unset($keranjang[$id]);
                session(['keranjang' => $keranjang]);

                return redirect()->route('keranjang.index')->with('error', 'Grade produk tidak tersedia lagi.');
            }
            if ($request->jumlah > (float) $varian->stok_kg) {
                return back()->with('error', "Jumlah melebihi stok Grade {$item['grade']} yang tersedia.");
            }
        } elseif (
            ProductCatalog::isFreshStrawberry($produk->nama_produk)
            || (int) $produk->stok_produk < 1
            || $request->jumlah > $produk->stok_produk
        ) {
            return back()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $keranjang[$id]['jumlah'] = (int) $request->jumlah;
        session(['keranjang' => $keranjang]);

        return redirect()->route('keranjang.index')
            ->with('success', 'Jumlah produk berhasil diperbarui.');
    }

    // Menghapus produk dari keranjang
    public function hapusKeranjang(int $id)
    {
        $keranjang = session('keranjang', []);
        unset($keranjang[$id]);
        session(['keranjang' => $keranjang]);

        return redirect()->route('keranjang.index')
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    // Route pesan lama tetap dipertahankan
    public function pesan(Request $request, int $id)
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

        if (! (bool) ($produk->is_available ?? true)) {
            return back()->with('error', 'Maaf, produk sedang habis dan tidak dapat dipesan.');
        }

        if (
            ProductCatalog::isHematPackage($produk->nama_produk)
            || ProductCatalog::isFreshStrawberry($produk->nama_produk)
        ) {
            return redirect()->route('produk.index')
                ->with('error', 'Gunakan pilihan produk yang tersedia di katalog untuk melakukan pemesanan.');
        }

        if ((int) $produk->stok_produk < 1 || $request->jumlah > $produk->stok_produk) {
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