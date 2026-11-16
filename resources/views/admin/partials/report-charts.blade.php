@php
    $maxPendapatan = max(1, (float) $grafikPeriode->max('pendapatan'));
    $maxPesanan = max(1, (int) $grafikPeriode->max('pesanan'));
    $maxProduk = max(1, (int) $produkTerlaris->max('jumlah_terjual'));
@endphp
<section class="report-charts-section" aria-label="Grafik laporan">
    <div class="report-charts-heading">
        <div>
            <h3>Visualisasi Penjualan</h3>
            <p>Perbandingan pendapatan, pesanan, dan produk terlaris sesuai filter.</p>
        </div>
        <span class="chart-period-pill">{{ ucfirst($period) }}</span>
    </div>
    <div class="report-chart-grid">
    <article class="panel report-chart-panel">
        <div class="chart-heading">
            <div>
                <h3>Pendapatan {{ ucfirst($period) }}</h3>
                <p>Pembayaran terverifikasi; pesanan batal tidak dihitung.</p>
            </div>
            <span class="chart-legend"><i class="legend-dot revenue-dot"></i> Pendapatan (Rp)</span>
        </div>
        <div class="report-plot" role="list" aria-label="Grafik pendapatan">
            @forelse ($grafikPeriode as $point)
                <div class="report-plot-row" role="listitem" title="{{ $point->label }}: Rp {{ number_format($point->pendapatan, 0, ',', '.') }}">
                    <span class="plot-label">{{ $point->label }}</span>
                    <span class="plot-track"><span class="plot-fill revenue-fill" style="width:{{ $point->pendapatan > 0 ? min(100, max(2, ($point->pendapatan / $maxPendapatan) * 100)) : 0 }}%"></span></span>
                    <strong class="plot-value">Rp {{ number_format($point->pendapatan, 0, ',', '.') }}</strong>
                </div>
            @empty
                <p class="empty">Belum ada data pendapatan pada periode ini.</p>
            @endforelse
        </div>
    </article>

    <article class="panel report-chart-panel">
        <div class="chart-heading">
            <div>
                <h3>Jumlah Pesanan {{ ucfirst($period) }}</h3>
                <p>Jumlah pesanan sesuai rentang dan filter laporan.</p>
            </div>
            <span class="chart-legend"><i class="legend-dot order-dot"></i> Pesanan</span>
        </div>
        <div class="report-plot" role="list" aria-label="Grafik jumlah pesanan">
            @forelse ($grafikPeriode as $point)
                <div class="report-plot-row" role="listitem" title="{{ $point->label }}: {{ number_format($point->pesanan, 0, ',', '.') }} pesanan">
                    <span class="plot-label">{{ $point->label }}</span>
                    <span class="plot-track"><span class="plot-fill order-fill" style="width:{{ $point->pesanan > 0 ? min(100, max(2, ($point->pesanan / $maxPesanan) * 100)) : 0 }}%"></span></span>
                    <strong class="plot-value">{{ number_format($point->pesanan, 0, ',', '.') }} pesanan</strong>
                </div>
            @empty
                <p class="empty">Belum ada pesanan pada periode ini.</p>
            @endforelse
        </div>
    </article>

    <article class="panel report-chart-panel top-products-chart">
        <div class="chart-heading">
            <div>
                <h3>Produk Terlaris</h3>
                <p>Jumlah produk dari transaksi terverifikasi dan bukan pesanan batal.</p>
            </div>
            <span class="chart-legend"><i class="legend-dot product-dot"></i> Produk terjual</span>
        </div>
        <div class="report-plot product-plot" role="list" aria-label="Grafik produk terlaris">
            @forelse ($produkTerlaris as $produk)
                <div class="report-plot-row product-plot-row" role="listitem" title="{{ $produk->nama_produk }}: {{ number_format($produk->jumlah_terjual, 0, ',', '.') }} pcs">
                    <span class="plot-label product-name">{{ $produk->nama_produk }}</span>
                    <span class="plot-track"><span class="plot-fill product-fill" style="width:{{ $produk->jumlah_terjual > 0 ? min(100, max(2, ($produk->jumlah_terjual / $maxProduk) * 100)) : 0 }}%"></span></span>
                    <strong class="plot-value">{{ number_format($produk->jumlah_terjual, 0, ',', '.') }} pcs</strong>
                </div>
            @empty
                <p class="empty">Belum ada produk terjual pada periode ini.</p>
            @endforelse
        </div>
    </article>
    </div>
</section>
<style>
    .report-charts-section { margin:4px 0 20px; }
    .report-charts-heading { display:flex; justify-content:space-between; align-items:center; gap:12px; margin:0 2px 12px; }
    .report-charts-heading h3 { margin:0; color:var(--forest); font-size:16px; letter-spacing:-.2px; }
    .report-charts-heading p { margin:5px 0 0; color:var(--muted); font-size:10px; line-height:1.5; }
    .chart-period-pill { flex:none; padding:7px 12px; border:1px solid #dfe9df; border-radius:999px; background:#edf5ee; color:var(--forest); font-size:9px; font-weight:700; }
    .report-chart-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .report-chart-panel { min-width:0; margin-bottom:0; padding:18px; border:1px solid #e8ebe4; border-top:3px solid #df3d5b; border-radius:15px; background:linear-gradient(145deg,#fff 60%,#fffafb); box-shadow:0 8px 20px rgba(23,76,50,.055); }
    .report-chart-panel:nth-child(2) { border-top-color:#2d7650; background:linear-gradient(145deg,#fff 60%,#f8fcf8); }
    .report-chart-panel:nth-child(3) { border-top-color:#88a54e; background:linear-gradient(145deg,#fff 60%,#fbfcf6); }
    .top-products-chart { grid-column:1/-1; }
    .chart-heading { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; min-height:48px; margin-bottom:13px; padding-bottom:12px; border-bottom:1px solid #edf0e9; }
    .chart-heading h3 { margin:0 0 5px; color:var(--forest); font-size:13px; }
    .chart-heading p { margin:0; color:var(--muted); font-size:10px; line-height:1.5; }
    .chart-legend { display:flex; align-items:center; gap:6px; flex:none; padding:6px 9px; border:1px solid #edf0e9; border-radius:999px; background:#fff; color:#737a71; font-size:9px; white-space:nowrap; }
    .legend-dot { display:inline-block; width:8px; height:8px; border-radius:50%; }
    .revenue-dot { background:var(--berry); box-shadow:0 0 0 3px #fbe8ec; }
    .order-dot { background:var(--forest); box-shadow:0 0 0 3px #e8f1e9; }
    .product-dot { background:#7b9d47; box-shadow:0 0 0 3px #eff4e7; }
    .revenue-fill { background:linear-gradient(90deg,#df3d5b,#f17d92); }
    .order-fill { background:linear-gradient(90deg,#174c32,#57916a); }
    .product-fill { background:linear-gradient(90deg,#6e913e,#a5c66f); }
    .report-plot { display:grid; gap:5px; max-height:310px; overflow:auto; overscroll-behavior:contain; padding:2px 6px 2px 0; scrollbar-color:#c3d2c4 transparent; scrollbar-width:thin; }
    .report-plot::-webkit-scrollbar { width:6px; height:6px; }
    .report-plot::-webkit-scrollbar-thumb { border-radius:8px; background:#c3d2c4; }
    .report-plot-row { display:grid; grid-template-columns:minmax(54px,76px) minmax(0,1fr) minmax(110px,auto); gap:10px; align-items:center; min-height:29px; padding:4px 7px; border-radius:8px; transition:background .15s ease; }
    .report-plot-row:hover { background:#f6f8f4; }
    .plot-label { overflow:hidden; color:#717970; font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
    .plot-track { display:block; height:10px; overflow:hidden; border-radius:10px; background:repeating-linear-gradient(90deg,#f1f3ef 0,#f1f3ef calc(25% - 1px),#dfe5df calc(25% - 1px),#dfe5df 25%); }
    .plot-fill { display:block; height:100%; min-width:0; border-radius:inherit; }
    .plot-value { color:#354238; font-size:10px; font-weight:700; text-align:right; white-space:nowrap; }
    .product-plot { grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px 14px; max-height:none; }
    .product-plot-row { grid-template-columns:minmax(90px,150px) minmax(0,1fr) minmax(48px,auto); min-height:38px; padding:8px 10px; border:1px solid #e9eee5; background:#fff; }
    .product-plot-row:hover { border-color:#d6e2d5; background:#f9fbf7; }
    .product-name { overflow:hidden; color:#3e5143; font-weight:600; text-overflow:ellipsis; }
    .report-plot .empty { grid-column:1/-1; margin:0; padding:22px 10px; border:1px dashed #dfe7dd; border-radius:10px; background:#fbfcfa; font-size:10px; }
    @media(max-width:900px) { .report-chart-grid { grid-template-columns:1fr; } .top-products-chart { grid-column:auto; } }
    @media(max-width:620px) {
        .report-charts-heading { align-items:flex-start; }
        .report-charts-heading h3 { font-size:14px; }
        .report-chart-grid { gap:10px; }
        .report-chart-panel { padding:14px; border-radius:13px; }
        .chart-heading { gap:8px; }
        .chart-legend { padding:5px 7px; font-size:8px; }
        .report-plot-row { grid-template-columns:minmax(44px,58px) minmax(0,1fr) minmax(96px,auto); gap:7px; }
        .product-plot { grid-template-columns:1fr; }
        .product-plot-row { grid-template-columns:minmax(85px,125px) minmax(0,1fr) minmax(48px,auto); }
        .plot-value { font-size:9px; }
    }
    @media print {
        .report-charts-section { margin:8px 0; }
        .report-charts-heading { margin-bottom:6px; }
        .report-charts-heading h3 { font-size:11pt; }
        .report-chart-grid { grid-template-columns:1fr 1fr; gap:8px; }
        .top-products-chart { grid-column:1/-1; }
        .report-chart-panel { padding:9px; border-radius:5px; box-shadow:none; }
        .chart-heading { min-height:0; margin-bottom:7px; }
        .chart-heading h3 { font-size:9pt; }
        .chart-heading p,.chart-legend,.plot-label,.plot-value { font-size:7pt; }
        .report-plot { max-height:none; overflow:visible; gap:3px; }
        .report-plot-row { gap:5px; grid-template-columns:48px minmax(30px,1fr) minmax(84px,auto); min-height:0; padding:2px 3px; }
        .product-plot { grid-template-columns:repeat(2,minmax(0,1fr)); gap:3px 8px; }
        .product-plot-row { grid-template-columns:minmax(75px,120px) minmax(30px,1fr) minmax(45px,auto); padding:3px; }
    }
</style>
