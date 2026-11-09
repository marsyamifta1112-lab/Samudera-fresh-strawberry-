@extends('kurir.layout')
@section('title', 'Data Pengiriman')
@section('content')
    <div class="page-heading"><div><h2>Data Pengiriman</h2><p>Daftar tugas pengiriman yang ditugaskan kepada Anda.</p></div></div>
    <section class="shipment-list">
        @forelse ($pengiriman as $item)
            <article class="shipment-row">
                <span class="status status-{{ $item->status_pengiriman }}">{{ strtoupper($item->status_pengiriman) }}</span>
                <div class="shipment-route">
                    <strong>{{ $item->nama_penerima ?: 'Penerima pesanan' }} · {{ $item->alamat_pengiriman }}</strong>
                    <small>Pesanan #SP{{ str_pad($item->id_pesanan, 6, '0', STR_PAD_LEFT) }} · {{ $item->no_telp_penerima ?: 'Nomor telepon tidak tersedia' }}</small>
                </div>
                <a class="button button-outline" href="{{ route('kurir.shipments.show', $item->id_pengiriman) }}">Lihat Detail</a>
            </article>
        @empty
            <div class="panel empty">Belum ada pengiriman yang ditugaskan kepada Anda.</div>
        @endforelse
    </section>
    <div class="pagination">{{ $pengiriman->links() }}</div>
@endsection
