@extends('admin.layout')
@section('title', 'Harga & Stok')
@section('content')
    <div class="page-heading"><div><h2>Harga &amp; Stok</h2><p>Perbarui harga dan persediaan produk langsung dari daftar ini.</p></div></div>
    <section class="panel inventory-table"><div class="table-wrap"><table><thead><tr><th>Produk</th><th>Harga</th><th>Stok</th><th>Edit</th></tr></thead><tbody>
    @forelse ($produk as $item)
        <tr><td><strong>{{ $item->nama_produk }}</strong></td><td>Rp {{ number_format($item->harga_produk, 0, ',', '.') }}</td><td>{{ number_format($item->stok_produk) }}</td><td>
            <form class="inline-form" method="POST" action="{{ route('admin.inventory.update', $item->id_produk) }}">@csrf @method('PUT')
                <input aria-label="Harga produk" type="number" name="harga_produk" min="0" step="0.01" value="{{ $item->harga_produk }}" required>
                <input aria-label="Stok produk" type="number" name="stok_produk" min="0" value="{{ $item->stok_produk }}" required>
                <button class="button button-small" type="submit" aria-label="Simpan harga dan stok">✎</button>
            </form>
        </td></tr>
    @empty<tr><td colspan="4" class="empty">Belum ada produk.</td></tr>@endforelse
    </tbody></table></div><div class="pagination">{{ $produk->links() }}</div></section>
@endsection
