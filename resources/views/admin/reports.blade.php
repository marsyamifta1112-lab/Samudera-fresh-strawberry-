@extends('admin.layout')
@section('title', 'Laporan Penjualan')
@section('content')
    @php
        $reportParams = array_filter([
            'dari' => $from,
            'sampai' => $to,
            'metode_pembayaran' => $filters['metode_pembayaran'] ?? null,
            'status_pesanan' => $filters['status_pesanan'] ?? null,
            'nominal_min' => $filters['nominal_min'] ?? null,
            'nominal_max' => $filters['nominal_max'] ?? null,
            'periode' => $period,
            'q' => $filters['q'] ?? null,
            'urutkan' => $filters['urutkan'] ?? null,
            'arah' => $filters['arah'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    @endphp

    <div class="report-page">
        <header class="report-hero">
            <div class="report-hero-copy">
                <span class="report-eyebrow"><i></i> RINGKASAN BISNIS</span>
                <h2>Laporan Penjualan</h2>
                <p>Pantau transaksi, pendapatan, dan produk favorit pelanggan dalam satu tampilan.</p>
                <div class="hero-period">
                    <span>Periode laporan</span>
                    <strong>{{ \Illuminate\Support\Carbon::parse($from)->format('d M Y') }} – {{ \Illuminate\Support\Carbon::parse($to)->format('d M Y') }}</strong>
                </div>
            </div>
            <div class="report-hero-actions no-print">
                <a class="report-action report-action-light" href="{{ route('admin.reports.export', $reportParams) }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M12 3v11m0 0 4-4m-4 4-4-4M5 17v3h14v-3"/></svg>
                    Unduh Excel
                </a>
                <a class="report-action report-action-berry" href="{{ route('admin.reports.print', $reportParams) }}">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M7 8V3h10v5M7 17H5a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M7 14h10v7H7z"/></svg>
                    Cetak Laporan
                </a>
            </div>
            <span class="hero-decoration hero-decoration-one" aria-hidden="true"></span>
            <span class="hero-decoration hero-decoration-two" aria-hidden="true"></span>
        </header>

        <section class="panel report-filter-panel no-print" aria-label="Filter laporan">
            <div class="filter-panel-heading">
                <div class="filter-title">
                    <span class="filter-title-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 5h16M7 12h10m-7 7h4"/></svg>
                    </span>
                    <div>
                        <h3>Atur laporan</h3>
                        <p>Pilih periode dan kriteria untuk menyesuaikan data.</p>
                    </div>
                </div>
                <div class="quick-ranges">
                    <span>Rentang cepat</span>
                    @foreach ($quickRanges as $key => $range)
                        @php
                            $quickParams = array_merge($reportParams, [
                                'dari' => $range['from'],
                                'sampai' => $range['to'],
                                'periode' => $key,
                            ]);
                        @endphp
                        <a class="quick-range" href="{{ route('admin.reports', $quickParams) }}">{{ $range['label'] }}</a>
                    @endforeach
                </div>
            </div>

            <form class="report-filter-form" method="GET" action="{{ route('admin.reports') }}">
                <label>Dari
                    <input type="date" name="dari" value="{{ $from }}">
                </label>
                <label>Sampai
                    <input type="date" name="sampai" value="{{ $to }}">
                </label>
                <label>Kelompok grafik
                    <select name="periode">
                        @foreach (['harian' => 'Harian', 'mingguan' => 'Mingguan', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $value => $label)
                            <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Metode pembayaran
                    <select name="metode_pembayaran">
                        <option value="">Semua metode</option>
                        @foreach ($filterOptions['metode_pembayaran'] as $metode)
                            <option value="{{ $metode }}" {{ ($filters['metode_pembayaran'] ?? '') === $metode ? 'selected' : '' }}>{{ $metode }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Status pesanan
                    <select name="status_pesanan">
                        <option value="">Semua status</option>
                        @foreach ($filterOptions['status_pesanan'] as $status)
                            <option value="{{ $status }}" {{ ($filters['status_pesanan'] ?? '') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Nominal minimum (Rp)
                    <input type="number" name="nominal_min" min="0" step="0.01" value="{{ $filters['nominal_min'] ?? '' }}" placeholder="0">
                </label>
                <label>Nominal maksimum (Rp)
                    <input type="number" name="nominal_max" min="0" step="0.01" value="{{ $filters['nominal_max'] ?? '' }}" placeholder="Tanpa batas">
                </label>
                <label class="search-filter">Cari pesanan / pelanggan / struk
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="ID, nama, atau nomor struk">
                </label>
                <label>Urutkan berdasarkan
                    <select name="urutkan">
                        @foreach (['tanggal' => 'Tanggal', 'pesanan' => 'Nomor pesanan', 'total' => 'Total pembayaran'] as $value => $label)
                            <option value="{{ $value }}" {{ ($filters['urutkan'] ?? 'tanggal') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Urutan
                    <select name="arah">
                        <option value="desc" {{ ($filters['arah'] ?? 'desc') === 'desc' ? 'selected' : '' }}>Terbaru / terbesar</option>
                        <option value="asc" {{ ($filters['arah'] ?? '') === 'asc' ? 'selected' : '' }}>Terlama / terkecil</option>
                    </select>
                </label>
                <div class="filter-actions">
                    <button class="button button-small" type="submit">Terapkan Filter</button>
                    <a class="button button-outline button-small" href="{{ route('admin.reports') }}">Reset</a>
                </div>
            </form>
        </section>

        <div class="report-period-label">
            <span class="period-mark" aria-hidden="true"></span>
            <strong>Data laporan</strong>
            <span class="period-separator">|</span>
            <span>{{ \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($to)->format('d/m/Y') }}</span>
            <span class="period-note">Pendapatan hanya menghitung pembayaran terverifikasi dan mengecualikan pesanan batal.</span>
        </div>

        <section class="cards report-cards" aria-label="Ringkasan laporan">
            <article class="card metric-card metric-transactions">
                <div class="metric-heading">
                    <div class="card-label">Transaksi terverifikasi</div>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M7 3h8l4 4v14H7z"/><path d="M15 3v5h5M10 13h6m-6 4h6"/></svg>
                    </span>
                </div>
                <div class="card-value green">{{ number_format($ringkasan['total_transaksi'], 0, ',', '.') }}</div>
                <div class="metric-footnote">Pembayaran berhasil diverifikasi</div>
            </article>
            <article class="card metric-card metric-revenue">
                <div class="metric-heading">
                    <div class="card-label">Total Pendapatan</div>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 2v20m5-16H9a3 3 0 0 0 0 6h6a3 3 0 0 1 0 6H6"/></svg>
                    </span>
                </div>
                <div class="card-value">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</div>
                <div class="metric-footnote">Dari transaksi terverifikasi</div>
            </article>
            <article class="card metric-card metric-orders">
                <div class="metric-heading">
                    <div class="card-label">Jumlah pesanan</div>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 4h2l2 12h10l2-8H7"/><circle cx="10" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                    </span>
                </div>
                <div class="card-value gold">{{ number_format($ringkasan['jumlah_pesanan'], 0, ',', '.') }}</div>
                <div class="metric-footnote">Pesanan dalam periode terpilih</div>
            </article>
            <article class="card metric-card metric-products">
                <div class="metric-heading">
                    <div class="card-label">Produk terjual</div>
                    <span class="metric-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="m12 3 8 4.5v9L12 21l-8-4.5v-9z"/><path d="m4.5 7.8 7.5 4.4 7.5-4.4M12 12.2V21"/></svg>
                    </span>
                </div>
                <div class="card-value green">{{ number_format($ringkasan['produk_terjual'], 0, ',', '.') }} <small>pcs</small></div>
                <div class="metric-footnote">Dari transaksi terverifikasi</div>
            </article>
        </section>

        @include('admin.partials.report-charts')

        <section class="panel report-table-panel">
            <div class="section-heading">
                <div>
                    <span class="table-eyebrow">RINCIAN PESANAN</span>
                    <h3>Detail Transaksi</h3>
                    <p>Menampilkan {{ $laporan->count() }} dari {{ $laporan->total() }} pesanan sesuai filter.</p>
                </div>
                <span class="table-count">{{ number_format($laporan->total(), 0, ',', '.') }} pesanan</span>
            </div>
            <div class="table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. transaksi</th>
                            <th>Pelanggan</th>
                            <th>Jumlah produk</th>
                            <th>Total pembayaran</th>
                            <th>Status pesanan</th>
                            <th>Status pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $row)
                            <tr>
                                <td>{{ $row->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($row->tanggal_pemesanan)->format('d/m/Y H:i') : '-' }}</td>
                                <td><span class="transaction-number">{{ $row->nomor_struk ?: '#'.$row->id_pesanan }}</span></td>
                                <td>{{ $row->nama_pelanggan ?: 'Pelanggan tidak tersedia' }}</td>
                                <td>{{ number_format($row->jumlah_produk, 0, ',', '.') }}</td>
                                <td>{{ $row->ongkir === null ? 'Menunggu ongkir' : 'Rp '.number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td><span class="status status-{{ $row->status_pesanan }}">{{ ucfirst($row->status_pesanan ?? '-') }}</span></td>
                                <td>{{ ucfirst($row->status_pembayaran ?? 'Belum dibayar') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="empty">Tidak ada pesanan pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $laporan->links() }}</div>
        </section>
    </div>

    <style>
        .report-page { display:grid; gap:0; }
        .report-hero { position:relative; isolation:isolate; display:flex; align-items:center; justify-content:space-between; gap:24px; min-height:186px; overflow:hidden; margin:0 0 18px; padding:27px 30px; border:1px solid #d5e5d9; border-radius:20px; background:linear-gradient(112deg,#123f2c 0%,#1d5b3b 58%,#286b47 100%); box-shadow:0 12px 28px rgba(20,73,47,.13); }
        .report-hero::before { position:absolute; z-index:-1; top:0; right:0; bottom:0; left:0; background:radial-gradient(circle at 82% 15%,rgba(255,255,255,.13),transparent 25%),radial-gradient(circle at 95% 100%,rgba(223,61,91,.28),transparent 34%); content:""; }
        .report-hero-copy { position:relative; z-index:1; }
        .report-eyebrow { display:inline-flex; align-items:center; gap:8px; color:#d5ead9; font-size:9px; font-weight:700; letter-spacing:1.6px; }
        .report-eyebrow i { width:7px; height:7px; border-radius:50%; background:#ee6680; box-shadow:0 0 0 4px rgba(238,102,128,.15); }
        .report-hero h2 { margin:12px 0 5px; color:#fff; font-size:27px; letter-spacing:-.4px; }
        .report-hero p { margin:0; color:#dcebe0; font-size:12px; line-height:1.5; }
        .hero-period { display:flex; align-items:center; gap:10px; margin-top:17px; color:#e7f2e9; font-size:10px; }
        .hero-period span { padding:5px 9px; border:1px solid #ffffff36; border-radius:999px; background:#ffffff12; }
        .hero-period strong { font-size:11px; }
        .report-hero-actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:9px; }
        .report-action { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:42px; padding:0 15px; border:1px solid transparent; border-radius:10px; font-size:11px; font-weight:700; transition:transform .18s ease,box-shadow .18s ease,background .18s ease; }
        .report-action:hover { transform:translateY(-2px); box-shadow:0 7px 16px #0e321f45; }
        .report-action svg { width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }
        .report-action-light { border-color:#ffffff75; background:#ffffffed; color:var(--forest); }
        .report-action-berry { background:var(--berry); color:#fff; }
        .hero-decoration { position:absolute; z-index:-1; display:block; border:1px solid #ffffff20; border-radius:50%; pointer-events:none; }
        .hero-decoration-one { top:-85px; right:13%; width:240px; height:240px; }
        .hero-decoration-two { right:7%; bottom:-116px; width:210px; height:210px; background:#ffffff08; }

        .report-filter-panel { margin-bottom:16px; padding:20px; border:1px solid #e6eadf; border-radius:17px; background:linear-gradient(135deg,#fff 60%,#f6faf5); box-shadow:0 7px 20px rgba(32,74,47,.045); }
        .filter-panel-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
        .filter-title { display:flex; align-items:center; gap:11px; }
        .filter-title-icon { display:grid; width:40px; height:40px; place-items:center; border-radius:12px; background:#e9f2eb; color:var(--forest); }
        .filter-title-icon svg { width:20px; height:20px; fill:none; stroke:currentColor; stroke-width:1.8; stroke-linecap:round; }
        .filter-title h3 { margin:0; color:var(--ink); font-size:14px; }
        .filter-title p { margin:4px 0 0; color:var(--muted); font-size:10px; }
        .quick-ranges { display:flex; flex-wrap:wrap; align-items:center; justify-content:flex-end; gap:7px; }
        .quick-ranges > span { margin-right:3px; color:var(--muted); font-size:10px; font-weight:600; }
        .quick-range { padding:8px 11px; border:1px solid #e5e9e1; border-radius:999px; background:#fff; color:#425449; font-size:10px; font-weight:600; transition:border-color .15s,background .15s,color .15s; }
        .quick-range:hover { border-color:#aac5ae; background:#edf5ee; color:var(--forest); }
        .report-filter-form { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px 13px; align-items:end; padding-top:17px; border-top:1px solid #edf0e9; }
        .report-filter-form label { display:grid; min-width:0; gap:7px; color:#4b554d; font-size:10px; font-weight:700; }
        .report-filter-form input,.report-filter-form select { width:100%; min-width:0; height:40px; padding:0 11px; border:1px solid #dfe5dc; border-radius:9px; outline:0; background:#fff; color:#303931; font-size:11px; transition:border-color .15s,box-shadow .15s; }
        .report-filter-form input:focus,.report-filter-form select:focus { border-color:#6f9c79; box-shadow:0 0 0 3px #e6f0e7; }
        .report-filter-form input::placeholder { color:#a0a69e; }
        .filter-actions { display:flex; align-items:center; gap:8px; min-height:40px; }
        .filter-actions .button { min-height:38px; padding:0 13px; border-radius:9px; font-size:10px; font-weight:700; }
        .filter-actions .button-outline { border-color:#e4c7cd; }

        .report-period-label { display:flex; flex-wrap:wrap; align-items:center; gap:8px; margin:0 2px 13px; color:#435849; font-size:10px; }
        .period-mark { width:8px; height:8px; border-radius:50%; background:var(--berry); box-shadow:0 0 0 4px #fbe8ec; }
        .report-period-label strong { color:var(--forest); font-size:11px; }
        .period-separator { color:#c0c8bd; }
        .period-note { margin-left:auto; color:var(--muted); font-size:9px; }
        .report-cards { grid-template-columns:repeat(4,minmax(0,1fr)); gap:13px; margin-bottom:20px; }
        .report-cards .metric-card { position:relative; min-width:0; min-height:142px; overflow:hidden; padding:17px 18px 15px; border:1px solid #e9ebe3; border-radius:15px; background:#fff; box-shadow:0 7px 18px rgba(30,65,43,.045); transition:transform .18s ease,box-shadow .18s ease; }
        .report-cards .metric-card:hover { transform:translateY(-3px); box-shadow:0 12px 22px rgba(30,65,43,.09); }
        .metric-card::before { position:absolute; top:0; right:0; left:0; height:3px; background:var(--metric-accent); content:""; }
        .metric-transactions { --metric-accent:#2c7650; --metric-tint:#eaf4ed; }
        .metric-revenue { --metric-accent:#df3d5b; --metric-tint:#fff0f2; }
        .metric-orders { --metric-accent:#d4a23d; --metric-tint:#fff7e5; }
        .metric-products { --metric-accent:#7c9a48; --metric-tint:#f1f5e8; }
        .metric-heading { display:flex; align-items:center; justify-content:space-between; gap:8px; }
        .report-cards .card-label { min-height:0; color:#70786f; font-size:10px; line-height:1.4; }
        .metric-icon { display:grid; flex:none; width:35px; height:35px; place-items:center; border-radius:11px; background:var(--metric-tint); color:var(--metric-accent); }
        .metric-icon svg { width:19px; height:19px; fill:none; stroke:currentColor; stroke-width:1.6; stroke-linecap:round; stroke-linejoin:round; }
        .report-cards .card-value { margin-top:13px; color:var(--metric-accent); font-size:23px; letter-spacing:-.35px; }
        .report-cards .card-value.green { color:#226343; }
        .report-cards .card-value.gold { color:#ba8626; }
        .report-cards .card-value small { font-size:10px; letter-spacing:0; }
        .metric-footnote { margin-top:5px; color:#9a9f96; font-size:9px; }

        .section-heading { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; }
        .section-heading h3 { margin:3px 0 0; color:var(--forest); font-size:15px; }
        .section-heading p { margin:5px 0 0; color:var(--muted); font-size:10px; }
        .table-eyebrow { color:#b64a5d; font-size:8px; font-weight:700; letter-spacing:1.2px; }
        .table-count { flex:none; padding:7px 11px; border:1px solid #e2ebe2; border-radius:999px; background:#f1f7f1; color:var(--forest); font-size:9px; font-weight:700; }
        .report-table-panel { margin-top:4px; padding:20px; border:1px solid #e9ebe3; border-radius:16px; box-shadow:0 7px 20px rgba(30,65,43,.04); }
        .report-table { min-width:920px; border-collapse:separate; border-spacing:0; }
        .report-table th { padding:12px 11px; border-bottom:1px solid #e4e9e1; background:#f3f7f2; color:#4f6253; font-size:9px; letter-spacing:.2px; white-space:nowrap; }
        .report-table th:first-child { border-radius:9px 0 0 0; }
        .report-table th:last-child { border-radius:0 9px 0 0; }
        .report-table td { padding:12px 11px; border-bottom:1px solid #f0f1ec; color:#50584f; font-size:10px; }
        .report-table tbody tr:hover td { background:#fbfcf9; }
        .transaction-number { color:var(--forest); font-weight:700; }
        .report-table .status { padding:5px 9px; border-radius:999px; font-size:9px; }
        .report-table .empty { padding:28px 12px; }
        .report-table-panel .pagination { color:var(--muted); }

        @media(max-width:1050px) {
            .report-hero { padding:24px; }
            .report-filter-form { grid-template-columns:repeat(3,minmax(0,1fr)); }
            .report-cards { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @media(max-width:760px) {
            .report-hero { align-items:flex-start; flex-direction:column; gap:18px; }
            .report-hero-actions { width:100%; }
            .filter-panel-heading { align-items:flex-start; flex-direction:column; }
            .quick-ranges { justify-content:flex-start; }
            .report-filter-form { grid-template-columns:repeat(2,minmax(0,1fr)); }
            .period-note { width:100%; margin:0 0 0 16px; }
        }
        @media(max-width:540px) {
            .report-hero { min-height:0; margin-bottom:13px; padding:21px 18px; border-radius:15px; }
            .report-hero h2 { margin-top:9px; font-size:22px; }
            .report-hero p { max-width:290px; font-size:11px; }
            .hero-period { flex-wrap:wrap; margin-top:13px; }
            .report-hero-actions { display:grid; grid-template-columns:1fr 1fr; gap:7px; }
            .report-action { min-height:40px; padding:0 8px; font-size:10px; }
            .report-filter-panel { padding:15px; border-radius:14px; }
            .filter-panel-heading { gap:13px; }
            .quick-ranges { gap:6px; }
            .quick-ranges > span { width:100%; }
            .quick-range { padding:7px 9px; font-size:9px; }
            .report-filter-form { grid-template-columns:1fr; gap:11px; }
            .report-filter-form input,.report-filter-form select { height:42px; }
            .filter-actions { padding-top:2px; }
            .report-period-label { align-items:flex-start; gap:7px; line-height:1.5; }
            .period-mark { margin-top:3px; }
            .period-note { margin-left:15px; }
            .report-cards { gap:9px; margin-bottom:16px; }
            .report-cards .metric-card { min-height:128px; padding:13px 12px 12px; border-radius:12px; }
            .report-cards .card-label { max-width:100px; font-size:9px; }
            .metric-icon { width:30px; height:30px; border-radius:9px; }
            .metric-icon svg { width:16px; height:16px; }
            .report-cards .card-value { margin-top:12px; font-size:19px; }
            .metric-footnote { font-size:8px; line-height:1.4; }
            .report-table-panel { padding:14px; border-radius:13px; }
            .section-heading h3 { font-size:13px; }
            .table-count { padding:6px 8px; font-size:8px; }
        }
    </style>
@endsection
