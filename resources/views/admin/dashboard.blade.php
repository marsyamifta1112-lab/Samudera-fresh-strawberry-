@extends('admin.layout')
@section('title', 'Dashboard Admin')
@section('content')
    <div class="page-heading"><div><h2>Dashboard Admin</h2></div></div>
    <section class="cards">
        <article class="card"><div class="card-label">Total Produk</div><div class="card-value">{{ number_format($ringkasan['produk']) }}</div></article>
        <article class="card"><div class="card-label">Total Pesanan</div><div class="card-value green">{{ number_format($ringkasan['pesanan']) }}</div></article>
        <article class="card"><div class="card-label">Total Penjualan</div><div class="card-value">Rp{{ number_format($ringkasan['penjualan'], 0, ',', '.') }}</div></article>
        <article class="card"><div class="card-label">Pesanan Baru</div><div class="card-value gold">{{ number_format($ringkasan['menunggu']) }}</div></article>
    </section>
    <section class="chart-layout">
        <article class="panel">
            <h3>Grafik Penjualan</h3>
            @php
                $nilaiGrafik = $grafik->pluck('total')->all();
                $nilaiMax = max(1, max($nilaiGrafik));
                $titikGrafik = collect($nilaiGrafik)->map(fn ($nilai, $i) => (int) ($i * 100 / max(1, count($nilaiGrafik) - 1)).','.((int) (120 - ($nilai / $nilaiMax * 100))))->implode(' ');
            @endphp
            <div class="chart-area">
                <svg viewBox="0 0 100 130" preserveAspectRatio="none" role="img" aria-label="Grafik penjualan beberapa hari terakhir">
                    <line x1="0" y1="20" x2="100" y2="20"></line><line x1="0" y1="60" x2="100" y2="60"></line><line x1="0" y1="100" x2="100" y2="100"></line>
                    <polyline points="{{ $titikGrafik }}"></polyline>
                    @foreach ($nilaiGrafik as $i => $nilai)
                        <circle cx="{{ (int) ($i * 100 / max(1, count($nilaiGrafik) - 1)) }}" cy="{{ (int) (120 - ($nilai / $nilaiMax * 100)) }}" r="1.7"></circle>
                    @endforeach
                </svg>
                <div class="chart-labels">@foreach ($grafik as $hari)<span>{{ $hari->label }}</span>@endforeach</div>
            </div>
        </article>
        <article class="panel">
            <h3>Pesanan Baru</h3>
            <div class="new-orders">
                @forelse ($pesananTerbaru->take(3) as $order)
                    <a href="{{ route('admin.orders.show', $order->id_pesanan) }}"><span>#{{ $order->id_pesanan }} · {{ $order->wilayah_ongkir ?: 'Wilayah tidak tercatat' }}</span><span>{{ $order->ongkir === null ? 'Menunggu ongkir' : 'Rp'.number_format($order->total_harga, 0, ',', '.') }}</span></a>
                @empty
                    <p class="empty">Belum ada pesanan baru.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection
