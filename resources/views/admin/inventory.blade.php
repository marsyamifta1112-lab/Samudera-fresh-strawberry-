@extends('admin.layout')
@section('title', 'Harga & Stok')
@section('content')
    <div class="page-heading"><div><h2>Harga &amp; Stok</h2><p>Perbarui harga dan persediaan produk langsung dari daftar ini.</p></div></div>
    <section class="panel inventory-table"><div class="table-wrap"><table><thead><tr><th>Produk</th><th>Harga</th><th>Stok</th><th>Edit</th></tr></thead><tbody>
    @forelse ($produk as $item)
        @if (\App\Support\ProductCatalog::isFreshStrawberry($item->nama_produk))
            <tr><td><strong>{{ $item->nama_produk }}</strong></td><td>Diatur per grade</td><td>Stok per grade</td><td><span class="muted">Kelola di tabel grade</span></td></tr>
        @else
            <tr><td><strong>{{ $item->nama_produk }}</strong></td><td>Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</td><td>{{ number_format($item->stok_produk) }}</td><td>
                <form class="inline-form" method="POST" action="{{ route('admin.inventory.update', $item->id_produk) }}">@csrf @method('PUT')
                    <input aria-label="Harga produk" type="number" name="harga_produk" min="0" step="0.01" value="{{ $item->harga_produk }}" required>
                    <input aria-label="Stok produk" type="number" name="stok_produk" min="0" value="{{ $item->stok_produk }}" required>
                    <button class="button button-small" type="submit" aria-label="Simpan harga dan stok">✎</button>
                </form>
            </td></tr>
        @endif
    @empty<tr><td colspan="4" class="empty">Belum ada produk.</td></tr>@endforelse
    </tbody></table></div><div class="pagination">{{ $produk->links() }}</div></section>
    <section class="panel inventory-table">
        <h3>Harga &amp; Stok Stroberi Segar per Grade (per kg)</h3>
        <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Grade</th><th>Harga/kg</th><th>Stok (kg)</th><th>Simpan</th></tr></thead><tbody>
        @forelse ($grades as $grade)
            <tr>
                <td>{{ $grade->nama_produk }}</td>
                <td><strong>Grade {{ $grade->grade }}</strong></td>
                <td colspan="3">
                    <form class="inline-form" method="POST" action="{{ route('admin.inventory.grades.update', $grade->id_produk_grade) }}">@csrf @method('PUT')
                        <input aria-label="Harga per kg Grade {{ $grade->grade }}" type="number" name="harga_per_kg" min="0" step="0.01" value="{{ $grade->harga_per_kg }}" required>
                        <input aria-label="Stok kg Grade {{ $grade->grade }}" type="number" name="stok_kg" min="0" step="0.001" value="{{ \App\Support\ProductCatalog::formatKilogramInput($grade->stok_kg) }}" required>
                        <button class="button button-small" type="submit">Simpan</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty">Varian grade belum tersedia. Tambahkan atau edit produk stroberi segar.</td></tr>
        @endforelse
        </tbody></table></div>
    </section>
@endsection
