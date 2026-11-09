@extends('admin.layout')
@section('title', 'Daftar Pesanan')
@section('content')
    <div class="page-heading"><div><h2>Daftar Pesanan</h2></div></div>
    <section class="order-list">
        @forelse ($pesanan as $order)
            <a class="order-row" href="{{ route('admin.orders.show', $order->id_pesanan) }}">
                <span class="status">{{ strtoupper($order->status_pesanan) }}</span>
                <span class="order-summary"><strong>{{ $order->nama_pelanggan }}</strong><small>{{ $order->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($order->tanggal_pemesanan)->format('d/m/Y') : '-' }} · {{ $order->wilayah_ongkir ?: 'Wilayah tidak tercatat' }} · {{ $order->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($order->total_harga, 0, ',', '.') }}</small></span>
                <span class="row-arrow">›</span>
            </a>
        @empty
            <div class="panel empty">Belum ada pesanan.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $pesanan->links() }}</div>
@endsection
