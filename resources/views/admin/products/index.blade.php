@extends('admin.layout')
@section('title', 'Produk')
@section('content')
    <div class="page-heading">
        <div><h2>Kelola Produk</h2></div>
        <a class="button button-outline button-small" href="{{ route('admin.products.best-sellers') }}">Produk Terlaris</a>
    </div>
    <div class="toolbar">
        <form method="GET" action="{{ route('admin.products.index') }}"><input name="q" value="{{ request('q') }}" placeholder="Cari nama produk"><button class="button button-small" type="submit">Cari</button></form>
    </div>
    <section class="product-list">
        @forelse ($produk as $item)
            @php
                $namaProduk = strtolower($item->nama_produk);
                $kategori = str_contains($namaProduk, 'keripik') ? 'Keripik' : (str_contains($namaProduk, 'selai') ? 'Selai' : (str_contains($namaProduk, 'segar') ? 'Segar' : 'Olahan'));
                $isAvailable = (bool) ($item->is_available ?? true);
            @endphp
            <div class="product-row">
                <a class="product-edit-link" href="{{ route('admin.products.edit', $item->id_produk) }}">
                    @if ($item->foto_produk)<img class="product-thumb" src="{{ asset('images/'.$item->foto_produk) }}" alt="{{ $item->nama_produk }}">@else<img class="product-thumb" src="{{ asset('images/strawberry.jpeg') }}" alt="">@endif
                    <span class="product-info"><strong>{{ $item->nama_produk }}</strong><small>{{ $kategori }}{{ $item->berat_gram ? ' · '.$item->berat_gram.' gram' : '' }}</small></span>
                    <span class="row-action" aria-label="Edit produk">✎</span>
                </a>
                <form method="POST" action="{{ route('admin.products.toggle', $item->id_produk) }}">
                    @csrf
                    <button
                        type="submit"
                        class="availability-toggle {{ $isAvailable ? 'available' : 'sold' }}"
                        aria-label="Ubah ketersediaan {{ $item->nama_produk }}"
                    >{{ $isAvailable ? 'Tersedia' : 'Habis' }}</button>
                </form>
            </div>
        @empty
            <div class="panel empty">Produk tidak ditemukan.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $produk->links() }}</div>
    <a class="button row-add" href="{{ route('admin.products.create') }}" aria-label="Tambah produk">＋</a>
    <style>
        .product-edit-link { display:flex; flex:1; min-width:0; align-items:center; gap:10px; color:inherit; text-decoration:none; }
        .availability-toggle { min-width:75px; padding:7px 9px; border:0; border-radius:4px; font-size:9px; font-weight:700; cursor:pointer; }
        .availability-toggle.available { background:#e5f2e9; color:#245b3b; }
        .availability-toggle.sold { background:#fde3e8; color:#b42342; }
    </style>
@endsection
