<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Laporan Penjualan - Samudera Fresh Strawberry</title>
    <style>
        :root { --forest:#174c32; --berry:#df3d5b; --ink:#242923; --muted:#77796f; --line:#e6e2d9; }
        * { box-sizing:border-box; }
        body { margin:0; color:var(--ink); background:#f7f6f1; font-family:Arial,sans-serif; }
        .print-page { width:min(1200px,96%); margin:22px auto; padding:24px; background:#fff; }
        .print-controls { display:flex; justify-content:space-between; margin-bottom:18px; }
        .print-controls a,.print-controls button { border:0; border-radius:5px; padding:9px 14px; background:var(--forest); color:#fff; font-size:12px; text-decoration:none; cursor:pointer; }
        .print-header { display:flex; align-items:center; gap:14px; padding-bottom:14px; border-bottom:2px solid var(--forest); }
        .print-header img { width:58px; height:58px; object-fit:contain; }
        .print-header h1 { margin:0; color:var(--forest); font-size:19px; }
        .print-header p { margin:4px 0 0; color:var(--muted); font-size:10px; }
        .report-title { margin:15px 0 4px; color:var(--ink); font-size:16px; }
        .report-period { margin:0 0 12px; color:var(--muted); font-size:10px; }
        .cards { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:8px; margin-bottom:10px; }
        .card { padding:10px; border:1px solid var(--line); border-radius:8px; }
        .card-label { min-height:20px; color:var(--muted); font-size:9px; }
        .card-value { margin-top:5px; color:var(--berry); font-size:14px; font-weight:bold; }
        .card-value.green { color:var(--forest); }
        .card-value.gold { color:#b28621; }
        .print-filter-summary { margin:0 0 10px; color:var(--muted); font-size:9px; }
        .print-table-section { margin-top:10px; }
        .print-table-section h2 { margin:0 0 7px; color:var(--forest); font-size:12px; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; min-width:900px; border-collapse:collapse; table-layout:fixed; }
        th,td { padding:6px 5px; border:1px solid var(--line); text-align:left; font-size:8px; overflow-wrap:anywhere; }
        th { background:#edf3ee; color:var(--forest); font-weight:bold; }
        .status { font-weight:bold; }
        .empty { color:var(--muted); text-align:center; }
        @media(max-width:700px) {
            .print-page { width:100%; margin:0; padding:14px; }
            .cards { grid-template-columns:repeat(2,minmax(0,1fr)); }
        }
        @page { size:A4 landscape; margin:10mm; }
        @media print {
            html,body { width:100%; background:#fff; }
            body { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
            .print-page { width:100%; max-width:none; margin:0; padding:0; }
            .no-print { display:none !important; }
            .print-header,.card { break-inside:avoid; }
            .table-wrap { overflow:visible; }
            table { width:100%; min-width:0; }
            thead { display:table-header-group; }
            tr { break-inside:avoid; }
            th,td { padding:4px 3px; font-size:7pt; }
        }
    </style>
</head>
<body>
    @php
        $filterLabels = [
            'metode_pembayaran' => 'Metode: '.($filters['metode_pembayaran'] ?? 'Semua'),
            'status_pesanan' => 'Status: '.($filters['status_pesanan'] ?? 'Semua'),
            'nominal_min' => isset($filters['nominal_min']) ? 'Minimum: Rp '.number_format($filters['nominal_min'], 0, ',', '.') : null,
            'nominal_max' => isset($filters['nominal_max']) ? 'Maksimum: Rp '.number_format($filters['nominal_max'], 0, ',', '.') : null,
            'q' => filled($filters['q'] ?? null) ? 'Pencarian: '.$filters['q'] : null,
        ];
    @endphp
    <main class="print-page">
        <div class="print-controls no-print">
            <a href="{{ route('admin.reports', request()->query()) }}">Kembali ke Laporan</a>
            <button type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        </div>
        <header class="print-header">
            <img src="{{ asset('images/logo samudera.jpeg') }}" alt="Logo Samudera Fresh">
            <div>
                <h1>Samudera Fresh Strawberry</h1>
                <p>Laporan penjualan dan transaksi</p>
            </div>
        </header>
        <h2 class="report-title">Laporan Penjualan</h2>
        <p class="report-period">
            Periode {{ \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') }} - {{ \Illuminate\Support\Carbon::parse($to)->format('d/m/Y') }}
            · Dicetak {{ now()->format('d/m/Y H:i') }}
        </p>
        <p class="print-filter-summary">{{ implode(' · ', array_filter($filterLabels)) }} · Pendapatan mencakup pembayaran terverifikasi dan mengecualikan pesanan batal.</p>

        <section class="cards" aria-label="Ringkasan laporan">
            <article class="card"><div class="card-label">Total transaksi terverifikasi</div><div class="card-value green">{{ number_format($ringkasan['total_transaksi'], 0, ',', '.') }}</div></article>
            <article class="card"><div class="card-label">Total pendapatan</div><div class="card-value">Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</div></article>
            <article class="card"><div class="card-label">Jumlah pesanan</div><div class="card-value gold">{{ number_format($ringkasan['jumlah_pesanan'], 0, ',', '.') }}</div></article>
            <article class="card"><div class="card-label">Produk terjual dari transaksi terverifikasi</div><div class="card-value green">{{ number_format($ringkasan['produk_terjual'], 0, ',', '.') }} pcs</div></article>
        </section>

        @include('admin.partials.report-charts')

        <section class="print-table-section">
            <h2>Detail Transaksi ({{ $laporan->count() }} pesanan)</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. transaksi</th>
                            <th>Pelanggan</th>
                            <th>Jumlah produk</th>
                            <th>Total pembayaran</th>
                            <th>Status pesanan</th>
                            <th>Metode</th>
                            <th>Status pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $row)
                            <tr>
                                <td>{{ $row->tanggal_pemesanan ? \Illuminate\Support\Carbon::parse($row->tanggal_pemesanan)->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $row->nomor_struk ?: '#'.$row->id_pesanan }}</td>
                                <td>{{ $row->nama_pelanggan ?: 'Pelanggan tidak tersedia' }}</td>
                                <td>{{ number_format($row->jumlah_produk, 0, ',', '.') }}</td>
                                <td>{{ $row->ongkir === null ? 'Menunggu ongkir' : 'Rp '.number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($row->status_pesanan ?? '-') }}</td>
                                <td>{{ $row->metode_pembayaran ?: '-' }}</td>
                                <td>{{ ucfirst($row->status_pembayaran ?? 'Belum dibayar') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="empty">Tidak ada pesanan pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
