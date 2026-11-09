@extends('admin.layout')
@section('title', 'Produk Terlaris')
@section('content')
    <div class="page-heading">
        <div><h2>Produk Terlaris</h2></div>
        <a class="button button-outline button-small" href="{{ route('admin.products.index') }}">Kelola Produk</a>
    </div>
    <section class="product-list">
        @forelse ($produkTerlaris as $item)
            <a class="product-row" href="{{ route('admin.products.edit', $item->id_produk) }}">
                @if ($item->foto_produk)<img class="product-thumb" src="{{ asset('images/'.$item->foto_produk) }}" alt="{{ $item->nama_produk }}">@else<img class="product-thumb" src="{{ asset('images/strawberry.jpeg') }}" alt="">@endif
                <span class="product-info"><strong>{{ $item->nama_produk }}</strong><small>Produk terlaris</small></span>
                <span class="row-action">{{ number_format($item->total_terjual) }} pcs terjual</span>
            </a>
        @empty
            <div class="panel empty">Belum ada data penjualan produk.</div>
        @endforelse
    </section>
@endsection
